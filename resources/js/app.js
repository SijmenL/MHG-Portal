import './bootstrap.js';

window.addEventListener('load', init);

let hamburgerIcon;
let closeHamburgerIcon;
let hamburgerMenu;
let body;
let select;
let buttonContainer;
let html;

function init() {
    deleteButtons()

    hamburgerIcon = document.getElementById('hamburger-icon');
    closeHamburgerIcon = document.getElementById('hamburger-close-icon');
    hamburgerMenu = document.getElementById('hamburger-menu');

    body = document.getElementById('app')
    html = document.querySelector('html')

    if (document.getElementById('select-roles')) {
        select = document.getElementById('select-roles');
        buttonContainer = document.getElementById('button-container');
        editRoles();
    }

    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', hamburger)
    }

    if (document.getElementsByClassName('forum-image').length !== 0 || document.getElementsByClassName('zoomable-image').length !== 0 || document.getElementsByClassName('file-image').length !== 0 || document.getElementsByClassName('file-video').length !== 0 || document.getElementsByClassName('file-pdf').length !== 0 || document.getElementsByClassName('file-audio').length !== 0) {
        initImageViewer(); // Call the image zoom setup function
    }
}

function hamburger() {
    hamburgerIcon.classList.toggle('d-none');
    closeHamburgerIcon.classList.toggle('d-none');
}

function deleteButtons() {
    let allButtons = document.querySelectorAll('a[class^=delete-button]');
    let popUp;
    for (let i = 0; i < allButtons.length; i++) {
        allButtons[i].addEventListener('click', function (e) {

            const scrollPosition = window.scrollY;
            html.classList.add('no-scroll');
            window.scrollTo(0, scrollPosition);

            popUp = document.createElement('div');
            popUp.classList.add('popup');
            popUp.style.top = "0px";
            body.appendChild(popUp);

            let popUpBody = document.createElement('div');
            popUpBody.classList.add('popup-body');
            popUp.appendChild(popUpBody);

            let popUpTitle = document.createElement('h2');
            let deleteName = allButtons[i].getAttribute('data-name');
            popUpTitle.innerText = `Weet je zeker dat je ${deleteName} wilt verwijderen?`;
            popUpBody.appendChild(popUpTitle);

            let popUpUnderTitle = document.createElement('p');
            popUpUnderTitle.innerText = `Deze actie kan niet ongedaan gemaakt worden.`;
            popUpUnderTitle.classList.add('text-danger');
            popUpBody.appendChild(popUpUnderTitle);

            let buttonContainer = document.createElement('div');
            buttonContainer.classList.add('button-container');
            popUpBody.appendChild(buttonContainer);

            let continueButton = document.createElement('a');
            continueButton.classList.add('btn', 'btn-outline-danger');
            continueButton.innerText = 'Ja, verwijderen';
            buttonContainer.appendChild(continueButton);

            let cancelButton = document.createElement('a');
            cancelButton.classList.add('btn', 'btn-success');
            cancelButton.innerText = 'Nee, annuleren';
            buttonContainer.appendChild(cancelButton);

            continueButton.addEventListener('click', () => {
                window.location.href = allButtons[i].dataset.link;
                html.classList.remove('no-scroll');
            });

            cancelButton.addEventListener('click', () => {
                popUp.remove();
                html.classList.remove('no-scroll');
            });
        });
    }
}

function editRoles() {
    select.querySelectorAll('option').forEach(option => {
        const button = document.createElement('p');
        const autoSubmit = document.getElementById("auto-submit");
        button.title = option.getAttribute('data-description');
        button.textContent = option.textContent;
        button.textContent = option.textContent;
        button.classList.add('btn', 'btn-secondary');
        button.dataset.value = option.value;

        if (option.selected) {
            button.classList.add('btn-primary', 'text-white');
            button.classList.remove('btn-secondary');
        } else {
            button.classList.remove('btn-primary', 'text-white');
            button.classList.add('btn-secondary');
        }

        button.addEventListener('click', () => {
            if (option.selected) {
                option.selected = false;
                button.classList.remove('btn-primary', 'text-white');
                button.classList.add('btn-secondary');
                autoSubmit.submit();
            } else {
                option.selected = true;
                button.classList.add('btn-primary', 'text-white');
                button.classList.remove('btn-secondary');
                autoSubmit.submit();
            }
        });

        buttonContainer.appendChild(button);
    });
}

function initImageViewer() {
    const html = document.documentElement;

    // Container
    const container = document.createElement('div');
    container.className = 'enlarged-image-container';
    container.style.display = 'none';
    container.setAttribute('role', 'dialog');
    document.body.appendChild(container);

    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    document.body.appendChild(overlay);

    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.className = 'close-image';
    closeBtn.setAttribute('aria-label', 'Close media');
    closeBtn.innerHTML = '&times;';
    container.appendChild(closeBtn);

    // Nav buttons
    const prevButton = document.createElement('button');
    prevButton.className = 'nav-button prev-button';
    prevButton.innerHTML = '&#10094;';
    container.appendChild(prevButton);

    const nextButton = document.createElement('button');
    nextButton.className = 'nav-button next-button';
    nextButton.innerHTML = '&#10095;';
    container.appendChild(nextButton);

    // Media wrapper
    const mediaWrap = document.createElement('div');
    mediaWrap.className = 'image-wrap';
    container.appendChild(mediaWrap);

    // State
    let gallery = [];
    let galleryTypes = [];
    let currentIndex = -1;
    let mediaEl = null;

    // Open gallery
    function openGallery(sources, index, types = []) {
        if (!sources || sources.length == 0) return;
        gallery = sources.slice();
        galleryTypes = types.length ? types.slice() : sources.map(() => "image");
        currentIndex = Math.max(0, Math.min(index, gallery.length - 1));
        setMedia(currentIndex);
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'auto';
        container.style.display = 'flex';
        html.classList.add('no-scroll');
        updateNav();
    }

    function setMedia(idx) {
        if (mediaEl) {
            mediaWrap.removeChild(mediaEl);
            mediaEl = null;
        }

        const src = gallery[idx];
        const type = galleryTypes[idx];

        // helper to build Office Viewer URL
        const officeViewer = url =>
            `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(url)}`;

        if (type == "video") {
            const video = document.createElement("video");
            video.className = "enlarged-video";
            video.controls = true;
            video.autoplay = true;
            video.preload = "auto";
            video.src = src;
            mediaEl = video;

        } else if (type == "audio") {
            const audio = document.createElement("audio");
            audio.className = "enlarged-audio";
            audio.controls = true;
            audio.autoplay = true;
            audio.preload = "auto";
            audio.src = src;
            mediaEl = audio;

        } else if (type == "pdf") {
            const frame = document.createElement("iframe");
            frame.className = "enlarged-pdf";
            frame.src = src;
            frame.setAttribute("frameborder", "0");
            frame.setAttribute("allowfullscreen", "");
            mediaEl = frame;

        } else if (type == "office") {
            const frame = document.createElement("iframe");
            frame.className = "enlarged-office";
            frame.src = officeViewer(src);
            frame.setAttribute("frameborder", "0");
            frame.setAttribute("allowfullscreen", "");
            mediaEl = frame;

        } else { // images by default
            const img = document.createElement("img");
            img.className = "enlarged-image";
            img.src = src;
            mediaEl = img;
            preloadNeighbor(idx);
        }

        mediaWrap.appendChild(mediaEl);
    }



    function preloadNeighbor(idx) {
        if (galleryTypes[idx + 1] == "image" && gallery[idx + 1]) {
            const i = new Image();
            i.src = gallery[idx + 1];
        }
        if (galleryTypes[idx - 1] == "image" && gallery[idx - 1]) {
            const i = new Image();
            i.src = gallery[idx - 1];
        }
    }

    function updateNav() {
        if (gallery.length <= 1) {
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
            return;
        }
        prevButton.style.display = (currentIndex == 0) ? 'none' : 'block';
        nextButton.style.display = (currentIndex == gallery.length - 1) ? 'none' : 'block';
    }

    function close() {
        container.style.display = 'none';
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        html.classList.remove('no-scroll');
        gallery = [];
        galleryTypes = [];
        currentIndex = -1;
        if (mediaEl) {
            mediaWrap.removeChild(mediaEl);
            mediaEl = null;
        }
    }

    // Controls
    prevButton.addEventListener('click', e => {
        e.stopPropagation();
        if (currentIndex > 0) {
            currentIndex -= 1;
            setMedia(currentIndex);
            updateNav();
        }
    });

    nextButton.addEventListener('click', e => {
        e.stopPropagation();
        if (currentIndex < gallery.length - 1) {
            currentIndex += 1;
            setMedia(currentIndex);
            updateNav();
        }
    });

    closeBtn.addEventListener('click', e => {
        e.stopPropagation();
        close();
    });

    overlay.addEventListener('click', close);
    container.addEventListener('click', e => {
        if (e.target == container) close();
    });

    document.addEventListener('keydown', e => {
        if (container.style.display == 'flex') {
            if (e.key == 'ArrowRight' && currentIndex < gallery.length - 1) {
                currentIndex += 1;
                setMedia(currentIndex);
                updateNav();
            } else if (e.key == 'ArrowLeft' && currentIndex > 0) {
                currentIndex -= 1;
                setMedia(currentIndex);
                updateNav();
            } else if (e.key == 'Escape') {
                close();
            }
        }
    });

    // Delegated clicks
    document.addEventListener('click', function (event) {
        if (event.target.closest('.has-dropdown')) return;

        // 1) Zoomable thumbnails
        const thumb = event.target.closest('.zoomable-image');
        if (thumb) {
            event.preventDefault();
            const thumbs = Array.from(document.querySelectorAll('.zoomable-image'));
            const sources = thumbs.map(t => t.dataset.fullsrc || t.src);
            const idx = thumbs.indexOf(thumb);
            if (idx !== -1) openGallery(sources, idx);
            return;
        }

        // 2) File manager: images, videos, audios, pdf
        const mediaRow = event.target.closest(
            'tr[data-is-image="true"], tr[data-is-video="true"], tr[data-is-audio="true"], tr[data-is-pdf="true"], tr[data-is-office="true"]'
        );
        if (mediaRow) {
            event.preventDefault();
            const fileManager = mediaRow.closest('.file-manager') || document;
            const rows = Array.from(fileManager.querySelectorAll(
                'tr[data-is-image="true"], tr[data-is-video="true"], tr[data-is-audio="true"], tr[data-is-pdf="true"], tr[data-is-office="true"]'
            ));
            const sources = rows.map(r =>
                r.dataset.imageSrc ||
                r.dataset.videoSrc ||
                r.dataset.audioSrc ||
                r.dataset.officeSrc ||
                r.dataset.pdfSrc
            );
            const types = rows.map(r => getTypeFromSrc(r));
            const idx = rows.indexOf(mediaRow);
            if (idx !== -1) openGallery(sources, idx, types);
            return;
        }


        // 3) Other file rows
        const fileRow = event.target.closest('tr.file');
        if (fileRow) {
            if (event.target.closest('.has-dropdown')) return;
            const link = fileRow.getAttribute('data-link');
            let target = fileRow.getAttribute('data-target');
            target = target ? target.trim() : '_self';
            if (target == 'null') target = '_self';
            if (link) window.open(link, target);
        }
    });

    function getTypeFromSrc(row) {
        if (row.dataset.isImage == "true") return "image";
        if (row.dataset.isVideo == "true") return "video";
        if (row.dataset.isAudio == "true") return "audio";
        if (row.dataset.isPdf == "true") return "pdf";

        const src =
            row.dataset.imageSrc ||
            row.dataset.videoSrc ||
            row.dataset.audioSrc ||
            row.dataset.pdfSrc ||
            row.dataset.officeSrc ||
            row.dataset.link ||
            "";

        if (/\.(docx?|xlsx?|pptx?)$/i.test(src)) return "office";
        if (/\.pdf$/i.test(src)) return "pdf";

        return "image"; // safe fallback
    }


}


