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
                return $b->type <=> $a->type;
            }
            return strcasecmp($a->file_name, $b->file_name);
        })->values();

        $breadcrumbs = $this->generateBreadcrumbs($locationId, $location, $folderId);

        return [
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    private function generateBreadcrumbs($locationId, $location, $folderId)
    {
        // Jouw bestaande comment blok
    }

    /**
     * Centralized Batch Action processor for Move, Copy, Delete, Rename, and Share (INTERNAL)
     */
    public function batchAction(Request $request, $location, $locationId)
    {
        $action = $request->input('action');
        $fileIds = $request->input('file_ids', []);
        $targetFolderId = $request->input('target_folder_id');
        $newName = $request->input('new_name');

        if (empty($fileIds)) return response()->json(['error' => 'Geen bestanden geselecteerd.'], 400);

        try {
            if ($action == 'share') {
                $isShared = $request->input('is_shared');
                $permission = $request->input('share_permission', 'read');
                $shareMode = $request->input('share_mode', 'merge');
                $commonHash = null;

                if ($isShared) {
                    if ($shareMode == 'merge') {
                        $firstFile = File::find($fileIds[0]);
                        if ($firstFile) {
                            $currentParentId = $firstFile->folder_id;
                            while ($currentParentId && !$commonHash) {
                                $parent = File::find($currentParentId);
                                if ($parent) {
                                    if ($parent->share_hash) $commonHash = $parent->share_hash;
                                    $currentParentId = $parent->folder_id;
                                } else {
                                    $currentParentId = null;
                                }
                            }

                            if (!$commonHash) {
                                $sibling = File::where('folder_id', $firstFile->folder_id)->where('location', $firstFile->location)->where('location_id', $firstFile->location_id)->whereNotIn('id', $fileIds)->whereNotNull('share_hash')->first();
                                if ($sibling) $commonHash = $sibling->share_hash;
                            }

                            if (!$commonHash) {
                                foreach ($fileIds as $id) {
                                    $f = File::find($id);
                                    if ($f && $f->share_hash) { $commonHash = $f->share_hash; break; }
                                }
                            }
                        }
                    }
                    if (!$commonHash) $commonHash = Str::random(32);
                }

                foreach ($fileIds as $id) {
                    $file = File::findOrFail($id);
                    if ($location == "Lesson" && $file->user_id !== Auth::id()) {
                        if (!$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) continue;
                    }
                    if ($isShared) $this->applyShareSettingsRecursively($file, $commonHash, $permission);
                    else $this->applyShareSettingsRecursively($file, null, 'read');
                }
                return response()->json(['success' => true, 'share_hash' => $commonHash ?? null]);
            }

            foreach ($fileIds as $id) {
                $file = File::findOrFail($id);
                if ($location == "Lesson" && $file->user_id !== Auth::id()) {
                    if (!$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) continue;
                }

                switch ($action) {
                    case 'rename':
                        $file->update(['file_name' => $newName]);
                        break;
                    case 'move':
                        if ($file->type == 2 && $this->isDescendantFolder($file, $targetFolderId)) return response()->json(['error' => 'Kan een map niet naar zichzelf verplaatsen.'], 400);

                        $newShareHash = null; $newSharePermission = 'read';
                        if ($targetFolderId) {
                            $parentFolder = File::find($targetFolderId);
                            if ($parentFolder && $parentFolder->share_hash) {
                                $newShareHash = $parentFolder->share_hash;
                                $newSharePermission = $parentFolder->share_permission;
                            }
                        }
                        $this->applyShareSettingsRecursively($file, $newShareHash, $newSharePermission);
                        $file->update(['folder_id' => $targetFolderId]);
                        break;
                    case 'copy':
                        $this->copyFileRecursive($file, $targetFolderId, $location, $locationId);
                        break;
                    case 'delete':
                        $this->deleteFolderContents($file);
                        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) Storage::disk('public')->delete($file->file_path);
                        $log = new Log(); $log->createLog(Auth::id(), 2, 'Delete file (Batch)', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Bestand verwijderd: '.$file->file_name);
                        $file->delete();
                        break;
                }
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Batch Action Error: ' . $e->getMessage());
            return response()->json(['error' => 'Interne serverfout.'], 500);
        }
    }

    /**
     * Centralized Public Batch Action processor (No login required)
     */
    public function publicBatch(Request $request, $hash)
    {
        $sharedRoot = File::where('share_hash', $hash)->firstOrFail();
        if ($sharedRoot->share_permission !== 'write') return response()->json(['error' => 'Geen toestemming voor deze actie.'], 403);

        $action = $request->input('action');
        $fileIds = $request->input('file_ids', []);
        $targetFolderId = $request->input('target_folder_id');
        $newName = $request->input('new_name');

        if (empty($fileIds)) return response()->json(['error' => 'Geen bestanden geselecteerd.'], 400);
        if ($action == 'share' || $action == 'toggle-access') return response()->json(['error' => 'Actie niet toegestaan.'], 403);

        try {
            foreach ($fileIds as $id) {
                $file = File::where('share_hash', $hash)->findOrFail($id);

                switch ($action) {
                    case 'rename':
                        $file->update(['file_name' => $newName]);
                        break;
                    case 'move':
                        if ($file->type == 2 && $this->isDescendantFolder($file, $targetFolderId)) return response()->json(['error' => 'Ongeldige verplaatsing.'], 400);
                        if ($targetFolderId) {
                            $target = File::where('share_hash', $hash)->find($targetFolderId);
                            if (!$target) return response()->json(['error' => 'Doelmap valt buiten gedeelde structuur.'], 403);
                        }
                        $file->update(['folder_id' => $targetFolderId]);
                        break;
                    case 'copy':
                        if ($targetFolderId) {
                            $target = File::where('share_hash', $hash)->find($targetFolderId);
                            if (!$target) return response()->json(['error' => 'Doelmap valt buiten gedeelde structuur.'], 403);
                        }
                        $this->copyFileRecursive($file, $targetFolderId, $sharedRoot->location, $sharedRoot->location_id, $sharedRoot->user_id);
                        break;
                    case 'delete':
                        $this->deleteFolderContents($file);
                        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) Storage::disk('public')->delete($file->file_path);
                        $file->delete();
                        break;
                }
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Interne serverfout.'], 500);
        }
    }

    private function isDescendantFolder($folder, $targetId) {
        if ($targetId == $folder->id) return true;
        $target = File::find($targetId);
        while ($target && $target->folder_id) {
            if ($target->folder_id == $folder->id) return true;
            $target = File::find($target->folder_id);
        }
        return false;
    }

    private function copyFileRecursive($file, $targetFolderId, $location, $locationId, $customUserId = null) {
        $newFile = $file->replicate();
        $newFile->folder_id = $targetFolderId;
        $newFile->user_id = $customUserId ?: Auth::id();

        $newShareHash = null; $newSharePermission = 'read'; $newAccess = $file->access;
        if ($targetFolderId) {
            $parent = File::find($targetFolderId);
            if ($parent && $parent->share_hash) {
                $newShareHash = $parent->share_hash;
                $newSharePermission = $parent->share_permission;
                $newAccess = 'all';
            }
        }
        $newFile->share_hash = $newShareHash;
        $newFile->share_permission = $newSharePermission;
        $newFile->access = $newAccess;

        if ($file->type == 0 && !empty($file->file_path) && Storage::disk('public')->exists($file->file_path)) {
            $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
            $newPath = $location . '/' . $locationId . '/' . Str::random(40) . ($extension ? '.' . $extension : '');
            Storage::disk('public')->copy($file->file_path, $newPath);
            $newFile->file_path = $newPath;
        }

        $newFile->save();

        if ($file->type == 2) {
            $children = File::where('folder_id', $file->id)->get();
            foreach ($children as $child) {
                $this->copyFileRecursive($child, $newFile->id, $location, $locationId, $customUserId);
            }
        }
    }

    /**
     * Shared logic for storing files, URLs and folders (Used by both protected & public routes)
     */
    private function processUploadAction(Request $request, $location, $locationId, $folderId, $userId, $access, $parentShareHash = null, $parentSharePermission = 'read')
    {
        switch ($request->input('type')) {
            case '0':
                $request->validate(['file' => 'required|array', 'file.*' => 'file']);
                foreach ($request->file('file') as $uploadedFile) {
                    $filePath = Storage::disk('public')->putFile($location . '/' . $locationId, $uploadedFile);
                    $newFile = new File();
                    $newFile->location = $location; $newFile->location_id = $locationId; $newFile->lesson_id = $location == 'Lesson' ? $locationId : null;
                    $newFile->folder_id = $folderId; $newFile->file_name = $uploadedFile->getClientOriginalName();
                    $newFile->file_path = $filePath; $newFile->type = 0; $newFile->user_id = $userId;
                    $newFile->access = $access; $newFile->share_hash = $parentShareHash; $newFile->share_permission = $parentSharePermission;
                    $newFile->save();
                }
                if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
                return redirect()->back()->with('success', 'Bestanden geüpload.');

            case '1':
                $request->validate(['file' => 'required|url', 'title' => 'required']);
                $newFile = new File();
                $newFile->location = $location; $newFile->location_id = $locationId; $newFile->lesson_id = $location == 'Lesson' ? $locationId : null;
                $newFile->folder_id = $folderId; $newFile->file_name = $request->input('title');
                $newFile->file_path = $request->input('file'); $newFile->user_id = $userId;
                $newFile->type = 1; $newFile->access = $access;
                $newFile->share_hash = $parentShareHash; $newFile->share_permission = $parentSharePermission;
                $newFile->save();
                if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
                return redirect()->back()->with('success', 'Hyperlink toegevoegd.');

            case '2':
                $request->validate(['title' => 'required']);
                $newFile = new File();
                $newFile->location = $location; $newFile->location_id = $locationId; $newFile->lesson_id = $location == 'Lesson' ? $locationId : null;
                $newFile->folder_id = $folderId; $newFile->file_name = $request->input('title');
                $newFile->file_path = ""; $newFile->user_id = $userId;
                $newFile->type = 2; $newFile->access = $access;
                $newFile->share_hash = $parentShareHash; $newFile->share_permission = $parentSharePermission;
                $newFile->save();
                if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
                return redirect()->back()->with('success', 'Map aangemaakt.');

            case '3':
                $request->validate(['folder_upload' => 'required|array', 'folder_upload.*' => 'file', 'folder_paths' => 'required|string']);
                $folderPaths = json_decode($request->input('folder_paths'), true);
                if (empty($folderPaths)) return response()->json(['error' => 'Geen bestanden gevonden in de map.'], 422);

                $baseFolderName = explode('/', $folderPaths[0])[0];
                $parentFolder = File::where('folder_id', $folderId)->where('file_name', $baseFolderName)->where('type', 2)->first();

                if (!$parentFolder) {
                    $parentFolder = new File();
                    $parentFolder->location = $location; $parentFolder->location_id = $locationId; $parentFolder->lesson_id = $location == 'Lesson' ? $locationId : null;
                    $parentFolder->folder_id = $folderId; $parentFolder->file_name = $baseFolderName;
                    $parentFolder->file_path = ""; $parentFolder->user_id = $userId;
                    $parentFolder->type = 2; $parentFolder->access = $access;
                    $parentFolder->share_hash = $parentShareHash; $parentFolder->share_permission = $parentSharePermission;
                    $parentFolder->save();
                }

                foreach ($request->file('folder_upload') as $key => $uploadedFile) {
                    $path = $folderPaths[$key];
                    $parts = explode('/', $path); array_shift($parts);
                    $currentParent = $parentFolder;

                    for ($i = 0; $i < count($parts) - 1; $i++) {
                        $folderName = $parts[$i];
                        $existingFolder = $currentParent->children()->where('file_name', $folderName)->first();
                        if (!$existingFolder) {
                            $newFolder = new File();
                            $newFolder->location = $location; $newFolder->location_id = $locationId; $newFolder->lesson_id = $location == 'Lesson' ? $locationId : null;
                            $newFolder->folder_id = $currentParent->id; $newFolder->file_name = $folderName;
                            $newFolder->file_path = Storage::disk('public')->putFile($location . '/' . $locationId, $uploadedFile);
                            $newFolder->user_id = $userId; $newFolder->type = 2; $newFolder->access = $access;
                            $newFolder->share_hash = $parentShareHash; $newFolder->share_permission = $parentSharePermission;
                            $newFolder->save();
                            $currentParent = $newFolder;
                        } else {
                            $currentParent = $existingFolder;
                        }
                    }
                    $filePath = Storage::disk('public')->putFile($location . '/' . $locationId, $uploadedFile);
                    $newFile = new File();
                    $newFile->location = $location; $newFile->location_id = $locationId; $newFile->lesson_id = $location == 'Lesson' ? $locationId : null;
                    $newFile->folder_id = $currentParent->id; $newFile->file_name = $uploadedFile->getClientOriginalName();
                    $newFile->file_path = $filePath; $newFile->user_id = $userId; $newFile->type = 0; $newFile->access = $access;
                    $newFile->share_hash = $parentShareHash; $newFile->share_permission = $parentSharePermission;
                    $newFile->save();
                }
                if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => true]);
                return redirect()->back()->with('success', 'Mappen geüpload.');
        }
    }

    public function filesStore(Request $request, $location, $locationId)
    {
        try {
            $request->validate(['type' => 'required', 'folder_id' => 'nullable|exists:files,id', 'access' => 'required|in:all,teachers']);
            $folderId = $request->input('folder_id'); $access = $request->input('access');
            $parentShareHash = null; $parentSharePermission = 'read';

            if ($folderId) {
                $parent = File::find($folderId);
                if ($parent && $parent->share_hash) {
                    $parentShareHash = $parent->share_hash; $parentSharePermission = $parent->share_permission; $access = 'all';
                }
            }

            return $this->processUploadAction($request, $location, $locationId, $folderId, Auth::id(), $access, $parentShareHash, $parentSharePermission);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['error' => 'Database fout: ' . $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Toevoegen mislukt: ' . $e->getMessage());
        }
    }

    public function publicUpload(Request $request, $hash, $folderId = null)
    {
        $folderId = $folderId ?: $request->query('folder');
        $folderId = $request->input('folder_id') ?: $folderId;

        $sharedRoot = File::where('share_hash', $hash)->firstOrFail();
        if ($sharedRoot->share_permission !== 'write') {
            if ($request->ajax()) return response()->json(['error' => 'Geen toestemming.'], 403);
            return redirect()->back()->with('error', 'Geen toestemming.');
        }

        try {
            return $this->processUploadAction($request, $sharedRoot->location, $sharedRoot->location_id, $folderId, $sharedRoot->user_id, 'all', $hash, 'write');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['error' => 'Database fout: ' . $e->getMessage()], 500);
            return redirect()->back()->with('error', 'Toevoegen mislukt.');
        }
    }

    public function downloadFile(Request $request, File $file)
    {
        $filePath = Storage::disk('public')->path($file->file_path);
        if (!file_exists($filePath)) return redirect()->back()->with('error', 'Bestand niet gevonden.');

        $storedFileName = $file->file_name;
        try {
            $lastDotPosition = strrpos($storedFileName, '.');
            $extension = ($lastDotPosition == false) ? '' : substr($storedFileName, $lastDotPosition + 1);
            $lastUnderscorePosition = strrpos($storedFileName, '_');
            $baseNameWithoutLastUnderscore = $lastUnderscorePosition !== false ? substr($storedFileName, 0, $lastUnderscorePosition) : $storedFileName;
            $secondToLastUnderscorePosition = strrpos($baseNameWithoutLastUnderscore, '_');

            $cleanBaseName = $secondToLastUnderscorePosition !== false ? substr($baseNameWithoutLastUnderscore, 0, $secondToLastUnderscorePosition) : $baseNameWithoutLastUnderscore;
            $downloadName = $cleanBaseName . (!empty($extension) ? '.' . $extension : '');
            return Response::download($filePath, $downloadName);
        } catch (\Exception $e) {
            return Response::download($filePath, $file->file_name);
        }
    }

    public function downloadFolder(Request $request, File $folder)
    {
        try {
            if ($folder->type !== 2) return redirect()->back()->with('error', 'Dit is geen map.');
            $zipFileName = $folder->file_name . '.zip';
            $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) == true) {
                $this->zipDirectory($zip, $folder);
                $zip->close();
            } else throw new \Exception('Kon het ZIP-bestand niet aanmaken.');
            return Response::download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Kon map niet downloaden.');
        }
    }

    public function publicDownloadFile(Request $request, $hash, File $file)
    {
        if ($file->share_hash !== $hash) abort(403, 'Geen toegang tot dit bestand via deze link.');
        return $this->downloadFile($request, $file);
    }

    public function publicDownloadFolder(Request $request, $hash, File $folder)
    {
        if ($folder->share_hash !== $hash) abort(403, 'Geen toegang tot deze map via deze link.');
        return $this->downloadFolder($request, $folder);
    }

    private function zipDirectory(ZipArchive $zip, File $folder, $basePath = '')
    {
        $contents = $folder->children()->get();
        foreach ($contents as $item) {
            $itemPath = $basePath . $item->file_name;
            if ($item->type == 2) {
                $zip->addEmptyDir($itemPath . '/');
                $this->zipDirectory($zip, $item, $itemPath . '/');
            } elseif ($item->type == 0) {
                $realFilePath = Storage::disk('public')->path($item->file_path);
                if (file_exists($realFilePath)) $zip->addFile($realFilePath, $itemPath);
            }
        }
    }

    public function destroyFile($location, $fileId)
    {
        $file = File::findOrFail($fileId);
        if ($location == "Lesson") {
            if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                return redirect()->route('lessons.environment.lesson.files', $file->filable_id)->with('error', 'Geen toestemming.');
            }
        }
        $this->deleteFolderContents($file);
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) Storage::disk('public')->delete($file->file_path);
        $file->delete();
        $log = new Log(); $log->createLog(Auth::id(), 2, 'Delete file', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Bestand verwijderd');
        return redirect()->back()->with('success', 'Bestand succesvol verwijderd.');
    }

    private function deleteFolderContents($folder)
    {
        if ($folder->type == 2) {
            $filesInFolder = File::where('folder_id', $folder->id)->get();
            foreach ($filesInFolder as $file) {
                $this->deleteFolderContents($file);
                if ($file->file_path && Storage::disk('public')->exists($file->file_path)) Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
        }
    }

    public function toggleFileAccess($location, $fileId)
    {
        $file = File::findOrFail($fileId);
        if ($location == "Lesson") {
            if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                return redirect()->route('lessons.environment.lesson.files', $file->filable_id)->with('error', 'Geen toestemming.');
            }
        }
        $newAccess = $file->access == 'teachers' ? 'all' : 'teachers';
        $file->update(['access' => $newAccess]);
        return redirect()->back()->with('success', 'Toegang aangepast.');
    }

    private function applyShareSettingsRecursively($file, $hash, $permission) {
        $file->share_hash = $hash;
        $file->share_permission = $permission;
        if ($hash !== null) $file->access = 'all';
        $file->save();
        if ($file->type == 2) {
            $children = File::where('folder_id', $file->id)->get();
            foreach ($children as $child) $this->applyShareSettingsRecursively($child, $hash, $permission);
        }
    }

    public function publicShared(Request $request, $hash, $folderId = null)
    {
        $folderId = $folderId ?: $request->query('folder');
        if (empty($folderId)) $folderId = null;

        $sharedRootFiles = File::where('share_hash', $hash)->get();
        if ($sharedRootFiles->isEmpty()) abort(404, 'Gedeelde link niet gevonden of verlopen.');

        $rootItems = $sharedRootFiles->filter(function($f) use ($sharedRootFiles) {
            return !$f->folder_id || !$sharedRootFiles->contains('id', $f->folder_id);
        })->values();

        $sharedRoot = $rootItems->first();

        if ($folderId) {
            $currentFolder = File::where('share_hash', $hash)->findOrFail($folderId);
            $files = File::where('folder_id', $folderId)->where('share_hash', $hash)->get();
            $breadcrumbs = [];
            $curr = $currentFolder;
            while ($curr && $sharedRootFiles->contains('id', $curr->id)) {
                array_unshift($breadcrumbs, $curr);
                $curr = $sharedRootFiles->firstWhere('id', $curr->folder_id);
            }
        } else {
            $currentFolder = null;
            $files = $rootItems;
            $breadcrumbs = [];
        }

        $files = $files->sort(function ($a, $b) {
            if ($a->type != $b->type) return $b->type <=> $a->type;
            return strcasecmp($a->file_name, $b->file_name);
        })->values();

        return view('files.public_shared', compact('files', 'hash', 'currentFolder', 'sharedRoot', 'breadcrumbs'));
    }
}
