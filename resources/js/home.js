window.addEventListener('load', init);

let helpButton;
let body;
let html;
let pages;
let currentPage = 0; // Start from the first page (index 0)

let nextPageButton;
let previousPageButton;
let closeButton;

function init() {
    helpButton = document.getElementById('help-button');
    body = document.getElementById('app');
    html = document.querySelector('html');

    pages = document.getElementsByClassName('page');

    // Initially show the first page and hide the others
    showPage(currentPage);

    helpButton.addEventListener('click', function () {
        openPopup();
    });

    nextPageButton = document.getElementById('next-page');
    nextPageButton.addEventListener('click', nextPage);

    previousPageButton = document.getElementById('previous-page');
    previousPageButton.addEventListener('click', previousPage);

    closeButton = document.getElementById('close-popup');
    closeButton.addEventListener('click', closePopup);

    updateButtonStates(); // Set initial button states
}

function showPage(index) {
    for (let i = 0; i < pages.length; i++) {
        pages[i].style.display = (i === index) ? 'block' : 'none';
    }
}

function updateButtonStates() {
    // Update next page button
    if (currentPage >= pages.length - 1) {
        nextPageButton.classList.remove('btn-dark');
        nextPageButton.disabled = true;
    } else {
        nextPageButton.classList.add('btn-dark');
        nextPageButton.disabled = false;
    }

    // Update previous page button
    if (currentPage <= 0) {
        previousPageButton.classList.remove('btn-dark');
        previousPageButton.disabled = true;
    } else {
        previousPageButton.classList.add('btn-dark');
        previousPageButton.disabled = false;
    }
}

function nextPage() {
    if (currentPage < pages.length - 1) {
        currentPage++;
        showPage(currentPage);
        updateButtonStates(); // Update buttons after page change
    }
}

function previousPage() {
    if (currentPage > 0) {
        currentPage--;
        showPage(currentPage);
        updateButtonStates(); // Update buttons after page change
    }
}

function openPopup() {
    let scrollPosition = window.scrollY;
    html.classList.add('no-scroll');
    window.scrollTo(0, scrollPosition);
    popUp.style.display = 'flex';
}

function closePopup() {
    popUp.style.display = 'none';
}
