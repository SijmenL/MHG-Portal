@extends('layouts.lessons')


@section('content')
    <div id="popup-upload" class="popup d-none" style="margin-top: -122px">
        <div class="popup-body">
            <h2>Upload bestanden naar de lesomgeving</h2>
            <p>Toegestane bestandstypes: <i>.pdf, .jpeg, .jpg, .webp, .png, .zip, .pptx, .docx, .doc, .ppt, .mp4, .mov,
                    .mp3, .wav, .xlsx</i></p>

            <form id="upload-form" enctype="multipart/form-data">
                @csrf
                <div class="d-flex flex-row-responsive gap-2">
                    <div class="form-group">
                        <label for="file">Kies bestanden om te uploaden</label>
                        <input type="file" name="file[]" multiple class="form-control" id="file-input">
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
                    <div class="progress-bar h-100 progress-bar-striped bg-success progress-bar-animated"
                         role="progressbar" style="width: 0%;"></div>
                </div>

                <div id="upload-status" class="text-success mt-2" style="display: none;"></div>
                <div class="button-container justify-content-center">
                    <button type="button" id="upload-button"
                            class="btn btn-success text-white flex flex-row align-items-center justify-content-center">
                        <span class="button-text">Uploaden</span>
                        <span style="display: none" class="loading-spinner spinner-border spinner-border-sm"
                              aria-hidden="true"></span>
                        <span style="display: none" class="loading-text" role="status">Laden...</span>
                    </button>
                    <a id="popup-upload-button-close" class="btn btn-outline-danger">Annuleren</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let uploadPopup = document.getElementById('popup-upload');
            let uploadPopupButton = document.getElementById('popup-upload-button');
            let uploadPopupButtonClose = document.getElementById('popup-upload-button-close');
            let uploadForm = document.getElementById('upload-form');
            let uploadButton = document.getElementById('upload-button');
            let fileInput = document.getElementById('file-input');
            let progressBar = document.querySelector('.progress-bar');
            let progressContainer = document.querySelector('.progress');
            let uploadStatus = document.getElementById('upload-status');
            let xhr = null; // Declare xhr globally so it can be canceled

            // Show the upload popup
            uploadPopupButton.addEventListener('click', function () {
                uploadPopup.classList.remove('d-none');

            });

            // Close the upload popup and reset everything
            uploadPopupButtonClose.addEventListener('click', function () {
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
                            @endphp
                            <li class="list-group-item d-flex flex-row gap-3 align-items-center justify-content-between">
                                <a class="text-decoration-none text-black d-flex flex-row gap-3 align-items-center justify-content-center"
                                   href="{{ Storage::url($file->file_path) }}" target="_blank">
                                    <img style="width: clamp(25px, 50px, 5vw)" class="m-0" alt="file"
                                         src="{{ asset('/files/lessons/file-icons/'.$icon) }}">
                                    <div class="d-flex flex-column gap-2">
                                        <span>{{ $file->file_name }}</span>
                                        @if($file->access === 'teachers')
                                            <i class="text-secondary">Dit bestand is alleen beschikbaar voor praktijkbegeleiders.</i>
                                        @endif
                                    </div>
                                </a>

                                @if(Auth::id() === $file->user_id || $lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists())
                                    <form
                                        action="{{ route('lessons.environment.lesson.files.destroy', [$lesson->id, $file->id]) }}"
                                        method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="min-width: 75px">Verwijderen</button>
                                    </form>
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
