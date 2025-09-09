<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class FileController extends Controller
{
    /**
     * Display a listing of the files.
     */
    public function index($fileableId, $fileableType, $folderId)
    {
        $modelClass = "App\\Models\\" . ucfirst($fileableType);
        $fileable = $modelClass::findOrFail($fileableId);

        $files = $fileable->files()->where('folder_id', $folderId)->get();

        $files = $files->sort(function ($a, $b) {
            if ($a->type != $b->type) {
                return $b->type <=> $a->type; // folders (2) before URLs (1)
            }

            return strcasecmp($a->file_name, $b->file_name);
        })->values();


        $breadcrumbs = $this->generateBreadcrumbs($fileableId, $fileableType, $folderId);

        return [
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    /**
     * Generate breadcrumbs based on folder hierarchy.
     */
    private function generateBreadcrumbs($fileableId, $fileableType, $folderId)
    {
        $breadcrumbs = [];

        // Eerste crumb: root
        $breadcrumbs[] = [
            'name' => 'Bestanden',
            'url' => route('lessons.environment.lesson.files', [
                'lessonId' => $fileableId,
                'folder' => null, // of laat folder weg, hangt af van je route
            ]),
        ];

        // Start vanaf huidige folder
        $currentFolder = $folderId ? File::find($folderId) : null;

        // Loop omhoog in de hiërarchie
        $trail = [];
        while ($currentFolder) {
            $trail[] = [
                'name' => $currentFolder->file_name,
                'url' => route('lessons.environment.lesson.files', [
                    'lessonId' => $fileableId,
                    'folder' => $currentFolder->id,
                ]),
            ];

            $currentFolder = $currentFolder->parent; // parent-relatie in File model
        }

        // Draai de maptrail om (root → current folder)
        $trail = array_reverse($trail);

        // Voeg toe aan breadcrumbs
        $breadcrumbs = array_merge($breadcrumbs, $trail);

        return $breadcrumbs;
    }


    /**
     * Store a newly created file, folder or hyperlink in storage.
     */

    public function filesStore(Request $request, $fileableType, $fileableId)
    {
        try {
            $modelClass = "App\\Models\\" . ucfirst($fileableType);
            $fileable = $modelClass::findOrFail($fileableId);

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
                        $filePath = Storage::disk('public')->putFile($fileableType . '/' . $fileable->id, $uploadedFile);
                        $newFile = new File();
                        $newFile->fileable()->associate($fileable);
                        $newFile->folder_id = $folderId;
                        $newFile->file_name = $uploadedFile->getClientOriginalName();
                        $newFile->file_path = $filePath;
                        $newFile->type = 0;
                        $newFile->user_id = Auth::id();
                        $newFile->access = $request->input('access');
                        $newFile->save();
                    }
                    return redirect()->back();

                case '1':
                    $request->validate(['file' => 'required|url', 'title' => 'required']);
                    $newFile = new File();
                    $newFile->fileable()->associate($fileable);
                    $newFile->folder_id = $folderId;
                    $newFile->file_name = $request->input('title');
                    $newFile->file_path = $request->input('file');
                    $newFile->user_id = Auth::id();
                    $newFile->type = 1;
                    $newFile->access = $request->input('access');
                    $newFile->save();
                    return redirect()->back();

                case '2':
                    $request->validate(['title' => 'required']);
                    $newFile = new File();
                    $newFile->fileable()->associate($fileable);
                    $newFile->folder_id = $folderId;
                    $newFile->file_name = $request->input('title');
                    $newFile->file_path = "";
                    $newFile->user_id = Auth::id();
                    $newFile->type = 2;
                    $newFile->access = $request->input('access');
                    $newFile->save();
                    return redirect()->back();

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
                        $parentFolder->fileable()->associate($fileable);
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
                                $newFolder->fileable()->associate($fileable);
                                $newFolder->folder_id = $currentParent->id;
                                $newFolder->file_name = $folderName;
                                $newFolder->file_path = Storage::disk('public')->putFile($fileableType . '/' . $fileable->id, $uploadedFile);
                                $newFolder->user_id = Auth::id();
                                $newFolder->type = 2;
                                $newFolder->access = $request->input('access');
                                $newFolder->save();
                                $currentParent = $newFolder;
                            } else {
                                $currentParent = $existingFolder;
                            }
                        }
                        $filePath = Storage::disk('public')->putFile($fileableType . '/' . $fileable->id, $uploadedFile);
                        $newFile = new File();
                        $newFile->fileable()->associate($fileable);
                        $newFile->folder_id = $currentParent->id;
                        $newFile->file_name = $uploadedFile->getClientOriginalName();
                        $newFile->file_path = $filePath;
                        $newFile->user_id = Auth::id();
                        $newFile->type = 0;
                        $newFile->access = $request->input('access');
                        $newFile->save();
                    }
                    return redirect()->back();
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes if needed
            \Log::error('Error in filesStore: ' . $e->getMessage());

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
            // Find the position of the last dot for the extension
            $lastDotPosition = strrpos($storedFileName, '.');
            $extension = ($lastDotPosition === false) ? '' : substr($storedFileName, $lastDotPosition + 1);

            // Get the filename without the last underscore and everything after it
            $lastUnderscorePosition = strrpos($storedFileName, '_');
            $baseNameWithoutLastUnderscore = $lastUnderscorePosition !== false ? substr($storedFileName, 0, $lastUnderscorePosition) : $storedFileName;

            // Find the second-to-last underscore within the cleaned base name
            $secondToLastUnderscorePosition = strrpos($baseNameWithoutLastUnderscore, '_');

            // Extract the clean name
            if ($secondToLastUnderscorePosition !== false) {
                $cleanBaseName = substr($baseNameWithoutLastUnderscore, 0, $secondToLastUnderscorePosition);
            } else {
                // Fallback if the second-to-last underscore is not found
                $cleanBaseName = $baseNameWithoutLastUnderscore;
            }

            // Reconstruct the final download name
            $downloadName = $cleanBaseName;
            if (!empty($extension)) {
                $downloadName .= '.' . $extension;
            }

            return Response::download($filePath, $downloadName);
        } catch (\Exception $e) {
            return Response::download($filePath, $file->name);
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

    /**
     * Recursively add a folder's contents to a ZIP archive.
     */
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

    /**
     * Delete a file.
     */
    public function destroyFile($location, $fileId,)
    {
        $file = File::findOrFail($fileId);

        if ($location === "Lesson") {
            if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                return redirect()->route('lessons.environment.lesson.files', $file->filable_id)->with('error', 'Je hebt geen toestemming om dit bestand te verwijderen.');
            }
        }

        // Recursive function to delete files in the folder
        $this->deleteFolderContents($file);

        // Delete the file from storage
        Storage::disk('public')->delete($file->file_path);

        // Delete the file record from the database
        $file->delete();

        // Log the deletion
        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Delete file', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Bestand verwijderd');

        return redirect()->back()->with('success', 'Bestand succesvol verwijderd.');
    }

    private function deleteFolderContents($folder)
    {
        // If the file is a folder (type 2), recursively delete its contents
        if ($folder->type == 2) {
            // Get all files inside this folder (those whose `folder_id` is this folder's ID)
            $filesInFolder = File::where('folder_id', $folder->id)->get();

            foreach ($filesInFolder as $file) {
                // If the file is a folder itself, recursively delete its contents
                $this->deleteFolderContents($file);

                // Delete the file from storage
                Storage::disk('public')->delete($file->file_path);

                // Delete the file record from the database
                $file->delete();
            }
        }
    }


    public function toggleFileAccess($location, $fileId)
    {
        // Fetch the file and ensure it belongs to the specified lesson
        $file = File::findOrFail($fileId);

        if ($location === "Lesson") {
            if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
                return redirect()->route('lessons.environment.lesson.files', $file->filable_id)->with('error', 'Je hebt geen toestemming om de toegang tot dit bestand te wijzigen.');
            }
        }

        // Toggle the file's access
        $newAccess = $file->access === 'teachers' ? 'all' : 'teachers';
        $file->update(['access' => $newAccess]);

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Update file permissions', "Fileable_type ".$file->fileable_type, "Location ".$file->fileable_id, 'Toegang gewijzigd '.$file->access);


        return redirect()->back()
            ->with('success', 'Toegang succesvol aangepast.');
    }


}
