window.addEventListener('load', init);

function init() {
    console.log('loaded')

    // Gather all event elements
    const events = document.querySelectorAll('.calendar-event');

    // Create a lookup object to store events by ID
    const eventsById = {};
    events.forEach(event => {
        const eventId = event.dataset.eventId;
        if (!eventsById[eventId]) {
            eventsById[eventId] = [];
        }
        eventsById[eventId].push(event);
    });


    // Add event listeners to each event
    events.forEach(event => {
        event.addEventListener('mouseover', () => {
            const eventId = event.dataset.eventId;
            eventsById[eventId].forEach(event => {
                event.classList.add('calendar-event-hovered'); // Add a class for styling
            });
        });

        event.addEventListener('mouseout', () => {
            const eventId = event.dataset.eventId;
            eventsById[eventId].forEach(event => {
                event.classList.remove('calendar-event-hovered');
            });
        });
    });
}

function positionPopup(event) {
    const popup = document.getElementById('event-popup');

    // Calculate the popup dimensions
    const popupWidth = popup.offsetWidth;
    const popupHeight = popup.offsetHeight;

    // Get the viewport dimensions and scroll positions
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    const scrollTop = window.scrollY;
    const scrollLeft = window.scrollX;

    // Calculate the mouse position relative to the viewport
    let mouseX = event.clientX + scrollLeft;
    let mouseY = event.clientY + scrollTop;

    // Default positioning (right and below the cursor)
    let popupLeft = mouseX + 15;
    let popupTop = mouseY + 15;

    // Check if the popup would overflow on the right side
    if ((popupLeft + popupWidth) > viewportWidth + scrollLeft) {
        // If yes, position it to the left of the cursor
        popupLeft = mouseX - popupWidth - 15;
    }

    // Check if the popup would overflow at the bottom
    if ((popupTop + popupHeight) > viewportHeight + scrollTop) {
        // If yes, position it above the cursor
        popupTop = mouseY - popupHeight - 15;
    }

    // Check if the popup would overflow on the left side (when flipped)
    if (popupLeft < scrollLeft) {
        // If yes, flip back to the right
        popupLeft = mouseX + 15;
    }

    // Check if the popup would overflow at the top (when flipped)
    if (popupTop < scrollTop) {
        // If yes, flip back to below the cursor
        popupTop = mouseY + 15;
    }

    // Additional check to avoid navbar or top-fixed elements
    const navbarOffset = 200; // Adjust this value based on your navbar height
    if (mouseY < navbarOffset) {
        // Move the popup below the cursor if it's close to the top
        popupTop = mouseY + 15;
    }

    // Apply the calculated positions
    popup.style.left = popupLeft + 'px';
    popup.style.top = popupTop + 'px';
}

// Attach the event listeners to all calendar events
document.querySelectorAll('.calendar-event').forEach(eventDiv => {
    eventDiv.addEventListener('mouseover', function(event) {
        const image = this.getAttribute('data-image');
        const title = this.getAttribute('data-title');
        const content = this.getAttribute('data-content');

        // Update popup content
        if (image) {
            document.getElementById('popup-image').src = image;
            document.getElementById('popup-image').style.display = "block";
        } else {
            document.getElementById('popup-image').style.display = "none";
        }
        document.getElementById('popup-title').textContent = title;
        document.getElementById('popup-content').textContent = content;

        // Display the popup
        const popup = document.getElementById('event-popup');
        popup.style.display = 'block';

        // Position the popup near the mouse
        positionPopup(event);
    });

    eventDiv.addEventListener('mousemove', function(event) {
        // Continuously reposition the popup as the mouse moves
        positionPopup(event);
    });

    eventDiv.addEventListener('mouseout', function() {
        // Hide the popup when the mouse leaves the event
        const popup = document.getElementById('event-popup');
        popup.style.display = 'none';
    });
});

