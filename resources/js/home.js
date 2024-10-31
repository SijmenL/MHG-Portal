window.addEventListener('load', init);

let helpButton;
let newButton;
let body;
let html;
let pages;
let currentPage = 0; // Start from the first page (index 0)

let newPopUp;
let popUp;
let nextPageButton;
let previousPageButton;
let closeButton;
let closeNewButton;

function init() {
    helpButton = document.getElementById('help-button');
    newButton = document.getElementById('new-button');
    newPopUp = document.getElementById('newPopUp');
    popUp = document.getElementById('popUp');
    body = document.getElementById('app');
    html = document.querySelector('html');

    pages = document.getElementsByClassName('page');

    // Initially show the first page and hide the others
    showPage(currentPage);

    helpButton.addEventListener('click', function () {
        openPopup();
    });

    newButton.addEventListener('click', function () {
        openNewPopup();
    });

    nextPageButton = document.getElementById('next-page');
    nextPageButton.addEventListener('click', nextPage);

    previousPageButton = document.getElementById('previous-page');
    previousPageButton.addEventListener('click', previousPage);

    closeButton = document.getElementById('close-popup');
    closeButton.addEventListener('click', closePopup);

    closeNewButton = document.getElementById('close-new-popup');
    closeNewButton.addEventListener('click', closeNewPopup);

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

function openNewPopup() {
    let scrollPosition = window.scrollY;
    html.classList.add('no-scroll');
    window.scrollTo(0, scrollPosition);
    newPopUp.style.display = 'flex';
}

function closePopup() {
    popUp.style.display = 'none';
    html.classList.remove('no-scroll');
}

function closeNewPopup() {
    newPopUp.style.display = 'none';
    html.classList.remove('no-scroll');
}

document.addEventListener('DOMContentLoaded', function () {
    // Get all tab links
    const tabLinks = document.querySelectorAll('.nav-link');

    // Add click event listeners to each tab link
    tabLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default anchor click behavior

            // Remove active class from all tab links
            tabLinks.forEach(link => link.classList.remove('active'));

            // Hide all tab content
            const tabContents = document.querySelectorAll('.tab-pane');
            tabContents.forEach(content => {
                content.classList.remove('show', 'active');
            });

            // Add active class to the clicked tab
            this.classList.add('active');

            // Show the content of the clicked tab
            const targetTabContent = document.querySelector(this.getAttribute('href'));
            targetTabContent.classList.add('show', 'active');
        });
    });

    // Close popup event
    document.getElementById('close-new-popup').addEventListener('click', function () {
        document.getElementById('newPopUp').style.display = 'none'; // Close the popup
    });
});

