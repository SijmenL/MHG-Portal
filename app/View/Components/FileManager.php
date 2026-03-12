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
    public $flatFolders;
    public $isPublic;
    public $shareHash;

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
        $locationId = null,
        $isPublic = false,
        $shareHash = null
    ) {
        $this->files = $files;
        $this->breadcrumbs = $breadcrumbs;
        $this->folderId = $folderId;
        $this->storageUrl = $storageUrl;
        $this->location = $location;
        $this->locationId = $locationId;
        $this->shareHash = $shareHash;
        $this->adminName = $adminName;
        $this->nonAdminName = $nonAdminName;

        // Zorg dat booleans goed worden uitgelezen, zelfs als ze als string/getal doorgegeven worden
        $this->hasAdminViewers = filter_var($hasAdminViewers, FILTER_VALIDATE_BOOLEAN);
        $this->isPublic = filter_var($isPublic, FILTER_VALIDATE_BOOLEAN);
        $this->isAdmin = filter_var($isAdmin, FILTER_VALIDATE_BOOLEAN);

        // De Kern-logica:
        // Als dit een interne pagina is (niet publiek) EN er is geen specifiek 'Toegang' onderscheid ingesteld
        // Dan krijgt elke bezoeker van deze pagina automatisch volledige bewerk-rechten (isAdmin = true)
        if (!$this->isPublic && !$this->hasAdminViewers) {
            $this->isAdmin = true;
        }

        // Haal alle mappen op voor deze locatie om de boomstructuur op te bouwen
        if ($this->isPublic) {
            // Beperk boomweergave tot alleen de specifieke publiek gedeelde structuur
            $allFoldersQuery = File::where('share_hash', $this->shareHash)->where('type', 2);
        } else {
            $allFoldersQuery = File::where('location', $this->location)
                ->where('location_id', $this->locationId)
                ->where('type', 2);
        }

        $allFolders = $allFoldersQuery->orderBy('file_name')->get();

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
        // Forceer de overschreven component-variabelen in de view
        // Dit bypast het probleem waarbij het @props block in Blade onze logic negeert
        return view('components.file_manager', [
            'isAdmin' => $this->isAdmin,
            'hasAdminViewers' => $this->hasAdminViewers,
            'isPublic' => $this->isPublic,
        ]);
    }
}
