<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\File;

class FileManager extends Component
{
    public $files;
    public $breadcrumbs;
    public $folderId;
    public $hasAdminViewers;
    public $isAdmin;
    public $storageUrl;
    public $adminName;
    public $nonAdminName;
    public $location;
    public $locationId;

    // New property for the sidebar and move/copy modals
    public $flatFolders;

    public function __construct(
        $files,
        $breadcrumbs,
        $hasAdminViewers,
        $storageUrl,
        $location,
        $folderId = null,
        $isAdmin = false,
        $adminName = "Administratie",
        $nonAdminName = "Administratie",
        $locationId = null
    ) {
        $this->files = $files;
        $this->breadcrumbs = $breadcrumbs;
        $this->folderId = $folderId;
        $this->hasAdminViewers = $hasAdminViewers;
        $this->isAdmin = $isAdmin;
        $this->adminName = $adminName;
        $this->nonAdminName = $nonAdminName;
        $this->storageUrl = $storageUrl;
        $this->location = $location;
        $this->locationId = $locationId;

        // Fetch all folders for this location to build the hierarchical tree
        $allFolders = File::where('location', $location)
            ->where('location_id', $locationId)
            ->where('type', 2)
            ->orderBy('file_name')
            ->get();

        $this->flatFolders = $this->buildFlatTree($allFolders);
    }

    /**
     * Recursively flattens the folder tree and adds a depth indicator for UI rendering
     */
    private function buildFlatTree($folders, $parentId = null, $depth = 0) {
        $flat = [];
        foreach ($folders as $folder) {
            if ($folder->folder_id == $parentId) {
                $folder->depth = $depth;
                $flat[] = $folder;
                // Recursively fetch children and merge them into the flat array
                $flat = array_merge($flat, $this->buildFlatTree($folders, $folder->id, $depth + 1));
            }
        }
        return $flat;
    }

    public function render(): View|Closure|string
    {
        return view('components.file_manager');
    }
}
