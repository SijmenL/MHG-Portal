import './bootstrap';

window.addEventListener('load', init);

let hamburgerIcon;
let closeHamburgerIcon;
let hamburgerMenu;
let body;
let select;
let buttonContainer;

function init() {
    deleteButtons()

    hamburgerIcon = document.getElementById('hamburger-icon');
    closeHamburgerIcon = document.getElementById('hamburger-close-icon');
    hamburgerMenu = document.getElementById('hamburger-menu');

    body = document.getElementById('app')

    if (document.getElementById('select-roles')) {
        select = document.getElementById('select-roles');
        buttonContainer = document.getElementById('button-container');
        editRoles();
    }

    hamburgerMenu.addEventListener('click', hamburger)
}

function hamburger() {
    hamburgerIcon.classList.toggle('d-none')
    closeHamburgerIcon.classList.toggle('d-none')
}

function deleteButtons() {
    let allButtons = document.querySelectorAll('a[class^=delete-button]');
    let popUp;
    for (let i = 0; i < allButtons.length; i++) {
        allButtons[i].addEventListener('click', function (e) {

            popUp = document.createElement('div');
            popUp.classList.add('popup')
            body.appendChild(popUp)

            let popUpBody = document.createElement('div')
            popUpBody.classList.add('popup-body')
            popUp.appendChild(popUpBody)

            let popUpTitle = document.createElement('h2')
            let deleteName =  allButtons[i].getAttribute('data-name')
            popUpTitle.innerText = `Weet je zeker dat je ${deleteName} wilt verwijderen?`;
            popUpBody.appendChild(popUpTitle)

            let popUpUnderTitle = document.createElement('p')
            popUpUnderTitle.innerText = `Deze actie kan niet ongedaan gemaakt worden.`;
            popUpUnderTitle.classList.add('text-danger')
            popUpBody.appendChild(popUpUnderTitle)

            let buttonContainer = document.createElement('div')
            buttonContainer.classList.add('button-container')
            popUpBody.appendChild(buttonContainer)


            let continueButton = document.createElement('a')
            continueButton.classList.add('btn')
            continueButton.classList.add('btn-success')
            continueButton.innerText = 'Ja, verwijderen'
            buttonContainer.appendChild(continueButton)

            let cancelButton = document.createElement('a')
            cancelButton.classList.add('btn')
            cancelButton.classList.add('btn-outline-danger')
            cancelButton.innerText = 'Nee, annuleren'
            buttonContainer.appendChild(cancelButton)

            continueButton.addEventListener('click', () => {
                window.location.href = allButtons[i].dataset.link;
            });

            cancelButton.addEventListener('click', () => {
                popUp.remove();
            });



        });
    }
}

function editRoles() {
    select.querySelectorAll('option').forEach(option => {
        const button = document.createElement('p');
        const autoSubmit = document.getElementById("auto-submit");
        button.title = option.getAttribute('data-description')
        button.textContent = option.textContent;
        button.classList.add('btn');
        button.classList.add('btn-secondary');
        button.dataset.value = option.value;

        if (option.selected) {
            button.classList.add('btn-primary');
            button.classList.add('text-white');
            button.classList.remove('btn-secondary');
        } else {
            button.classList.remove('btn-primary');
            button.classList.remove('text-white');
            button.classList.add('btn-secondary');
        }

        button.addEventListener('click', () => {
            if (option.selected) {
                option.selected = false;
                button.classList.remove('btn-primary');
                button.classList.remove('text-white');
                button.classList.add('btn-secondary');
                autoSubmit.submit();
            } else {
                option.selected = true;
                button.classList.add('btn-primary');
                button.classList.add('text-white');
                button.classList.remove('btn-secondary');
                autoSubmit.submit();
            }
        });

        buttonContainer.appendChild(button);
    });
}
