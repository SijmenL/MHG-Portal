@props([
    'files',
    'breadcrumbs',
    'folderId',
    'isAdmin',
    'adminName',
    'nonAdminName',
    'storageUrl',
    'hasAdminViewers',
    'location',
    'locationId',
    'isPublic' => false,
    'shareHash' => null
])

@php
    // DYNAMISCHE URL LOGICA VOOR INTERN VS PUBLIEK
    $rawUploadUrl = $isPublic
        ? route('files.shared_public_upload', ['hash' => $shareHash, 'folder_id' => $folderId])
        : route('files.store', [$location, $locationId]);

    // Fix voor de Laravel router: haalt dubbele slashes weg als de optionele map-id ontbreekt in de URL
    $uploadUrl = str_replace('//upload', '/upload', $rawUploadUrl);

    $batchUrl = $isPublic
        ? route('files.shared_public_batch', ['hash' => $shareHash])
        : route('files.batch', [$location, $locationId]);

    $baseFolderUrl = $isPublic
        ? route('files.shared_public', ['hash' => $shareHash])
        : '?folder=';

    // Dynamically fetch and build the entire folder tree directly in the view
    if ($isPublic) {
        $allFoldersQuery = \App\Models\File::where('share_hash', $shareHash)->where('type', 2)->orderBy('file_name')->get();
    } else {
        $allFoldersQuery = \App\Models\File::where('location', $location)
            ->where('location_id', $locationId)
            ->where('type', 2)
            ->orderBy('file_name')
            ->get();
    }

    // Filter out restricted folders for non-admins
    if (!$isAdmin && !$isPublic) {
        $allFolders = $allFoldersQuery->reject(function($folder) {
            return $folder->access === 'teachers';
        });
    } else {
        $allFolders = $allFoldersQuery;
    }

    $dynamicBreadcrumbs = [];
    $activePathIds = [];

    if (!empty($folderId)) {
        $curr = $allFoldersQuery->firstWhere('id', $folderId);
        while ($curr) {
            array_unshift($dynamicBreadcrumbs, $curr);
            $activePathIds[] = $curr->id;
            $curr = $allFoldersQuery->firstWhere('id', $curr->folder_id);
        }
    }

    // Recursive function for the visual sidebar tree
    $buildTree = function($parentId, $depth) use (&$buildTree, $allFolders, $folderId, $isPublic, $shareHash) {
        $children = $allFolders->filter(function($folder) use ($parentId) {
            return $folder->folder_id == $parentId;
        });

        $html = '';
        foreach ($children as $folder) {
            $isActive = $folderId == $folder->id;
            $activeClass = $isActive ? 'active-folder bg-primary bg-opacity-10 text-primary fw-bold rounded-3' : 'text-dark hover-bg-light rounded-3';
            $icon = $isActive ? 'folder_open' : 'folder';
            $iconColor = $isActive ? 'text-primary' : 'text-secondary';

            $hasChildren = $allFolders->where('folder_id', $folder->id)->count() > 0;

            $toggleIcon = $hasChildren
                ? '<span class="material-symbols-rounded cursor-pointer toggle-children text-secondary fs-5 me-1" data-target="'.$folder->id.'">chevron_right</span>'
                : '<span style="width:24px; display:inline-block; flex-shrink:0;"></span>';

            $folderLink = $isPublic ? route('files.shared_public', ['hash' => $shareHash, 'folder_id' => $folder->id]) : '?folder=' . $folder->id;

            $html .= '<li class="mb-1">';
            $html .= '<div class="d-flex align-items-center" style="padding-left: ' . (($depth - 1) * 15) . 'px;">';
            $html .= $toggleIcon;
            $html .= '<a href="' . $folderLink . '" class="flex-grow-1 d-flex align-items-center py-2 px-2 ' . $activeClass . '" style="text-decoration: none;">';
            $html .= '<span class="material-symbols-rounded me-2 ' . $iconColor . '">' . $icon . '</span>';
            $html .= '<span class="text-truncate">' . htmlspecialchars($folder->file_name) . '</span>';
            $html .= '</a>';
            $html .= '</div>';

            if ($hasChildren) {
                $html .= '<ul class="list-unstyled m-0 folder-children-list" id="folder-children-' . $folder->id . '" style="display: none;">' . $buildTree($folder->id, $depth + 1) . '</ul>';
            }

            $html .= '</li>';
        }
        return $html;
    };

    // Recursive function for the select options in the move/copy modal
    $renderOptions = function($parentId, $depth) use (&$renderOptions, $allFolders) {
        $children = $allFolders->filter(function($folder) use ($parentId) {
            return $folder->folder_id == $parentId;
        });

        $html = '';
        foreach ($children as $f) {
            $html .= '<option value="' . $f->id . '">' . str_repeat('&nbsp;&nbsp;', $depth) . '- ' . htmlspecialchars($f->file_name) . '</option>';
            $html .= $renderOptions($f->id, $depth + 1);
        }
        return $html;
    };
@endphp

<style>
    .file-sidebar { width: 100%; max-width: 280px; flex-shrink: 0; min-height: calc(100vh - 120px); overflow-y: auto; overflow-x: hidden; border-right: 1px solid #dee2e6; }
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .table-hover tbody tr.file:hover { background-color: #f8f9fa; }
    .table-primary, .table-hover tbody tr.file.table-primary:hover { background-color: #eef5ff !important; }
    tr.file.drag-over { background-color: #e9ecef !important; border: 2px dashed #adb5bd !important; }
    tr.file[draggable="true"] { cursor: grab; }
    tr.file[draggable="true"]:active { cursor: grabbing; }

    #context-menu { position: absolute; z-index: 1050; width: 220px; background: #fff; border: 1px solid #dee2e6; border-radius: .5rem; padding: .5rem 0; display: none; box-shadow: 0 .25rem .75rem rgba(0,0,0,.05); }
    .context-item { padding: .5rem 1.25rem; cursor: pointer; display: flex; align-items: center; gap: .75rem; color: #495057; font-weight: 500; font-size: 0.9rem; }
    .context-item:hover { background: #f8f9fa; color: var(--bs-primary); }
    .context-item.text-danger:hover { background: #fff5f5; color: #dc3545; }
    .cursor-pointer { cursor: pointer; }
    .toggle-children:hover { color: var(--bs-primary) !important; background: #e9ecef; border-radius: 4px; }

    .btn:disabled, .btn.disabled { opacity: 0.4 !important; cursor: not-allowed !important; pointer-events: all !important; background-color: #f8f9fa !important; border-color: #e9ecef !important; color: #adb5bd !important; filter: grayscale(100%); }
    .btn:disabled .material-symbols-rounded, .btn.disabled .material-symbols-rounded { color: #adb5bd !important; }
    .modal { background-color: rgba(0, 0, 0, 0.75) !important; backdrop-filter: blur(5px); }
    .modal-backdrop { display: none !important; }
    #previewModal .modal-content { background-color: transparent !important; border: none !important; }
    .preview-arrow { opacity: 0.6; }
    .preview-arrow:hover { opacity: 1; background-color: rgba(0,0,0,0.8) !important; }
    .file-sidebar::-webkit-scrollbar { width: 6px; }
    .file-sidebar::-webkit-scrollbar-track { background: transparent; }
    .file-sidebar::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 4px; }
    .file-sidebar::-webkit-scrollbar-thumb:hover { background: #adb5bd; }
    body.preview-open { overflow: hidden !important; }

    .view-toggle-btn { background: transparent; padding: 6px 10px; }
    .view-toggle-btn.active { background-color: var(--bs-primary) !important; border-radius: 6px; }
    .view-toggle-btn.active span { color: white !important; }

    #file-browser-container.view-mode-grid .table-responsive { border: none !important; background: transparent; }
    #file-browser-container.view-mode-grid table, #file-browser-container.view-mode-grid thead, #file-browser-container.view-mode-grid tbody, #file-browser-container.view-mode-grid tr { display: block; width: 100%; }
    #file-browser-container.view-mode-grid thead { display: none; }
    #file-browser-container.view-mode-grid tbody { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.25rem; }
    #file-browser-container.view-mode-grid tr.file { display: flex; flex-direction: column; border: 1px solid #dee2e6; border-radius: 0.75rem; padding: 1rem; position: relative; background: #fff; box-shadow: 0 .125rem .25rem rgba(0,0,0,.05); height: 100%; }
    #file-browser-container.view-mode-grid tr.file:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); border-color: #b6d4fe; }
    #file-browser-container.view-mode-grid tr.file.table-primary { border-color: var(--bs-primary); box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb, 13, 110, 253), 0.25); }
    #file-browser-container.view-mode-grid td { display: block; padding: 0.25rem 0 !important; border: none !important; }
    #file-browser-container.view-mode-grid td.size-cell, #file-browser-container.view-mode-grid td.date-cell { display: none !important; }
    #file-browser-container.view-mode-grid td.icon-cell { display: flex; justify-content: center; height: 130px; align-items: center; margin-bottom: 0.75rem; border-radius: 0.5rem; background: #f8f9fa; overflow: hidden; }
    #file-browser-container.view-mode-grid td.icon-cell img.file-thumbnail { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    #file-browser-container.view-mode-grid td.icon-cell img.file-icon-only { width: 56px !important; height: 56px !important; object-fit: contain !important; }
    #file-browser-container.view-mode-grid td.name-cell { font-weight: 600; text-align: center; white-space: normal; overflow: hidden; text-overflow: ellipsis; width: 100%; margin-top: auto; }
    #file-browser-container.view-mode-grid td.access-cell { text-align: center; margin-top: 0.5rem; }
    #file-browser-container.view-mode-grid td.checkbox-cell { position: absolute; top: 0.75rem; left: 0.75rem; z-index: 2; padding: 0 !important; margin: 0 !important; }
    #file-browser-container.view-mode-grid td.checkbox-cell input { transform: scale(1.3); box-shadow: 0 0 0 2px rgba(255,255,255,0.9); cursor: pointer; }
    #file-browser-container:not(.view-mode-grid) img.file-thumbnail, #file-browser-container:not(.view-mode-grid) img.file-icon-only { width: 32px !important; height: 32px !important; object-fit: contain !important; border-radius: 4px; }
</style>

<!-- UPLOAD MODAL -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-sm">
            <div class="modal-header bg-light border-bottom rounded-top-4 py-3">
                <h5 class="modal-title fw-bold" id="uploadModalTitle">Nieuw toevoegen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                @if($isPublic)
                    <div class="alert alert-warning m-4 mb-0 border-0 rounded-3 d-flex align-items-center shadow-sm">
                        <span class="material-symbols-rounded me-2 fs-4">public</span>
                        <small><strong>Publiek Gedeeld:</strong> Je uploadt direct in een publiek gedeelde map.</small>
                    </div>
                @endif

                <ul class="nav nav-tabs px-3 pt-3 bg-light border-bottom-0" id="uploadTabs" role="tablist">
                    <li class="nav-item" role="presentation"><button class="nav-link active fw-medium px-4" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab">Bestanden</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link fw-medium px-4" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab">Hyperlink</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link fw-medium px-4" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab">Map</button></li>
                </ul>

                <div class="tab-content bg-white p-4 rounded-bottom-4" style="min-height: 380px;">
                    <!-- Tab 1: Bestanden -->
                    <div class="tab-pane show active" id="tab1" role="tabpanel">
                        <form id="upload-form" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex flex-wrap gap-4 align-items-center justify-content-center bg-light p-3 rounded-3 border">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="upload-option" id="upload-files" value="files" checked>
                                        <label class="form-check-label fw-medium" for="upload-files">Losse Bestanden</label>
                                    </div>
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="radio" name="upload-option" id="upload-folder" value="folder">
                                        <label class="form-check-label fw-medium" for="upload-folder">Complete Map</label>
                                    </div>
                                </div>

                                <div class="form-group w-100">
                                    <div id="files-upload-container">
                                        <label for="files-input" class="form-label text-muted small fw-bold text-uppercase">Kies bestanden</label>
                                        <input type="file" name="file[]" multiple class="form-control rounded-3 py-2" id="files-input">
                                    </div>
                                    <div id="folder-upload-container" class="d-none">
                                        <label for="folder-input" class="form-label text-muted small fw-bold text-uppercase">Kies een map</label>
                                        <input type="file" name="folder_upload[]" multiple class="form-control rounded-3 py-2" id="folder-input" directory webkitdirectory>
                                    </div>
                                    <input type="hidden" name="type" id="upload-type" value="0">
                                    <input type="hidden" name="folder_id" value="{{ $folderId }}">
                                    <input type="hidden" name="folder_paths" id="folder-paths-input">
                                </div>

                                @if(!$isPublic && $hasAdminViewers)
                                    <div class="form-group">
                                        <label for="access" class="form-label text-muted small fw-bold text-uppercase">Toegang</label>
                                        <select name="access" class="form-select rounded-3 py-2" id="access" >
                                            <option value="teachers" selected>Alleen {{ $adminName }}</option>
                                            <option value="all">Iedereen</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="access" value="all">
                                @endif
                            </div>

                            <div class="progress mt-4 rounded-pill" style="display: none; height: 10px;">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <div id="upload-status" class="text-primary mt-2 small fw-medium text-center" style="display: none;"></div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annuleren</button>
                                <button type="button" id="upload-button" class="btn btn-primary text-white d-flex align-items-center">
                                    <span class="button-text">Uploaden</span>
                                    <span class="loading-spinner spinner-border spinner-border-sm" style="display: none;" aria-hidden="true"></span>
                                    <span class="loading-text ms-2" style="display: none;">Laden...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab 2: Hyperlink -->
                    <div class="tab-pane" id="tab2" role="tabpanel">
                        <div class="alert alert-primary border-0 rounded-3 d-flex align-items-center mb-4">
                            <span class="material-symbols-rounded me-2 fs-4">info</span>
                            <small>Vul een geldige url in, inclusief <code>https://</code></small>
                        </div>
                        <form method="post" action="{{ $uploadUrl }}">
                            @csrf
                            <div class="d-flex flex-column gap-3">
                                <div class="form-group">
                                    <label for="file" class="form-label text-muted small fw-bold text-uppercase">URL</label>
                                    <input type="url" name="file" class="form-control rounded-3 py-2" placeholder="https://www.voorbeeld.nl" required>
                                    <input type="hidden" name="type" value="1">
                                    <input type="hidden" name="folder_id" value="{{ $folderId }}">
                                </div>
                                <div class="form-group">
                                    <label for="title" class="form-label text-muted small fw-bold text-uppercase">Weergavenaam</label>
                                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Naam van de link" required>
                                </div>
                                @if(!$isPublic && $hasAdminViewers)
                                    <div class="form-group">
                                        <label for="access" class="form-label text-muted small fw-bold text-uppercase">Toegang</label>
                                        <select name="access" class="form-select rounded-3 py-2">
                                            <option value="all">Iedereen</option>
                                            <option value="teachers">Alleen {{ $adminName }}</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="access" value="all">
                                @endif
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annuleren</button>
                                <button type="submit" class="btn btn-primary text-white ">Toevoegen</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab 3: Map -->
                    <div class="tab-pane" id="tab3" role="tabpanel">
                        <form method="post" action="{{ $uploadUrl }}">
                            @csrf
                            <div class="d-flex flex-column gap-3">
                                <div class="form-group">
                                    <label for="title" class="form-label text-muted small fw-bold text-uppercase">Naam van de map</label>
                                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Nieuwe map" required>
                                    <input type="hidden" name="type" value="2">
                                    <input type="hidden" name="folder_id" value="{{ $folderId }}">
                                </div>
                                @if(!$isPublic && $hasAdminViewers)
                                    <div class="form-group">
                                        <label for="access" class="form-label text-muted small fw-bold text-uppercase">Toegang</label>
                                        <select name="access" class="form-select rounded-3 py-2">
                                            <option value="all">Iedereen</option>
                                            <option value="teachers">Alleen {{ $adminName }}</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="access" value="all">
                                @endif
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annuleren</button>
                                <button type="submit" class="btn btn-primary text-white">Aanmaken</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$isPublic)
    <!-- SHARE MODAL -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-sm">
                <div class="modal-header bg-light border-bottom rounded-top-4">
                    <h5 class="modal-title fw-bold">Delen met Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="shareFileId">

                    <div id="shareLoadingState" class="d-none text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted small">Instellingen bijwerken...</p>
                    </div>

                    <div id="shareContentState">
                        <div class="form-check form-switch fs-5 mb-3 d-flex align-items-center">
                            <input class="form-check-input cursor-pointer me-3" type="checkbox" role="switch" id="shareToggleSwitch">
                            <label class="form-check-label fw-medium text-dark mt-1" for="shareToggleSwitch" style="font-size: 1rem;">Publiek delen inschakelen</label>
                        </div>
                        <div id="shareSettingsContainer" class="d-none mt-4 pt-3 border-top">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Link Instellingen</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="shareMode" id="shareModeMerge" value="merge" checked>
                                    <label class="form-check-label text-dark" for="shareModeMerge">Koppel aan dichtstbijzijnde gedeelde map/link</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shareMode" id="shareModeNew" value="new">
                                    <label class="form-check-label text-dark" for="shareModeNew">Forceer een nieuwe, aparte link</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold text-uppercase">Rechten voor bezoekers</label>
                                <select id="sharePermissionSelect" class="form-select rounded-3">
                                    <option value="read">Alleen weergeven (Lezen)</option>
                                    <option value="write">Weergeven & Toevoegen (Schrijven)</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-muted small fw-bold text-uppercase">Deelbare Link</label>
                                <div class="input-group shadow-sm rounded-3">
                                    <input type="text" id="shareLinkInput" class="form-control bg-light" readonly>
                                    <button class="btn btn-outline-secondary bg-white d-flex align-items-center" type="button" id="copyShareLinkBtn" title="Kopiëren">
                                        <span class="material-symbols-rounded fs-5 text-dark">content_copy</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top rounded-bottom-4">
                    <button type="button" class="btn btn-primary text-white px-4" data-bs-dismiss="modal">Klaar</button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- DYNAMIC BATCH ACTION MODAL -->
<div class="modal fade" id="batchActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-sm">
            <div class="modal-header bg-light border-bottom rounded-top-4">
                <h5 class="modal-title fw-bold" id="batchActionTitle">Actie Uitvoeren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="batchActionType">
                <div id="batchActionInputContainer"></div>
            </div>
            <div class="modal-footer bg-light border-top rounded-bottom-4">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-primary text-white" id="batchActionSubmit">Bevestigen</button>
            </div>
        </div>
    </div>
</div>

<!-- CUSTOM ALERT MODAL -->
<div class="modal fade" id="customAlertModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-sm">
            <div class="modal-header bg-light border-bottom rounded-top-4">
                <h5 class="modal-title fw-bold">Melding</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 d-flex align-items-center">
                <span class="material-symbols-rounded text-warning fs-1 me-3">warning</span>
                <span class="fw-medium text-dark" id="customAlertMessage">Alert</span>
            </div>
            <div class="modal-footer bg-light border-top rounded-bottom-4">
                <button type="button" class="btn btn-primary text-white" data-bs-dismiss="modal">Begrepen</button>
            </div>
        </div>
    </div>
</div>

<!-- CUSTOM CONFIRM MODAL -->
<div class="modal fade" id="customConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-sm">
            <div class="modal-header bg-light border-bottom rounded-top-4">
                <h5 class="modal-title fw-bold">Bevestiging</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 d-flex align-items-center">
                <span class="material-symbols-rounded text-danger fs-1 me-3">help</span>
                <span class="fw-medium text-dark" id="customConfirmMessage">Weet u het zeker?</span>
            </div>
            <div class="modal-footer bg-light border-top rounded-bottom-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-danger text-white" id="customConfirmBtn">Bevestigen</button>
            </div>
        </div>
    </div>
</div>

<!-- RICH PREVIEW GALLERY MODAL -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen m-0">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header border-0 pb-0 position-absolute top-0 w-100" style="z-index: 1050;">
                <h5 class="modal-title text-white fw-medium text-truncate pe-4" id="previewTitle" style="max-width: 85%;">Bestand Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center position-relative w-100 h-100" id="previewBackdrop" style="cursor: pointer;">
                <button id="previewPrev" class="btn btn-dark bg-opacity-75 text-white position-absolute start-0 top-50 translate-middle-y ms-2 ms-md-4 rounded-circle p-2 border-0 preview-arrow" style="z-index: 10;">
                    <span class="material-symbols-rounded fs-2 d-block">chevron_left</span>
                </button>
                <div id="previewContainer" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 p-md-5 text-center" style="cursor: default; pointer-events: auto;">
                </div>
                <button id="previewNext" class="btn btn-dark bg-opacity-75 text-white position-absolute end-0 top-50 translate-middle-y me-2 me-md-4 rounded-circle p-2 border-0 preview-arrow" style="z-index: 10;">
                    <span class="material-symbols-rounded fs-2 d-block">chevron_right</span>
                </button>
            </div>
            <div class="modal-footer border-0 pt-0 position-absolute bottom-0 w-100 d-flex justify-content-between" style="z-index: 1050; padding: 1rem 2rem;">
                <span id="previewCounter" class="text-white opacity-75 small fw-medium"></span>
                <div class="d-flex gap-2">
                    <a href="#" id="previewDownloadBtn" class="btn btn-primary text-white d-flex align-items-center">
                        <span class="material-symbols-rounded me-2 fs-6">download</span> Downloaden
                    </a>
                    <button type="button" class="btn btn-light " data-bs-dismiss="modal">Sluiten</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTEXT MENU -->
@if($isAdmin)
    <div id="context-menu">
        <div class="context-item" onclick="handleMenuAction('download')">
            <span class="material-symbols-rounded fs-5 text-black">download</span> Downloaden
        </div>

        @if(!$isPublic)
            <div class="context-item" onclick="handleMenuAction('share')">
                <span class="material-symbols-rounded fs-5 text-black">share</span> Delen
            </div>
            @if($hasAdminViewers)
                <div class="context-item" onclick="handleMenuAction('toggle-access')">
                    <span class="material-symbols-rounded fs-5 text-black">security</span> Rechten wisselen
                </div>
            @endif
            <hr class="my-1 border-light">
        @endif

        <div class="context-item" onclick="handleMenuAction('rename')">
            <span class="material-symbols-rounded fs-5 text-black">edit</span> Hernoemen
        </div>
        <div class="context-item" onclick="handleMenuAction('copy')">
            <span class="material-symbols-rounded fs-5 text-black">content_copy</span> Kopiëren
        </div>
        <div class="context-item" onclick="handleMenuAction('move')">
            <span class="material-symbols-rounded fs-5 text-black">drive_file_move</span> Verplaatsen
        </div>
        <hr class="my-2 border-light">
        <div class="context-item text-danger" onclick="handleMenuAction('delete')">
            <span class="material-symbols-rounded fs-5">delete</span> Verwijderen
        </div>
    </div>
@endif

<!-- MAIN LAYOUT -->
<div class="w-100 border border-secondary-subtle rounded-4 bg-white overflow-hidden d-flex align-items-stretch">
    <div class="d-flex flex-column flex-md-row w-100 align-items-stretch">

        <!-- LEFT SIDEBAR: FOLDER TREE -->
        <div class="file-sidebar bg-light d-none d-md-block">
            <div class="p-3 border-bottom bg-white d-flex align-items-center">
                <h6 class="text-uppercase text-muted fw-bold mb-0 d-flex align-items-center flex-grow-1">
                    <span class="material-symbols-rounded me-2 fs-5">account_tree</span> Structuur
                </h6>
            </div>
            <div class="p-2 py-3">
                <ul class="list-unstyled folder-tree m-0">
                    <li>
                        <div class="d-flex align-items-center mb-1">
                            <span style="width:24px; display:inline-block; flex-shrink:0;"></span>
                            <a href="{{ $baseFolderUrl }}" class="flex-grow-1 d-flex align-items-center py-2 px-2 {{ empty($folderId) ? 'active-folder bg-primary bg-opacity-10 text-primary fw-bold rounded-3' : 'text-dark hover-bg-light rounded-3' }}" style="text-decoration: none;">
                                <span class="material-symbols-rounded me-2 {{ empty($folderId) ? 'text-primary' : 'text-secondary' }}">{{ empty($folderId) ? 'folder_open' : 'folder' }}</span>
                                <span class="text-truncate">Hoofdmap</span>
                            </a>
                        </div>
                        <ul class="list-unstyled m-0 folder-children-list" id="folder-children-root">
                            {!! $buildTree(null, 1) !!}
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT CONTENT: BROWSER -->
        <div class="file-manager p-3 p-md-4 flex-grow-1 bg-white" style="min-width: 0;">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-end border-bottom pb-3 mb-4">
                <div>
                    <h3 class="m-0 text-dark fw-bold d-flex align-items-center">
                        @if($isPublic) <span class="material-symbols-rounded fs-2 text-primary me-2">public</span> @endif
                        @if (empty($dynamicBreadcrumbs))
                            {{ $isPublic ? 'Gedeelde Bestanden' : 'Bestanden' }}
                        @else
                            {{ end($dynamicBreadcrumbs)->file_name }}
                        @endif
                    </h3>

                    <!-- DYNAMIC BREADCRUMBS -->
                    <ol class="breadcrumb m-0 mt-2 small flex-wrap">
                        @if(!empty($dynamicBreadcrumbs))
                            <li class="breadcrumb-item"><a href="{{ $baseFolderUrl }}" class="text-decoration-none text-primary">{{ $isPublic ? 'Home' : 'Bestanden' }}</a></li>
                            @foreach ($dynamicBreadcrumbs as $crumb)
                                @if (!$loop->last)
                                    @php $crumbLink = $isPublic ? route('files.shared_public', ['hash' => $shareHash, 'folder_id' => $crumb->id]) : '?folder=' . $crumb->id; @endphp
                                    <li class="breadcrumb-item"><a href="{{ $crumbLink }}" class="text-decoration-none text-primary">{{ $crumb->file_name }}</a></li>
                                @else
                                    <li class="breadcrumb-item active text-black fw-medium">{{ $crumb->file_name }}</li>
                                @endif
                            @endforeach
                        @endif
                    </ol>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center bg-light border border-secondary-subtle rounded-3 p-1">
                        <button class="btn border-0 view-toggle-btn active" data-view="list" title="Lijstweergave">
                            <span class="material-symbols-rounded fs-5 d-block text-secondary">view_list</span>
                        </button>
                        <button class="btn border-0 view-toggle-btn" data-view="grid" title="Tegelweergave">
                            <span class="material-symbols-rounded fs-5 d-block text-secondary">grid_view</span>
                        </button>
                    </div>

                    @if($isAdmin)
                        <div class="dropdown">
                            <button class="btn btn-primary text-white d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="material-symbols-rounded me-2 fs-5">add</span> Nieuw toevoegen
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border border-secondary-subtle rounded-3 mt-1 shadow-sm">
                                <li><a class="dropdown-item py-2 cursor-pointer" onclick="openUploadModal('tab3-tab')"><span class="material-symbols-rounded fs-5 align-middle me-3 text-warning">folder</span> Map</a></li>
                                <li><a class="dropdown-item py-2 cursor-pointer" onclick="openUploadModal('tab1-tab')"><span class="material-symbols-rounded fs-5 align-middle me-3 text-success">upload_file</span> Bestand(en) uploaden</a></li>
                                <li><a class="dropdown-item py-2 cursor-pointer" onclick="openUploadModal('tab2-tab')"><span class="material-symbols-rounded fs-5 align-middle me-3 text-info">link</span> Hyperlink toevoegen</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- BATCH ACTION TOOLBAR -->
            @if($isAdmin)
                <div id="selection-toolbar" class="bg-light border rounded-3 p-3 mb-4 d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <div>
                        <span id="selected-count" class="fw-medium text-dark ms-2 fs-6">0 items geselecteerd</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-sm btn-outline-dark d-flex align-items-center fw-medium batch-multi-action" disabled onclick="handleMenuAction('download')">
                            <span class="material-symbols-rounded fs-6 me-1">download</span> <span class="d-none d-sm-inline">Downloaden</span>
                        </button>

                        @if(!$isPublic)
                            <button class="btn btn-sm btn-outline-dark d-flex align-items-center fw-medium batch-multi-action" disabled onclick="handleMenuAction('share')">
                                <span class="material-symbols-rounded fs-6 me-1">share</span> <span class="d-none d-sm-inline">Delen</span>
                            </button>
                            @if($hasAdminViewers)
                                <button class="btn btn-sm btn-outline-dark  d-flex align-items-center fw-medium batch-multi-action" disabled onclick="handleMenuAction('toggle-access')">
                                    <span class="material-symbols-rounded fs-6 me-1">security</span> <span class="d-none d-sm-inline">Rechten</span>
                                </button>
                            @endif
                        @endif

                        <button class="btn btn-sm btn-outline-dark  d-flex align-items-center fw-medium batch-single-action" disabled onclick="handleMenuAction('rename')">
                            <span class="material-symbols-rounded fs-6 me-1">edit</span> <span class="d-none d-sm-inline">Hernoemen</span>
                        </button>
                        <button class="btn btn-sm btn-outline-dark  d-flex align-items-center fw-medium batch-multi-action" disabled onclick="handleMenuAction('copy')">
                            <span class="material-symbols-rounded fs-6 me-1">content_copy</span> <span class="d-none d-sm-inline">Kopiëren</span>
                        </button>
                        <button class="btn btn-sm btn-outline-dark  d-flex align-items-center fw-medium batch-multi-action" disabled onclick="handleMenuAction('move')">
                            <span class="material-symbols-rounded fs-6 me-1">drive_file_move</span> <span class="d-none d-sm-inline">Verplaatsen</span>
                        </button>
                        <button class="btn btn-sm btn-danger text-white d-flex align-items-center fw-medium batch-multi-action" disabled onclick="handleMenuAction('delete')">
                            <span class="material-symbols-rounded fs-6 me-1">delete</span> <span class="d-none d-sm-inline">Verwijderen</span>
                        </button>
                    </div>
                </div>
            @endif

            @php
                $accessCount = 0;
                foreach ($files as $file) {
                   if(!($file->access === 'teachers' && !$isAdmin)) $accessCount++;
                }
            @endphp

            @if($accessCount > 0)
                <div id="file-browser-container">
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                @if($isAdmin)
                                    <th style="width: 40px; padding-left: 1rem;"><input type="checkbox" id="selectAll" class="form-check-input rounded-1 cursor-pointer"></th>
                                @endif
                                <th style="width: 50px;"></th>
                                <th class="fw-bold">Naam</th>
                                <th class="d-none d-md-table-cell fw-bold">Grootte</th>
                                @if($isAdmin)
                                    <th class="d-none d-lg-table-cell fw-bold">Gewijzigd</th>
                                    @if($hasAdminViewers && !$isPublic) <th class="fw-bold">Toegang</th> @endif
                                @endif
                            </tr>
                            </thead>
                            <tbody class="border-top-0">
                            @foreach($files as $file)
                                @if(!($file->access === 'teachers' && !$isAdmin))
                                    @php
                                        $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ["jpg", "jpeg", "png", "gif", "webp", "svg", "bmp", "avif"]);
                                        $isVideo = in_array(strtolower($extension), ["mp4", "webm", "ogv", "mov"]);
                                        $isAudio = in_array(strtolower($extension), ["mp3", "wav", "ogg", "oga", "aac", "flac"]);
                                        $isPdf = in_array(strtolower($extension), ["pdf"]);
                                        $isOffice = in_array(strtolower($extension), ["doc", "docx", "xls", "xlsx", "ppt", "pptx"]);

                                        $icon = 'unknown.webp';
                                        if ($file->type === 0 || $file->type === null) {
                                            switch(strtolower($extension)) {
                                                case 'pdf': $icon = 'pdf.webp'; break;
                                                case 'jpeg': case 'jpg': $icon = 'jpg.webp'; break;
                                                case 'png': $icon = 'png.webp'; break;
                                                case 'webp': $icon = 'webp.webp'; break;
                                                case 'zip': $icon = 'zip.webp'; break;
                                                case 'docx': case 'doc': $icon = 'doc.webp'; break;
                                                case 'pptx': case 'ppt': $icon = 'ppt.webp'; break;
                                                case 'mp4': $icon = 'mp4.webp'; break;
                                                case 'mov': $icon = 'mov.webp'; break;
                                                case 'mp3': $icon = 'mp3.webp'; break;
                                                case 'wav': $icon = 'wav.webp'; break;
                                                case 'xlsx': $icon = 'xlsx.webp'; break;
                                                case 'html': $icon = 'html.webp'; break;
                                                case 'css': $icon = 'css.webp'; break;
                                                case 'js': $icon = 'js.webp'; break;
                                                case 'svg': $icon = 'ai.webp'; break;
                                                case 'showm': $icon = 'showm.webp'; break;
                                            }
                                        } elseif ($file->type === 1) { $icon = 'url.webp'; }
                                          elseif ($file->type === 2) { $icon = 'folder.webp'; }

                                        // DYNAMIC URLS
                                        $fileLink = $file->file_path;
                                        $fileTarget = '_blank';
                                        $downloadUrl = $isPublic
                                            ? route('files.shared_public_download', ['hash' => $shareHash, 'file' => $file->id])
                                            : route('files.download', ['file' => $file->id]);

                                        if ($file->type === 2) {
                                            $fileLink = $isPublic
                                                ? route('files.shared_public', ['hash' => $shareHash, 'folder_id' => $file->id])
                                                : '?folder=' . $file->id;
                                            $fileTarget = '_self';
                                            $downloadUrl = $isPublic
                                                ? route('files.shared_public_zip', ['hash' => $shareHash, 'folder' => $file->id])
                                                : route('files.zip', ['folder' => $file->id]);
                                        } elseif ($file->type === 0 || $file->type === null) {
                                            $fileLink = $storageUrl . '/' . ltrim($file->file_path, '/');
                                        }

                                        $thumbnailSrc = asset('/files/file-icons/'.$icon);
                                        $imgClass = 'file-icon-only';
                                    @endphp

                                    <tr class="file cursor-pointer"
                                        draggable="{{ $isAdmin ? 'true' : 'false' }}"
                                        data-id="{{ $file->id }}"
                                        data-type="{{ $file->type }}"
                                        data-name="{{ $file->file_name }}"
                                        data-link="{{ $fileLink }}"
                                        data-target="{{ $fileTarget }}"
                                        data-download-url="{{ $downloadUrl }}"
                                        data-access-url="{{ route('files.toggle-access', [$location, $file->id]) }}"
                                        data-share-hash="{{ $file->share_hash ?? '' }}"
                                        data-share-permission="{{ $file->share_permission ?? 'read' }}"
                                        data-icon="{{ asset('/files/file-icons/'.$icon) }}"
                                        data-is-image="{{ $isImage ? 'true' : 'false' }}"
                                        data-is-video="{{ $isVideo ? 'true' : 'false' }}"
                                        data-is-audio="{{ $isAudio ? 'true' : 'false' }}"
                                        data-is-pdf="{{ $isPdf ? 'true' : 'false' }}"
                                        data-is-office="{{ $isOffice ? 'true' : 'false' }}">

                                        @if($isAdmin)
                                            <td class="checkbox-cell" style="padding-left: 1rem;">
                                                <input type="checkbox" class="form-check-input file-checkbox rounded-1 cursor-pointer" value="{{ $file->id }}">
                                            </td>
                                        @endif
                                        <td class="icon-cell">
                                            <img alt="icon" src="{{ $thumbnailSrc }}" class="{{ $imgClass }}" loading="lazy">
                                        </td>
                                        <td class="name-cell fw-medium text-dark" title="{{ $file->file_name }}">
                                            {{ $file->file_name }}
                                            @if(!$isPublic && !empty($file->share_hash))
                                                <span class="material-symbols-rounded text-success fs-6 ms-2 align-middle share-badge" title="Gedeeld met publieke link" data-bs-toggle="tooltip">link</span>
                                            @endif
                                        </td>
                                        <td class="size-cell d-none d-md-table-cell text-muted small">
                                            @if(isset($file->file_path) && $file->file_path !== "" && Storage::disk('public')->exists($file->file_path))
                                                {{ number_format(Storage::disk('public')->size($file->file_path) / 1024 / 1024, 2) }} MB
                                            @endif
                                        </td>
                                        @if($isAdmin)
                                            <td class="date-cell d-none d-lg-table-cell text-muted small">{{ $file->updated_at->format('d M Y H:i') }}</td>
                                            @if($hasAdminViewers && !$isPublic)
                                                <td class="access-cell small">
                                                    @if($file->access === 'teachers')
                                                        <span class="badge bg-danger rounded-pill px-3 py-1 fw-medium">{{ $adminName }}</span>
                                                    @else
                                                        <span class="badge bg-success rounded-pill px-3 py-1 fw-medium">Iedereen</span>
                                                    @endif
                                                </td>
                                            @endif
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert bg-light text-center py-5 mt-4 rounded-4 border d-flex flex-column align-items-center justify-content-center" role="alert">
                    <span class="material-symbols-rounded fs-1 text-secondary mb-3" style="font-size: 3rem !important;">folder_open</span>
                    <h5 class="text-dark fw-bold">Deze map is nog leeg</h5>
                    <p class="text-muted mb-0">Sleep bestanden hierheen of gebruik de knop rechtsboven om iets toe te voegen.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const getModal = (id) => {
            const el = document.getElementById(id);
            if (!el) return null;
            if (typeof bootstrap !== 'undefined') return bootstrap.Modal.getOrCreateInstance(el);
            return { show: () => el.classList.add('show', 'd-block'), hide: () => el.classList.remove('show', 'd-block') };
        };

        const openFolders = JSON.parse(localStorage.getItem('fm_open_folders') || '[]');
        const activePathIds = @json($activePathIds);

        activePathIds.forEach(id => {
            if(!openFolders.includes(id)) openFolders.push(id);
        });

        openFolders.forEach(id => {
            const ul = document.getElementById('folder-children-' + id);
            const icon = document.querySelector(`.toggle-children[data-target="${id}"]`);
            if (ul) ul.style.display = 'block';
            if (icon) icon.textContent = 'expand_more';
        });

        document.querySelectorAll('.toggle-children').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault(); e.stopPropagation();
                const targetId = toggle.getAttribute('data-target');
                const ul = document.getElementById('folder-children-' + targetId);
                if (ul) {
                    const isHidden = ul.style.display === 'none';
                    ul.style.display = isHidden ? 'block' : 'none';
                    toggle.textContent = isHidden ? 'expand_more' : 'chevron_right';

                    let currentOpen = JSON.parse(localStorage.getItem('fm_open_folders') || '[]');
                    if (isHidden) { if (!currentOpen.includes(parseInt(targetId))) currentOpen.push(parseInt(targetId)); }
                    else { currentOpen = currentOpen.filter(id => id !== parseInt(targetId)); }
                    localStorage.setItem('fm_open_folders', JSON.stringify(currentOpen));
                }
            });
        });

        window.customAlert = function(message) {
            const msgEl = document.getElementById('customAlertMessage');
            if(msgEl) msgEl.textContent = message;
            const modalInstance = getModal('customAlertModal');
            if (modalInstance) modalInstance.show();
        };

        window.customConfirm = function(message, onConfirmCallback) {
            const msgEl = document.getElementById('customConfirmMessage');
            if(msgEl) msgEl.textContent = message;
            const confirmBtn = document.getElementById('customConfirmBtn');
            if(confirmBtn) {
                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                newConfirmBtn.addEventListener('click', () => {
                    const modalInstance = getModal('customConfirmModal');
                    if (modalInstance) modalInstance.hide();
                    if (typeof onConfirmCallback === 'function') onConfirmCallback();
                });
            }
            const modalInstance = getModal('customConfirmModal');
            if (modalInstance) modalInstance.show();
        };

        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const modalEl = this.closest('.modal');
                if (modalEl) {
                    const modalInstance = getModal(modalEl.id);
                    if (modalInstance) modalInstance.hide();
                    if (modalEl.id === 'previewModal') closeAndCleanPreview();
                }
            });
        });

        const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');
        const fileBrowserContainer = document.getElementById('file-browser-container');

        if(fileBrowserContainer && viewToggleBtns.length > 0) {
            const savedView = localStorage.getItem('fileManagerView') || 'list';
            setViewMode(savedView);

            viewToggleBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    setViewMode(btn.dataset.view);
                });
            });

            function setViewMode(view) {
                if(view === 'grid') fileBrowserContainer.classList.add('view-mode-grid');
                else fileBrowserContainer.classList.remove('view-mode-grid');
                localStorage.setItem('fileManagerView', view);
                viewToggleBtns.forEach(b => {
                    if(b.dataset.view === view) {
                        b.classList.add('active');
                        b.querySelector('span').classList.remove('text-secondary');
                    } else {
                        b.classList.remove('active');
                        b.querySelector('span').classList.add('text-secondary');
                    }
                });
            }
        }

        window.openUploadModal = function(tabId) {
            const tabBtn = document.getElementById(tabId);
            if (typeof bootstrap !== 'undefined' && tabBtn) {
                new bootstrap.Tab(tabBtn).show();
            } else if(tabBtn) {
                document.querySelectorAll('#uploadTabs .nav-link').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('#uploadModal .tab-pane').forEach(p => p.classList.remove('show', 'active'));
                tabBtn.classList.add('active');
                document.getElementById(tabBtn.dataset.bsTarget.replace('#','')).classList.add('show', 'active');
            }
            const modalInstance = getModal('uploadModal');
            if (modalInstance) modalInstance.show();
        };

        let uploadOptionRadios = document.querySelectorAll('input[name="upload-option"]');
        let filesUploadContainer = document.getElementById('files-upload-container');
        let folderUploadContainer = document.getElementById('folder-upload-container');
        let uploadTypeInput = document.getElementById('upload-type');
        let filesInput = document.getElementById('files-input');
        let folderInput = document.getElementById('folder-input');
        let uploadButton = document.getElementById('upload-button');
        let uploadForm = document.getElementById('upload-form');
        let progressContainer = document.querySelector('.progress');
        let progressBar = document.querySelector('.progress-bar');
        let uploadStatus = document.getElementById('upload-status');
        let xhr = null;

        if (uploadOptionRadios.length > 0) {
            uploadOptionRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'files') {
                        filesUploadContainer.classList.remove('d-none');
                        folderUploadContainer.classList.add('d-none');
                        uploadTypeInput.value = '0';
                    } else {
                        filesUploadContainer.classList.add('d-none');
                        folderUploadContainer.classList.remove('d-none');
                        uploadTypeInput.value = '3';
                    }
                });
            });
        }

        if (uploadButton !== null) {
            uploadButton.addEventListener('click', function () {
                let files = [];
                let isFolderUpload = uploadTypeInput.value === '3';
                let inputElement;

                if (isFolderUpload) {
                    inputElement = folderInput;
                    files = inputElement.files;
                    if (files.length === 0) {
                        uploadStatus.style.display = 'block';
                        uploadStatus.textContent = 'Gebruik de "Map" tab om een lege map aan te maken.';
                        return;
                    }
                } else {
                    inputElement = filesInput;
                    files = inputElement.files;
                    if (files.length === 0) {
                        uploadStatus.style.display = 'block';
                        uploadStatus.textContent = 'Selecteer bestanden om te uploaden.';
                        return;
                    }
                }

                this.disabled = true;
                this.querySelector('.button-text').style.display = 'none';
                this.querySelector('.loading-spinner').style.display = 'inline-block';
                this.querySelector('.loading-text').style.display = 'inline-block';

                let formData = new FormData(uploadForm);
                let totalSize = 0;
                let filePaths = [];

                formData.delete('file[]');
                formData.delete('folder_upload[]');

                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    totalSize += file.size;

                    if (isFolderUpload) {
                        formData.append('folder_upload[]', file);
                        filePaths.push(file.webkitRelativePath);
                    } else {
                        formData.append('file[]', file);
                    }
                }

                if (isFolderUpload) {
                    formData.set('folder_paths', JSON.stringify(filePaths));
                }

                progressContainer.style.display = 'flex';
                uploadStatus.style.display = 'block';
                progressBar.style.width = '0%';
                uploadStatus.textContent = `0 MB / ${(totalSize / 1024 / 1024).toFixed(2)} MB`;

                xhr = new XMLHttpRequest();
                // USE DYNAMIC UPLOAD URL
                xhr.open('POST', "{{ $uploadUrl }}", true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        let uploaded = (e.loaded / 1024 / 1024).toFixed(2);
                        let total = (e.total / 1024 / 1024).toFixed(2);
                        progressBar.style.width = `${(e.loaded / e.total) * 100}%`;
                        uploadStatus.textContent = `${uploaded} MB / ${total} MB`;
                    }
                });

                xhr.onload = function () {
                    const button = document.getElementById('upload-button');
                    if (xhr.status === 200 || xhr.status === 201) {
                        location.reload();
                    } else {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            uploadStatus.classList.replace('text-primary', 'text-danger');
                            uploadStatus.textContent = response.error || response.message || 'Fout tijdens uploaden.';
                        } catch (e) {
                            uploadStatus.classList.replace('text-primary', 'text-danger');
                            uploadStatus.textContent = 'Onbekende fout.';
                        }
                    }
                    button.disabled = false;
                    button.querySelector('.button-text').style.display = 'inline-block';
                    button.querySelector('.loading-spinner').style.display = 'none';
                    button.querySelector('.loading-text').style.display = 'none';
                };

                xhr.onerror = function () {
                    uploadStatus.classList.replace('text-primary', 'text-danger');
                    uploadStatus.textContent = 'Netwerkfout.';
                    const button = document.getElementById('upload-button');
                    button.disabled = false;
                    button.querySelector('.button-text').style.display = 'inline-block';
                    button.querySelector('.loading-spinner').style.display = 'none';
                    button.querySelector('.loading-text').style.display = 'none';
                };

                xhr.send(formData);
            });
        }

        let selectedFiles = [];
        const contextMenu = document.getElementById('context-menu');

        function updateSelectionUI() {
            const countText = document.getElementById('selected-count');
            const num = selectedFiles.length;
            if(countText) countText.textContent = `${num} item(s) geselecteerd`;

            document.querySelectorAll('.batch-multi-action').forEach(btn => btn.disabled = num === 0);
            document.querySelectorAll('.batch-single-action').forEach(btn => btn.disabled = num !== 1);

            document.querySelectorAll('tr.file').forEach(row => {
                const cb = row.querySelector('.file-checkbox');
                if (cb) {
                    cb.checked = selectedFiles.includes(row.dataset.id);
                    if (cb.checked) row.classList.add('table-primary');
                    else row.classList.remove('table-primary');
                }
            });

            const selectAll = document.getElementById('selectAll');
            const totalRows = document.querySelectorAll('tr.file').length;
            if(selectAll) selectAll.checked = (num > 0 && num === totalRows);
        }

        const selectAllInput = document.getElementById('selectAll');
        if(selectAllInput) {
            selectAllInput.addEventListener('change', (e) => {
                if (e.target.checked) selectedFiles = Array.from(document.querySelectorAll('tr.file')).map(row => row.dataset.id);
                else selectedFiles = [];
                updateSelectionUI();
            });
        }

        document.querySelectorAll('.file-checkbox').forEach(cb => {
            cb.addEventListener('change', function(e) {
                const id = this.value;
                if (this.checked && !selectedFiles.includes(id)) selectedFiles.push(id);
                else if (!this.checked) selectedFiles = selectedFiles.filter(fid => fid !== id);
                updateSelectionUI();
            });
        });

        document.querySelectorAll('tr.file').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.closest('.checkbox-cell') || e.target.tagName.toLowerCase() === 'input') return;

                const link = row.dataset.link;
                const target = row.dataset.target || "_self";

                if(row.dataset.type === '2') {
                    window.location.href = link;
                    return;
                }

                if (row.dataset.isImage === 'true' || row.dataset.isVideo === 'true' || row.dataset.isAudio === 'true' || row.dataset.isPdf === 'true' || row.dataset.isOffice === 'true') {
                    e.preventDefault();
                    openPreviewGallery(row);
                    return;
                }
                window.open(link, target);
            });

            if(contextMenu) {
                row.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    if (!selectedFiles.includes(row.dataset.id)) {
                        selectedFiles = [row.dataset.id];
                        updateSelectionUI();
                    }
                    contextMenu.style.top = `${e.pageY}px`;
                    contextMenu.style.left = `${e.pageX}px`;
                    contextMenu.style.display = 'block';
                });
            }
        });

        document.addEventListener('click', () => { if(contextMenu) contextMenu.style.display = 'none'; });

        document.querySelectorAll('tr.file').forEach(row => {
            if(row.getAttribute('draggable') !== 'true') return;
            row.addEventListener('dragstart', (e) => {
                if(!selectedFiles.includes(row.dataset.id)) {
                    selectedFiles = [row.dataset.id];
                    updateSelectionUI();
                }
                e.dataTransfer.setData('text/plain', JSON.stringify(selectedFiles));
                e.dataTransfer.effectAllowed = 'move';
            });

            if(row.dataset.type == '2') {
                row.addEventListener('dragover', (e) => { e.preventDefault(); row.classList.add('drag-over'); });
                row.addEventListener('dragleave', (e) => { row.classList.remove('drag-over'); });
                row.addEventListener('drop', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    row.classList.remove('drag-over');
                    try {
                        const draggedIds = JSON.parse(e.dataTransfer.getData('text/plain'));
                        if(draggedIds.includes(row.dataset.id)) {
                            customAlert('Je kunt een map niet naar zichzelf verplaatsen.');
                            return;
                        }
                        executeBatchAjax('move', draggedIds, row.dataset.id, null);
                    } catch(err) {}
                });
            }
        });

        const shareToggle = document.getElementById('shareToggleSwitch');
        const shareSelect = document.getElementById('sharePermissionSelect');
        const shareLinkInput = document.getElementById('shareLinkInput');
        const shareRadios = document.querySelectorAll('input[name="shareMode"]');
        const shareLoadingState = document.getElementById('shareLoadingState');
        const shareContentState = document.getElementById('shareContentState');
        const shareSettingsContainer = document.getElementById('shareSettingsContainer');

        const updateShareSettings = () => {
            const fileIds = document.getElementById('shareFileId').value.split(',');
            const isShared = shareToggle.checked;
            const permission = shareSelect.value;
            const shareMode = document.querySelector('input[name="shareMode"]:checked').value;

            // Show loading state
            if(shareContentState && shareLoadingState) {
                shareContentState.classList.add('opacity-50');
                shareContentState.style.pointerEvents = 'none';
            }

            fetch(`{{ $batchUrl }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: 'share',
                    file_ids: fileIds,
                    is_shared: isShared,
                    share_permission: permission,
                    share_mode: shareMode,
                    _token: '{{ csrf_token() }}'
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        fileIds.forEach(id => {
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if(!row) return;
                            let nameCell = row.querySelector('.name-cell');

                            if (isShared && data.share_hash) {
                                row.dataset.shareHash = data.share_hash;
                                row.dataset.sharePermission = permission;
                                if (!nameCell.querySelector('.share-badge')) {
                                    nameCell.innerHTML += ` <span class="material-symbols-rounded text-success fs-6 ms-2 align-middle share-badge" title="Gedeeld met publieke link" data-bs-toggle="tooltip">link</span>`;
                                }
                            } else {
                                row.dataset.shareHash = '';
                                row.dataset.sharePermission = 'read';
                                if (nameCell.querySelector('.share-badge')) nameCell.querySelector('.share-badge').remove();
                            }
                        });

                        if (isShared && data.share_hash) {
                            shareSettingsContainer.classList.remove('d-none');
                            shareLinkInput.value = `{{ url('/bestanden/gedeeld') }}/` + data.share_hash;
                        } else {
                            shareSettingsContainer.classList.add('d-none');
                        }
                    } else {
                        customAlert(data.error || 'Fout bij het opslaan van deel-instellingen.');
                        shareToggle.checked = !isShared;
                    }
                })
                .catch(err => customAlert('Netwerkfout.'))
                .finally(() => {
                    if(shareContentState && shareLoadingState) {
                        shareContentState.classList.remove('opacity-50');
                        shareContentState.style.pointerEvents = 'auto';
                    }
                });
        };

        if(shareToggle) shareToggle.addEventListener('change', updateShareSettings);
        if(shareSelect) shareSelect.addEventListener('change', updateShareSettings);
        if(shareRadios) shareRadios.forEach(radio => radio.addEventListener('change', updateShareSettings));

        document.getElementById('copyShareLinkBtn')?.addEventListener('click', () => {
            if(!shareLinkInput.value) return;
            shareLinkInput.select();
            shareLinkInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(shareLinkInput.value).then(() => {
                const btn = document.getElementById('copyShareLinkBtn');
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<span class="material-symbols-rounded fs-5 text-success">check</span>';
                setTimeout(() => btn.innerHTML = origHtml, 2000);
            });
        });

        window.handleMenuAction = function(action) {
            if(selectedFiles.length === 0) return;

            if (action === 'download') {
                selectedFiles.forEach((id, index) => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row && row.dataset.downloadUrl) {
                        setTimeout(() => {
                            const a = document.createElement('a');
                            a.href = row.dataset.downloadUrl;
                            a.download = row.dataset.name || 'download';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        }, index * 400);
                    }
                });
                return;
            }

            if (action === 'share') {
                document.getElementById('shareFileId').value = selectedFiles.join(',');
                let hash = '';
                let perm = 'read';

                // Kijk NAAR DE ACTUELE DOM (niet naar de db, die weerspiegelt dit al)
                for(let i=0; i<selectedFiles.length; i++) {
                    const r = document.querySelector(`tr[data-id="${selectedFiles[i]}"]`);
                    if(r && r.dataset.shareHash) {
                        hash = r.dataset.shareHash;
                        perm = r.dataset.sharePermission || 'read';
                        break;
                    }
                }

                // Reset UI state gebaseerd op DOM data
                document.getElementById('shareModeMerge').checked = true;

                if (hash && hash.trim() !== '') {
                    shareToggle.checked = true;
                    shareSettingsContainer.classList.remove('d-none');
                    shareSelect.value = perm;
                    shareLinkInput.value = `{{ url('/bestanden/gedeeld') }}/` + hash;
                } else {
                    shareToggle.checked = false;
                    shareSettingsContainer.classList.add('d-none');
                    shareSelect.value = 'read';
                    shareLinkInput.value = '';
                }

                const modalInstance = getModal('shareModal');
                if (modalInstance) modalInstance.show();
                return;
            }

            if (action === 'toggle-access') {
                customConfirm(`Weet u zeker dat u de rechten van ${selectedFiles.length} item(s) wilt wijzigen?`, () => {
                    const btn = document.querySelector('button[onclick="handleMenuAction(\'toggle-access\')"]');
                    if(btn) {
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Laden...';
                        btn.disabled = true;
                    }
                    let promises = selectedFiles.map(id => {
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if(row && row.dataset.accessUrl) {
                            return fetch(row.dataset.accessUrl, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        }
                        return Promise.resolve();
                    });
                    Promise.all(promises).then(() => location.reload()).catch(() => customAlert('Fout opgetreden.'));
                });
                return;
            }

            if(action === 'delete') {
                customConfirm(`Weet u zeker dat u ${selectedFiles.length} item(s) wilt verwijderen? Dit is definitief.`, () => {
                    executeBatchAjax('delete', selectedFiles);
                });
                return;
            }

            document.getElementById('batchActionType').value = action;
            const container = document.getElementById('batchActionInputContainer');
            const title = document.getElementById('batchActionTitle');
            container.innerHTML = '';

            if (action === 'rename') {
                title.textContent = 'Bestand/Map Hernoemen';
                const row = document.querySelector(`tr[data-id="${selectedFiles[0]}"]`);
                container.innerHTML = `
                    <label class="form-label text-muted small text-uppercase fw-bold">Nieuwe naam</label>
                    <input type="text" class="form-control rounded-3" id="batchActionNewName" value="${row.dataset.name}">
                `;
            } else if (action === 'move' || action === 'copy') {
                title.textContent = action === 'move' ? 'Verplaatsen naar...' : 'Kopiëren naar...';
                container.innerHTML = `
                    <label class="form-label text-muted small text-uppercase fw-bold">Selecteer doelmap</label>
                    <select class="form-select rounded-3" id="batchActionTargetFolder">
                        <option value="">- Hoofdmap -</option>
                        {!! $renderOptions(null, 1) !!}
                </select>
`;
            }
            const modalInstance = getModal('batchActionModal');
            if (modalInstance) modalInstance.show();
        };

        document.getElementById('batchActionSubmit')?.addEventListener('click', () => {
            const action = document.getElementById('batchActionType').value;
            const newName = document.getElementById('batchActionNewName')?.value;
            const targetFolder = document.getElementById('batchActionTargetFolder')?.value;

            const btn = document.getElementById('batchActionSubmit');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Even geduld...';
            btn.disabled = true;

            executeBatchAjax(action, selectedFiles, targetFolder, newName);
        });

        function executeBatchAjax(action, fileIds, targetFolderId = null, newName = null) {
            fetch(`{{ $batchUrl }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action, file_ids: fileIds, target_folder_id: targetFolderId, new_name: newName, _token: '{{ csrf_token() }}' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        customAlert(data.error || 'Er is een interne fout opgetreden.');
                        resetBatchBtn();
                    }
                }).catch(err => {
                customAlert('Netwerkfout bij communicatie met de server.');
                resetBatchBtn();
            });
        }

        function resetBatchBtn() {
            const btn = document.getElementById('batchActionSubmit');
            if(btn) { btn.innerHTML = 'Bevestigen'; btn.disabled = false; }
            const modalInstance = getModal('batchActionModal');
            if (modalInstance) modalInstance.hide();
        }

        let previewableFilesList = [];
        let currentPreviewIndex = -1;

        document.querySelectorAll('tr.file').forEach((row) => {
            const isMedia = row.dataset.isImage === 'true' || row.dataset.isVideo === 'true' || row.dataset.isAudio === 'true' || row.dataset.isPdf === 'true' || row.dataset.isOffice === 'true';
            if (isMedia) previewableFilesList.push(row);
        });

        function closeAndCleanPreview() {
            const container = document.getElementById('previewContainer');
            if (container) {
                const mediaElements = container.querySelectorAll('audio, video, iframe');
                mediaElements.forEach(media => {
                    if (media.pause) media.pause();
                    media.removeAttribute('src');
                    if (media.load) media.load();
                });
                container.innerHTML = '';
            }
            document.body.classList.remove('preview-open');
        }

        function loadPreviewContent(index) {
            if(index < 0 || index >= previewableFilesList.length) return;

            closeAndCleanPreview();
            currentPreviewIndex = index;

            const row = previewableFilesList[index];
            const link = row.dataset.link;
            const title = row.dataset.name;
            const customIconUrl = row.dataset.icon;
            const container = document.getElementById('previewContainer');

            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewDownloadBtn').href = row.dataset.downloadUrl || link;
            document.getElementById('previewCounter').textContent = `${index + 1} / ${previewableFilesList.length}`;

            container.innerHTML = '<div class="spinner-border text-white" role="status"></div>';

            document.getElementById('previewPrev').style.display = index > 0 ? 'block' : 'none';
            document.getElementById('previewNext').style.display = index < previewableFilesList.length - 1 ? 'block' : 'none';

            if (row.dataset.isImage === 'true') {
                container.innerHTML = `<img src="${link}" class="img-fluid" style="max-height: 85vh; max-width: 100%; object-fit: contain;">`;
            } else if (row.dataset.isPdf === 'true') {
                container.innerHTML = `
                    <div class="w-100 h-100" style="max-height: 85vh; overflow-y: auto; -webkit-overflow-scrolling: touch;">
                        <iframe src="${link}#toolbar=0" class="w-100 h-100 border-0" style="min-height: 85vh; display: block;"></iframe>
                    </div>`;
            } else if (row.dataset.isVideo === 'true') {
                container.innerHTML = `<video controls autoplay class="w-100 bg-black" style="max-height: 85vh;"><source src="${link}"></video>`;
            } else if (row.dataset.isAudio === 'true') {
                container.innerHTML = `
                    <div class="p-5 rounded-4 bg-white border w-100 d-flex flex-column align-items-center" style="max-width: 400px;">
                        <img src="${customIconUrl}" alt="Audio Icon" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 1.5rem;">
                        <audio controls autoplay class="w-100"><source src="${link}"></audio>
                    </div>`;
            } else if (row.dataset.isOffice === 'true') {
                container.innerHTML = `
                    <div class="d-flex flex-column align-items-center justify-content-center bg-white border rounded-4 w-100 p-5" style="max-width: 600px;">
                        <img src="${customIconUrl}" alt="Office Icon" style="width: 100px; height: 100px; object-fit: contain; margin-bottom: 1.5rem;">
                        <h4 class="fw-bold text-dark">Office Bestand</h4>
                        <p class="text-muted w-75 mx-auto">Office bestanden kunnen niet direct in de browser worden bekeken. Gebruik de knop hieronder om het bestand lokaal te openen.</p>
                        <a href="${row.dataset.downloadUrl || link}" class="btn btn-primary text-white mt-3 d-flex align-items-center fw-medium">
                            <span class="material-symbols-rounded me-2 fs-5">download</span> Nu Downloaden
                        </a>
                    </div>
                `;
            }
        }

        function openPreviewGallery(row) {
            const index = previewableFilesList.findIndex(r => r === row);
            if(index !== -1) {
                document.body.classList.add('preview-open');
                const modalInstance = getModal('previewModal');
                if (modalInstance) modalInstance.show();
                loadPreviewContent(index);
            }
        }

        document.getElementById('previewPrev')?.addEventListener('click', (e) => { e.stopPropagation(); loadPreviewContent(currentPreviewIndex - 1); });
        document.getElementById('previewNext')?.addEventListener('click', (e) => { e.stopPropagation(); loadPreviewContent(currentPreviewIndex + 1); });

        document.addEventListener('keydown', function(e) {
            if (document.body.classList.contains('preview-open')) {
                if (e.key === 'ArrowLeft') {
                    const prevBtn = document.getElementById('previewPrev');
                    if (prevBtn && prevBtn.style.display !== 'none') prevBtn.click();
                } else if (e.key === 'ArrowRight') {
                    const nextBtn = document.getElementById('previewNext');
                    if (nextBtn && nextBtn.style.display !== 'none') nextBtn.click();
                } else if (e.key === 'Escape') {
                    const modalInstance = getModal('previewModal');
                    if (modalInstance) modalInstance.hide();
                    closeAndCleanPreview();
                }
            }
        });

        const previewBackdrop = document.getElementById('previewBackdrop');
        if(previewBackdrop) {
            previewBackdrop.addEventListener('click', function(e) {
                if (e.target === this || e.target.id === 'previewContainer') {
                    const modalInstance = getModal('previewModal');
                    if (modalInstance) modalInstance.hide();
                    closeAndCleanPreview();
                }
            });
        }

        document.getElementById('previewModal').addEventListener('hidden.bs.modal', closeAndCleanPreview);
    });
</script>
