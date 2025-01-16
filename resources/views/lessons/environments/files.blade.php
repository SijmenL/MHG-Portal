@extends('layouts.lessons')


@section('content')
    <div id="popup-upload" class="popup d-none" style="margin-top: -122px">
        <div class="popup-body">
            <h2>Upload naar de lesomgeving</h2>

            <div class="tab-container no-scrolbar" style="overflow-x: auto;">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
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
                </ul>
            </div>
            <div class="tab-content w-100 bg-light rounded p-4">
                <div class="tab-pane show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    <p class="text-center">Toegestane bestandstypes: <i>.pdf, .jpeg, .jpg, .webp, .png, .zip, .pptx,
                            .docx, .doc, .ppt, .mp4, .mov, .mp3, .wav, .xlsx</i></p>
                    <form id="upload-form" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-row gap-2 align-items-center justify-content-center">
                            <div class="form-group">
                                <label for="file">Kies bestanden om te uploaden</label>
                                <input type="file" name="file[]" multiple class="form-control" id="file-input">
                                <input type="hidden" name="type" value="0">
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
                        let uploadPopupButtonClose = document.getElementsByClassName('popup-upload-button-close');
                        let uploadForm = document.getElementById('upload-form');
                        let uploadButton = document.getElementById('upload-button');
                        let fileInput = document.getElementById('file-input');
                        let progressBar = document.querySelector('.progress-bar');
                        let progressContainer = document.querySelector('.progress');
                        let uploadStatus = document.getElementById('upload-status');
                        let xhr = null; // Declare xhr globally so it can be canceled

                        console.log(uploadPopupButtonClose)

                        // Show the upload popup
                        uploadPopupButton.addEventListener('click', function () {
                            uploadPopup.classList.remove('d-none');

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
                </script>

                <div class="text-start tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    <p class="text-center">Vul een url in waarvan we een link kunnen maken</p>
                    <form method="post" action="{{ route('lessons.environment.lesson.files.store', $lesson->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-row-responsive w-100 gap-2 align-items-center justify-content-center">
                            <div class="form-group w-100">
                                <label for="file">Vul een url in</label>
                                <input type="text" name="file" class="form-control"> <!-- Not an array -->
                                <input type="hidden" name="type" value="1">
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
                                <span class="button-text">Uploaden</span>
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
            @if($isTeacher)
                <a id="popup-upload-button"
                   class="btn btn-outline-dark">
                    Bestanden uploaden
                </a>
            @endif
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
            @if(count($files) > 0)
                <p>Klik op de bestanden om ze te downloaden of te bekijken.</p>
                <ul class="list-group mt-3">
                    @foreach($files as $file)
                        @if(!($file->access === 'teachers' && !$isTeacher))
                            <!-- Display file icon -->
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
                            @endphp
                            <li class="list-group-item d-flex flex-row gap-3 align-items-center justify-content-between">
                                <a class="text-decoration-none text-black d-flex flex-row gap-3 align-items-center justify-content-center"
                                   @if($file->type === 0 || $file->type === null)
                                   href="{{ Storage::url($file->file_path) }}"
                                   @else
                                   href="{{ $file->file_path }}"
                                   @endif
                                   target="_blank">
                                    <img style="width: clamp(25px, 50px, 5vw)" class="m-0" alt="file"
                                         src="{{ asset('/files/lessons/file-icons/'.$icon) }}">
                                    <div class="d-flex flex-column gap-2">
                                        <span>{{ $file->file_name }}</span>
                                        @if($file->access === 'teachers')
                                            <i class="text-secondary">Dit bestand is alleen beschikbaar voor
                                                praktijkbegeleiders.</i>
                                        @endif
                                    </div>
                                </a>

                                @if($isTeacher)
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Opties
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><form
                                                    action="{{ route('lessons.environment.lesson.files.destroy', [$lesson->id, $file->id]) }}"
                                                    method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">Verwijderen
                                                    </button>
                                                </form></li>
                                            @if($file->access === 'teachers')
                                                <li><a class="dropdown-item" href="{{ route('lessons.environment.lesson.files.toggle-access', [$lesson->id, $file->id]) }}">Maak beschikbaar voor alle deelnemers</a></li>
                                            @endif
                                            @if($file->access === 'all' || $file->access === '')
                                                <li><a class="dropdown-item" href="{{ route('lessons.environment.lesson.files.toggle-access', [$lesson->id, $file->id]) }}">Maak alleen beschikbaar voor praktijkbegeleiders</a></li>
                                            @endif
                                        </ul>
                                    </div>


                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                    <span class="material-symbols-rounded me-2">cloud_off</span>Er zijn geen bestanden toegevoegd aan
                    deze lesomgeving.
                </div>
            @endif
        </div>

    </div>
@endsection
