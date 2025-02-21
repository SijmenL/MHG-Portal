@extends('layouts.lessons')


@section('content')
    <div id="popup-upload" class="popup d-none" style="margin-top: -122px">
        <div class="popup-body">

            <div class="tab-container no-scrolbar" style="overflow-x: auto;">
                <ul class="nav nav-tabs d-none" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1"
                                type="button" role="tab" aria-controls="tab1" aria-selected="true">Bestanden
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button"
                                role="tab" aria-controls="tab2" aria-selected="false">Hyperlink
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button"
                                role="tab" aria-controls="tab3" aria-selected="false">Map
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tab-content w-100 bg-light rounded p-4">
                <div class="tab-pane show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    <h2>Bestanden uploaden</h2>
                    <p class="text-center">Toegestane bestandstypes: <i>.pdf, .jpeg, .jpg, .webp, .png, .zip, .pptx,
                            .docx, .doc, .ppt, .mp4, .mov, .mp3, .wav, .xlsx</i></p>
                    <form id="upload-form" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-row gap-2 align-items-center justify-content-center">
                            <div class="form-group">
                                <label for="file">Kies bestanden om te uploaden</label>
                                <input type="file" name="file[]" multiple class="form-control" id="file-input">
                                <input type="hidden" name="type" value="0">
                                <input type="hidden" name="folder_id" value="{{ $folderId }}">
                            </div>
                            <div class="form-group">
                                <label for="access">Toegang</label>
                                <select name="access" class="form-control" id="access">
                                    <option value="all">Iedereen</option>
                                    <option value="teachers">Alleen praktijkbegeleiders</option>
                                </select>
                            </div>
                        </div>
                        <div class="progress mt-3" style="display: none;">
                            <div class="progress-bar progress-bar-striped bg-success progress-bar-animated h-100"
                                 role="progressbar" style="width: 0%;"></div>
                        </div>
                        <div id="upload-status" class="text-success mt-2" style="display: none;"></div>
                        <div class="button-container justify-content-center">
                            <button type="button" id="upload-button"
                                    class="btn btn-success text-white d-flex align-items-center justify-content-center">
                                <span class="button-text">Uploaden</span>
                                <span class="loading-spinner spinner-border spinner-border-sm" style="display: none;"
                                      aria-hidden="true"></span>
                                <span class="loading-text" style="display: none;" role="status">Laden...</span>
                            </button>
                            <a class="popup-upload-button-close btn btn-outline-danger">Annuleren</a>
                        </div>
                    </form>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        let uploadPopup = document.getElementById('popup-upload');
                        let uploadPopupButton = document.getElementById('popup-upload-button');
                        let hyperlinkPopupButton = document.getElementById('popup-hyperlink-button');
                        let mapPopupButton = document.getElementById('popup-map-button');
                        let uploadPopupButtonClose = document.getElementsByClassName('popup-upload-button-close');
                        let uploadForm = document.getElementById('upload-form');
                        let uploadButton = document.getElementById('upload-button');
                        let fileInput = document.getElementById('file-input');
                        let progressBar = document.querySelector('.progress-bar');
                        let progressContainer = document.querySelector('.progress');
                        let uploadStatus = document.getElementById('upload-status');
                        let xhr = null; // Declare xhr globally so it can be canceled

                        console.log(uploadPopupButtonClose)

                        uploadPopupButton.addEventListener('click', function () {
                            uploadPopup.classList.remove('d-none');

                            document.getElementById('tab1-tab').classList.add('active');
                            document.getElementById('tab1').classList.add('show', 'active');

                            document.getElementById('tab2-tab').classList.remove('active');
                            document.getElementById('tab2').classList.remove('show', 'active');
                            document.getElementById('tab3-tab').classList.remove('active');
                            document.getElementById('tab3').classList.remove('show', 'active');
                        });

                        hyperlinkPopupButton.addEventListener('click', function () {
                            uploadPopup.classList.remove('d-none');

                            document.getElementById('tab2-tab').classList.add('active');
                            document.getElementById('tab2').classList.add('show', 'active');

                            document.getElementById('tab1-tab').classList.remove('active');
                            document.getElementById('tab1').classList.remove('show', 'active');
                            document.getElementById('tab3-tab').classList.remove('active');
                            document.getElementById('tab3').classList.remove('show', 'active');
                        });

                        mapPopupButton.addEventListener('click', function () {
                            uploadPopup.classList.remove('d-none');

                            document.getElementById('tab3-tab').classList.add('active');
                            document.getElementById('tab3').classList.add('show', 'active');

                            document.getElementById('tab1-tab').classList.remove('active');
                            document.getElementById('tab1').classList.remove('show', 'active');
                            document.getElementById('tab2-tab').classList.remove('active');
                            document.getElementById('tab2').classList.remove('show', 'active');
                        });


                        // Close the upload popup and reset everything
                        Array.from(uploadPopupButtonClose).forEach(function (button) {
                            button.addEventListener('click', function () {
                                if (xhr) {
                                    xhr.abort(); // Cancel the ongoing upload
                                }
                                uploadPopup.classList.add('d-none');
                                progressContainer.style.display = 'none';
                                progressBar.style.width = '0%';
                                uploadStatus.textContent = '';
                                uploadForm.reset(); // Reset the form

                                uploadButton.disabled = false;
                                uploadButton.classList.remove('loading');

                                // Show the spinner and hide the text
                                uploadButton.querySelector('.button-text').style.display = 'inline-block';
                                uploadButton.querySelector('.loading-spinner').style.display = 'none';
                                uploadButton.querySelector('.loading-text').style.display = 'none';
                            });
                        });


                        // Handle file upload
                        uploadButton.addEventListener('click', function () {
                            if (fileInput.files.length === 0) {
                                uploadStatus.style.display = 'block';
                                uploadStatus.textContent = 'Selecteer bestanden om te uploaden.';
                                return;
                            }

                            uploadButton.disabled = true;
                            uploadButton.classList.add('loading');

                            // Show the spinner and hide the text
                            uploadButton.querySelector('.button-text').style.display = 'none';
                            uploadButton.querySelector('.loading-spinner').style.display = 'inline-block';
                            uploadButton.querySelector('.loading-text').style.display = 'inline-block';

                            let formData = new FormData(uploadForm);
                            progressContainer.style.display = 'block';
                            uploadStatus.style.display = 'block';
                            progressBar.style.width = '0%';
                            uploadStatus.textContent = '0 MB / 0 MB';

                            xhr = new XMLHttpRequest();
                            xhr.open('POST', "{{ route('lessons.environment.lesson.files.store', $lesson->id) }}", true);
                            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                            // Update progress bar with size
                            xhr.upload.addEventListener('progress', function (e) {
                                if (e.lengthComputable) {
                                    let uploaded = (e.loaded / 1024 / 1024).toFixed(2); // Convert to MB
                                    let total = (e.total / 1024 / 1024).toFixed(2); // Convert to MB
                                    progressBar.style.width = `${(e.loaded / e.total) * 100}%`;
                                    uploadStatus.textContent = `${uploaded} MB / ${total} MB`;
                                }
                            });

                            // Handle upload success or failure
                            xhr.onload = function () {
                                if (xhr.status === 200) {
                                    progressContainer.style.display = 'none';
                                    progressBar.style.width = '0%';
                                    uploadStatus.textContent = '';
                                    uploadPopup.classList.add('d-none');
                                    uploadForm.reset();
                                    location.reload(); // Optionally reload to reflect new files
                                } else {
                                    uploadStatus.textContent = 'Er is iets misgegaan tijdens het uploaden.';
                                }
                            };

                            xhr.onerror = function () {
                                uploadStatus.textContent = 'Er is een netwerkfout opgetreden.';
                            };

                            xhr.send(formData);
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function () {
                        // Select all <tr> elements with the class "file"
                        const rows = document.querySelectorAll('tr.file');

                        rows.forEach(function (row) {
                            // Get the link and target from the data attributes
                            const link = row.getAttribute('data-link');
                            let target = row.getAttribute('data-target');

                            // Trim any leading/trailing spaces from the target
                            target = target ? target.trim() : "_self";

                            // If target is the string "null", change it to "_self"
                            if (target === "null") {
                                target = "_self";
                            }

                            // Add a click event listener to each row
                            row.addEventListener('click', function (event) {
                                // If the click happened inside an element with class 'has-dropdown', do nothing
                                if (event.target.closest('.has-dropdown')) {
                                    return;
                                }

                                // Open the link in the specified target
                                window.open(link, target);
                            });
                        });
                    });


                </script>

                <div class="text-start tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    <h2 class="text-center">Hyperlink toevoegen</h2>
                    <p class="text-center">Vul een url in waarvan we een link kunnen maken</p>
                    <form method="post" action="{{ route('lessons.environment.lesson.files.store', $lesson->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-row-responsive w-100 gap-2 align-items-center justify-content-center">
                            <div class="form-group w-100">
                                <label for="file">Vul een url in</label>
                                <input type="text" name="file" class="form-control"> <!-- Not an array -->
                                <input type="hidden" name="type" value="1">
                                <input type="hidden" name="folder_id" value="{{ $folderId }}">
                            </div>
                            <div class="form-group w-100">
                                <label for="title">Weergavenaam</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                            <div class="form-group w-100">
                                <label for="access">Toegang</label>
                                <select name="access" class="form-control" id="access">
                                    <option value="all">Iedereen</option>
                                    <option value="teachers">Alleen praktijkbegeleiders</option>
                                </select>
                            </div>
                        </div>

                        <div class="button-container justify-content-center">
                            <button type="submit"
                                    class="btn btn-success text-white d-flex align-items-center justify-content-center">
                                <span class="button-text">Hyperlink toevoegen</span>
                                <span class="loading-spinner spinner-border spinner-border-sm" style="display: none;"
                                      aria-hidden="true"></span>
                                <span class="loading-text" style="display: none;" role="status">Laden...</span>
                            </button>
                            <a class="popup-upload-button-close btn btn-outline-danger">Annuleren</a>
                        </div>
                    </form>
                </div>

                <div class="text-start tab-pane" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    <h2 class="text-center">Map toevoegen</h2>
                    <p class="text-center">Vul de naam van je map in</p>
                    <form method="post" action="{{ route('lessons.environment.lesson.files.store', $lesson->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-row-responsive w-100 gap-2 align-items-center justify-content-center">
                            <div class="form-group w-100">
                                <label for="title">Naam</label>
                                <input type="text" name="title" class="form-control">
                                <input type="hidden" name="type" value="2">
                                <input type="hidden" name="folder_id" value="{{ $folderId }}">
                            </div>
                            <div class="form-group w-100">
                                <label for="access">Toegang</label>
                                <select name="access" class="form-control" id="access">
                                    <option value="all">Iedereen</option>
                                    <option value="teachers">Alleen praktijkbegeleiders</option>
                                </select>
                            </div>
                        </div>

                        <div class="button-container justify-content-center">
                            <button type="submit"
                                    class="btn btn-success text-white d-flex align-items-center justify-content-center">
                                <span class="button-text">Map toevoegen</span>
                                <span class="loading-spinner spinner-border spinner-border-sm" style="display: none;"
                                      aria-hidden="true"></span>
                                <span class="loading-text" style="display: none;" role="status">Laden...</span>
                            </button>
                            <a class="popup-upload-button-close btn btn-outline-danger">Annuleren</a>
                        </div>
                    </form>
                </div>

            </div>


        </div>
    </div>


    <div class="container col-md-11">
        <div class="d-flex flex-row align-items-center justify-content-between">
            <h1>Bestanden</h1>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('lessons') }}">Lessen</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('lessons.environment.lesson', $lesson->id) }}">{{ $lesson->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Bestanden</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div>
            <div class="file-manager bg-light p-2 rounded">
                <div class="file-tools">
                    <div style="transform: translateY(9px)">
                        @if (!isset($folderId))
                            <h2 class="no-mobile">Lesbestanden</h2>
                        @else
                            <h2 class="no-mobile"><a>{{ last($breadcrumbs)['name'] }}</a></h2>
                        @endif

                        <div aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @if(isset($folderId))
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('lessons.environment.lesson.files', $lesson->id) }}">Lesbestanden</a>
                                    </li>
                                    @foreach ($breadcrumbs as $breadcrumb)
                                        @if (!$loop->last)
                                            <li class="breadcrumb-item">
                                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                                            </li>
                                        @else
                                            <li class="breadcrumb-item active">
                                                <a>{{ $breadcrumb['name'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ol>
                        </div>
                    </div>

                    @if($isTeacher)
                        <div class="dropdown d-flex flex-row-reverse" style="min-width: 50%">
                            <button
                                class="btn btn-outline-dark d-flex flex-row gap-2 align-items-center justify-content-center"
                                type="button" style="display: flex !important;"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="material-symbols-rounded">add</span> <span>Nieuw toevoegen</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a id="popup-map-button" class="dropdown-item">
                                        Map</a></li>
                                <li><a id="popup-upload-button" class="dropdown-item">
                                        Bestand uploaden</a></li>
                                <li><a id="popup-hyperlink-button" class="dropdown-item">
                                        Hyperlink toevoegen</a></li>
                            </ul>
                        </div>

                    @endif
                </div>

                @if(count($files) > 0)
                    <table class="table table-borderless">
                        <thead>
                        <tr class="bg-light">
                            <th class="bg-light"></th>
                            <th class="bg-light">Naam</th>
                            <th class="no-mobile bg-light">Bestandsgrootte</th>
                            @if($isTeacher)
                            <th class="no-mobile bg-light">Gewijzigd</th>
                                <th class="bg-light">Toegang</th>
                                <th class="bg-light">Opties</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($files as $file)
                            @if(!($file->access === 'teachers' && !$isTeacher))
                                @php
                                    // Get the file extension
                                    $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);

                                    // Set default icon
                                    $icon = 'unknown.webp';

                                    // Match the file extension to the correct icon
                                    if ($file->type === 0 || $file->type === null) {

                                    switch(strtolower($extension)) {
                                        case 'pdf':
                                            $icon = 'pdf.webp'; // PDF icon
                                            break;
                                        case 'jpeg':
                                        case 'jpg':
                                            $icon = 'jpg.webp'; // Image icon
                                            break;
                                            case 'png':
                                                $icon = 'png.webp';
                                                break;
                                                case 'webp':
                                                $icon = 'webp.webp';
                                                break;
                                        case 'zip':
                                            $icon = 'zip.webp'; // ZIP icon
                                            break;
                                        case 'docx':
                                        case 'doc':
                                            $icon = 'doc.webp'; // Word document icon
                                            break;
                                        case 'pptx':
                                        case 'ppt':
                                            $icon = 'ppt.webp'; // PowerPoint icon
                                            break;
                                        case 'mp4':
                                             $icon = 'mp4.webp'; // Video icon
                                            break;
                                        case 'mov':
                                            $icon = 'mov.webp'; // Video icon
                                            break;
                                        case 'mp3':
                                            $icon = 'mp3.webp'; // Audio file icon
                                            break;
                                        case 'wav':
                                            $icon = 'wav.webp'; // Audio file icon
                                            break;
                                        case 'xlsx':
                                            $icon = 'xlsx.webp'; // Excel icon
                                            break;
                                    }
    }

                                    if ($file->type === 1) {
                                        $icon = 'url.webp';
                                    }

                                    if ($file->type === 2) {
                                        $icon = 'folder.webp';
                                    }
                                @endphp
                                <tr class="file" style="cursor: pointer"
                                    data-link=" @if($file->type === 2)?folder={{ $file->id }} @elseif($file->type === 0 || $file->type === null) {{ Storage::url($file->file_path) }} @else{{ $file->file_path }} @endif"
                                    data-target="@if($file->type === 2) _self @else _blank @endif">
                                    <td>
                                        <img style="width: clamp(25px, 50px, 5vw)" class="m-0" alt="file"
                                             src="{{ asset('/files/lessons/file-icons/'.$icon) }}">
                                    </td>

                                    <td>
                                        <span>{{ $file->file_name }}</span>
                                    </td>


                                    <td class="no-mobile">
                                        @if(isset($file->file_path) && $file->file_path !== "")
                                            @if(Storage::disk('public')->exists($file->file_path))
                                                <span>{{ number_format(Storage::disk('public')->size($file->file_path) / 1024 / 1024, 2) }} MB</span>
                                            @endif
                                        @endif
                                    </td>

                                    @if($isTeacher)
                                    <td class="no-mobile">
                                        <span>{{ $file->updated_at }}</span>
                                    </td>
                                        <td>
                                            @if($file->access === 'teachers')
                                                <span>Praktijkbegeleiders</span>
                                            @else
                                                <span>Alle deelnemers</span>
                                            @endif
                                        </td>
                                    @endif

                                    @if($isTeacher)
                                        <td class="has-dropdown" style="cursor: default">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    Opties
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li class="w-100">
                                                        <form
                                                            action="{{ route('lessons.environment.lesson.files.destroy', [$lesson->id, $file->id]) }}"
                                                            method="POST" class="d-inline-block w-100">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">Verwijderen
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @if($file->access === 'teachers')
                                                        <li class="w-100"><a class="dropdown-item"
                                                                             href="{{ route('lessons.environment.lesson.files.toggle-access', [$lesson->id, $file->id]) }}">Maak
                                                                beschikbaar voor alle deelnemers</a></li>
                                                    @endif
                                                    @if($file->access === 'all' || $file->access === '')
                                                        <li class="w-100"><a class="dropdown-item"
                                                                             href="{{ route('lessons.environment.lesson.files.toggle-access', [$lesson->id, $file->id]) }}">Maak
                                                                alleen beschikbaar voor praktijkbegeleiders</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    @endif
                                </tr>

                            @endif
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                        <span class="material-symbols-rounded me-2">cloud_off</span>Er zijn nog geen bestanden
                        toegevoegd aan
                        deze map.
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
