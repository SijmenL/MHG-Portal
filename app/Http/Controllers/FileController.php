<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use ZipArchive;

class FileController extends Controller
{
    /**
     * Display a listing of the files.
     */
    public function index($locationId, $location, $folderId)
    {
        $files = File::where('location', $location)
            ->where('location_id', $locationId)
            ->where('folder_id', $folderId)
            ->get();

        $files = $files->sort(function ($a, $b) {
            if ($a->type != $b->type) {
                return $b->type <=> $a->type; // folders (2) before URLs (1)
            }

            return strcasecmp($a->file_name, $b->file_name);
        })->values();

        $breadcrumbs = $this->generateBreadcrumbs($locationId, $location, $folderId);

        return [
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    /**
     * Generate breadcrumbs based on folder hierarchy.
     */
    private function generateBreadcrumbs($locationId, $location, $folderId)
    {
//        $breadcrumbs = [];
//        // Base breadcrumbs
//        $routes = [
//            'Lesson' => 'lessons.environment.lesson.files',
//            'Archive' => 'archive',
//            'Admin' => 'admin.files',
//            'Dolfijnen' => 'dolfijnen.files',
//            'Zeeverkenners' => 'zeeverkenners.files',
//            'Loodsen' => 'loodsen.files',
//            'Afterloodsen' => 'afterloodsen.files',
//            'Leiding' => 'leiding.files',
//        ];
//
//        if (array_key_exists($location, $routes)) {
//            $params = ['folder' => null];
//            if ($location === "Lesson") $params['lessonId'] = $locationId;
//            $breadcrumbs[] = ['name' => 'Bestanden', 'url' => route($routes[$location], $params)];
//        }
//
//        $currentFolder = $folderId ? File::find($folderId) : null;
//        $trail = [];
//
//        while ($currentFolder) {
//            if (array_key_exists($location, $routes)) {
//                $params = ['folder' => $currentFolder->id];
//                if ($location === "Lesson") $params['lessonId'] = $locationId;
//
//                $trail[] = [
//                    'name' => $currentFolder->file_name,
//                    'url' => route($routes[$location], $params),
//                ];
//            }
//            $currentFolder = $currentFolder->parent;
//        }
//
//        $trail = array_reverse($trail);
//        return array_merge($breadcrumbs, $trail);
    }

    /**
     * Centralized Batch Action processor for Move, Copy, Delete, and Rename
     */
    public function batchAction(Request $request, $location, $locationId)
    {
        $action = $request->input('action'); // rename, move, copy, delete
        $fileIds = $request->input('file_ids', []);
        $targetFolderId = $request->input('target_folder_id'); // Can be null (root folder)
        $newName = $request->input('new_name');

        if (empty($fileIds)) {
            return response()->json(['error' => 'Geen bestanden geselecteerd.'], 400);
        }

        try {
            foreach ($fileIds as $id) {
                $file = File::findOrFail($id);

                // Check specific lesson permissions if applicable
                if ($location === "Lesson" && $file->user_id !== Auth::id()) {
                    if (!$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                        continue; // Skip file if user lacks permission
                    }
                }

                switch ($action) {
                    case 'rename':
                        $file->update(['file_name' => $newName]);
                        break;

                    case 'move':
                        // Prevent moving a folder into itself or its own subdirectories
                        if ($file->type == 2 && $this->isDescendantFolder($file, $targetFolderId)) {
                            return response()->json(['error' => 'Kan een map niet naar zichzelf of een onderliggende map verplaatsen.'], 400);
                        }
                        $file->update(['folder_id' => $targetFolderId]);
                        break;

                    case 'copy':
                        $this->copyFileRecursive($file, $targetFolderId, $location, $locationId);
                        break;

                    case 'delete':
                        $this->deleteFolderContents($file);
                        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                            Storage::disk('public')->delete($file->file_path);
                        }

                        $log = new Log();
                        $log->createLog(Auth::id(), 2, 'Delete file (Batch)', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Bestand verwijderd: '.$file->file_name);
                        $file->delete();
                        break;
                }
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Batch Action Error: ' . $e->getMessage());
            return response()->json(['error' => 'Er is een interne serverfout opgetreden: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helps prevent infinitely recursive folder moves
     */
    private function isDescendantFolder($folder, $targetId) {
        if ($targetId == $folder->id) return true;

        $target = File::find($targetId);
        while ($target && $target->folder_id) {
            if ($target->folder_id == $folder->id) return true;
            $target = File::find($target->folder_id);
        }
        return false;
    }

    /**
     * Recursively duplicates a file/folder and physical storage objects
     */
    private function copyFileRecursive($file, $targetFolderId, $location, $locationId) {
        $newFile = $file->replicate();
        $newFile->folder_id = $targetFolderId;
        $newFile->user_id = Auth::id(); // Copied by current user

        // If it's a physical file, duplicate it on the disk
        if ($file->type == 0 && !empty($file->file_path) && Storage::disk('public')->exists($file->file_path)) {
            $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
            $newPath = $location . '/' . $locationId . '/' . Str::random(40) . ($extension ? '.' . $extension : '');

            Storage::disk('public')->copy($file->file_path, $newPath);
            $newFile->file_path = $newPath;
        }

        $newFile->save();

        // If it's a folder, recursively copy its children
        if ($file->type == 2) {
            $children = File::where('folder_id', $file->id)->get();
            foreach ($children as $child) {
                $this->copyFileRecursive($child, $newFile->id, $location, $locationId);
            }
        }
    }

    /**
     * Store a newly created file, folder or hyperlink in storage.
     */
    public function filesStore(Request $request, $location, $locationId)
    {
        try {
            $request->validate([
                'type' => 'required',
                'folder_id' => 'nullable|exists:files,id',
                'access' => 'required|in:all,teachers',
            ]);

            $folderId = $request->input('folder_id');

            switch ($request->input('type')) {
                case '0':
                    $request->validate(['file' => 'required|array', 'file.*' => 'file']);

                    foreach ($request->file('file') as $uploadedFile) {
                        $filePath = Storage::disk('public')->putFile($location . '/' . $locationId, $uploadedFile);
                        $newFile = new File();
                        $newFile->location = $location;
                        $newFile->location_id = $locationId;
                        $newFile->lesson_id = $location === 'Lesson' ? $locationId : null;
                        $newFile->folder_id = $folderId;
                        $newFile->file_name = $uploadedFile->getClientOriginalName();
                        $newFile->file_path = $filePath;
                        $newFile->type = 0;
                        $newFile->user_id = Auth::id();
                        $newFile->access = $request->input('access');
                        $newFile->save();
                    }

                    if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
                    return redirect()->back()->with('success', 'Bestanden geüpload.');

                case '1':
                    $request->validate(['file' => 'required|url', 'title' => 'required']);
                    $newFile = new File();
                    $newFile->location = $location;
                    $newFile->location_id = $locationId;
                    $newFile->lesson_id = $location === 'Lesson' ? $locationId : null;
                    $newFile->folder_id = $folderId;
                    $newFile->file_name = $request->input('title');
                    $newFile->file_path = $request->input('file');
                    $newFile->user_id = Auth::id();
                    $newFile->type = 1;
                    $newFile->access = $request->input('access');
                    $newFile->save();
                    return redirect()->back()->with('success', 'Hyperlink toegevoegd.');

                case '2':
                    $request->validate(['title' => 'required']);
                    $newFile = new File();
                    $newFile->location = $location;
                    $newFile->location_id = $locationId;
                    $newFile->lesson_id = $location === 'Lesson' ? $locationId : null;
                    $newFile->folder_id = $folderId;
                    $newFile->file_name = $request->input('title');
                    $newFile->file_path = "";
                    $newFile->user_id = Auth::id();
                    $newFile->type = 2;
                    $newFile->access = $request->input('access');
                    $newFile->save();
                    return redirect()->back()->with('success', 'Map aangemaakt.');

                case '3':
                    $request->validate([
                        'folder_upload' => 'required|array',
                        'folder_upload.*' => 'file',
                        'folder_paths' => 'required|string',
                    ]);
                    $folderPaths = json_decode($request->input('folder_paths'), true);
                    if (empty($folderPaths)) {
                        return response()->json(['error' => 'Geen bestanden gevonden in de map.'], 422);
                    }
                    $baseFolderName = explode('/', $folderPaths[0])[0];
                    $parentFolder = File::where('folder_id', $folderId)
                        ->where('file_name', $baseFolderName)
                        ->where('type', 2)
                        ->first();
                    if (!$parentFolder) {
                        $parentFolder = new File();
                        $parentFolder->location = $location;
                        $parentFolder->location_id = $locationId;
                        $parentFolder->lesson_id = $location === 'Lesson' ? $locationId : null;
                        $parentFolder->folder_id = $folderId;
                        $parentFolder->file_name = $baseFolderName;
                        $parentFolder->file_path = "";
                        $parentFolder->user_id = Auth::id();
                        $parentFolder->type = 2;
                        $parentFolder->access = $request->input('access');
                        $parentFolder->save();
                    }

                    foreach ($request->file('folder_upload') as $key => $uploadedFile) {
                        $path = $folderPaths[$key];
                        $parts = explode('/', $path);
                        array_shift($parts);
                        $currentParent = $parentFolder;
                        for ($i = 0; $i < count($parts) - 1; $i++) {
                            $folderName = $parts[$i];
                            $existingFolder = $currentParent->children()->where('file_name', $folderName)->first();
                            if (!$existingFolder) {
                                $newFolder = new File();
                                $newFolder->location = $location;
                                $newFolder->location_id = $locationId;
                                $newFolder->lesson_id = $location === 'Lesson' ? $locationId : null;
                                $newFolder->folder_id = $currentParent->id;
                                $newFolder->file_name = $folderName;
                                $newFolder->file_path = Storage::disk('public')->putFile($location . '/' . $locationId, $uploadedFile);
                                $newFolder->user_id = Auth::id();
                                $newFolder->type = 2;
                                $newFolder->access = $request->input('access');
                                $newFolder->save();
                                $currentParent = $newFolder;
                            } else {
                                $currentParent = $existingFolder;
                            }
                        }
                        $filePath = Storage::disk('public')->putFile($location . '/' . $locationId, $uploadedFile);
                        $newFile = new File();
                        $newFile->location = $location;
                        $newFile->location_id = $locationId;
                        $newFile->lesson_id = $location === 'Lesson' ? $locationId : null;
                        $newFile->folder_id = $currentParent->id;
                        $newFile->file_name = $uploadedFile->getClientOriginalName();
                        $newFile->file_path = $filePath;
                        $newFile->user_id = Auth::id();
                        $newFile->type = 0;
                        $newFile->access = $request->input('access');
                        $newFile->save();
                    }

                    if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
                    return redirect()->back()->with('success', 'Mappen geüpload.');
            }
        } catch (\Exception $e) {
            \Log::error('Error in filesStore: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) return response()->json(['error' => 'Database fout: ' . $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Toevoegen mislukt: ' . $e->getMessage());
        }
    }

    public function downloadFile(Request $request, File $file)
    {
        $filePath = Storage::disk('public')->path($file->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Bestand niet gevonden.');
        }

        $storedFileName = $file->file_name;

        try {
            $lastDotPosition = strrpos($storedFileName, '.');
            $extension = ($lastDotPosition === false) ? '' : substr($storedFileName, $lastDotPosition + 1);

            $lastUnderscorePosition = strrpos($storedFileName, '_');
            $baseNameWithoutLastUnderscore = $lastUnderscorePosition !== false ? substr($storedFileName, 0, $lastUnderscorePosition) : $storedFileName;

            $secondToLastUnderscorePosition = strrpos($baseNameWithoutLastUnderscore, '_');

            if ($secondToLastUnderscorePosition !== false) {
                $cleanBaseName = substr($baseNameWithoutLastUnderscore, 0, $secondToLastUnderscorePosition);
            } else {
                $cleanBaseName = $baseNameWithoutLastUnderscore;
            }

            $downloadName = $cleanBaseName;
            if (!empty($extension)) {
                $downloadName .= '.' . $extension;
            }

            return Response::download($filePath, $downloadName);
        } catch (\Exception $e) {
            return Response::download($filePath, $file->file_name);
        }
    }

    public function downloadFolder(Request $request, File $folder)
    {
        try {
            if ($folder->type !== 2) {
                return redirect()->back()->with('error', 'Dit is geen map.');
            }

            $zipFileName = $folder->file_name . '.zip';
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $this->zipDirectory($zip, $folder);
                $zip->close();
            } else {
                throw new \Exception('Kon het ZIP-bestand niet aanmaken.');
            }

            return Response::download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kon map niet downloaden, waarschijnlijk is deze leeg.');
        }
    }

    private function zipDirectory(ZipArchive $zip, File $folder, $basePath = '')
    {
        $contents = $folder->children()->get();

        foreach ($contents as $item) {
            $itemPath = $basePath . $item->file_name;
            if ($item->type === 2) {
                $zip->addEmptyDir($itemPath . '/');
                $this->zipDirectory($zip, $item, $itemPath . '/');
            } elseif ($item->type === 0) {
                $realFilePath = Storage::disk('public')->path($item->file_path);
                if (file_exists($realFilePath)) {
                    $zip->addFile($realFilePath, $itemPath);
                }
            }
        }
    }

    public function destroyFile($location, $fileId)
    {
        $file = File::findOrFail($fileId);

        if ($location === "Lesson") {
            if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                return redirect()->route('lessons.environment.lesson.files', $file->filable_id)->with('error', 'Je hebt geen toestemming om dit bestand te verwijderen.');
            }
        }

        $this->deleteFolderContents($file);
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        $log = new Log();
        $log->createLog(Auth::id(), 2, 'Delete file', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Bestand verwijderd');

        return redirect()->back()->with('success', 'Bestand succesvol verwijderd.');
    }

    private function deleteFolderContents($folder)
    {
        if ($folder->type == 2) {
            $filesInFolder = File::where('folder_id', $folder->id)->get();
            foreach ($filesInFolder as $file) {
                $this->deleteFolderContents($file);
                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }
                $file->delete();
            }
        }
    }

    public function toggleFileAccess($location, $fileId)
    {
        $file = File::findOrFail($fileId);

        if ($location === "Lesson") {
            if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                return redirect()->route('lessons.environment.lesson.files', $file->filable_id)->with('error', 'Je hebt geen toestemming om de toegang tot dit bestand te wijzigen.');
            }
        }

        $newAccess = $file->access === 'teachers' ? 'all' : 'teachers';
        $file->update(['access' => $newAccess]);

        $log = new Log();
        $log->createLog(Auth::id(), 2, 'Update file permissions', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Toegang gewijzigd '.$file->access);

        return redirect()->back()->with('success', 'Toegang succesvol aangepast.');
    }
}
