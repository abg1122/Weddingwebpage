// Countdown timer for August 21, 2026, 00:00:00 (start of the wedding)
function updateCountdown() {
    const countdownElement = document.getElementById("countdown-timer");
    if (!countdownElement) return; // Exit if the element doesn't exist

    const weddingDate = new Date("August 21, 2026 00:00:00").getTime();
    const now = new Date().getTime();
    const distance = weddingDate - now;

    if (distance < 0) {
        countdownElement.innerHTML = "🎉 The wedding has begun! 🎉";
        return;
    }

    // Calculate days, hours, minutes, and seconds
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Display the countdown in an elegant format
    countdownElement.innerHTML = `
        <strong>${days}</strong> Days | 
        <strong>${hours}</strong> Hours | 
        <strong>${minutes}</strong> Minutes | 
        <strong>${seconds}</strong> Seconds`;
}

// Update the countdown every second, but only if the element exists
const countdownElement = document.getElementById("countdown-timer");
if (countdownElement) {
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Initial call
}

// Mobile menu functionality
document.addEventListener("DOMContentLoaded", function () {
    const menuButton = document.getElementById("menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    if (menuButton && sidebar) {
        // Apply saved menu state on page load
        const savedState = localStorage.getItem("menuState");
        if (savedState === "open") {
            sidebar.classList.add("show");
        } else {
            sidebar.classList.remove("show"); // Ensure it's hidden by default
        }

        // Toggle menu on button click
        menuButton.addEventListener("click", function () {
            sidebar.classList.toggle("show");

            // Update menu state in localStorage
            if (sidebar.classList.contains("show")) {
                localStorage.setItem("menuState", "open");
            } else {
                localStorage.setItem("menuState", "closed");
            }
        });

        // Handle menu link clicks
        document.querySelectorAll(".sidebar a").forEach(link => {
            link.addEventListener("click", function (event) {
                // Optional: Remove event.preventDefault() if full page reload is desired
                // event.preventDefault();

                // Navigate to the new page
                window.location.href = this.href;

                // Keep the menu open when navigating (optional)
                localStorage.setItem("menuState", "open");
            });
        });
    }
});

// RSVP form functionality (only run if the form exists on the page)
const rsvpForm = document.getElementById('rsvp-form');
if (rsvpForm) {
    rsvpForm.addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevent default form submission

        const message = document.getElementById('rsvp-message');
        message.textContent = 'Submitting your RSVP... Please wait.';
        message.style.color = '#a68b8b';

        try {
            const response = await fetch(rsvpForm.action, {
                method: 'POST',
                body: new FormData(rsvpForm),
                headers: {'Accept': 'application/json'}
            });
            const data = await response.json();
            if (data.success) {
                message.textContent = data.message;
                message.style.color = '#4a5e4d';
                rsvpForm.reset(); // Clear the form
            } else {
                message.textContent = 'Oops! There was an issue submitting your RSVP. Please try again or contact us directly.';
                message.style.color = 'red';
            }
        } catch (error) {
            message.textContent = 'An error occurred. Please try again or contact us directly.';
            message.style.color = 'red';
        }
    });
}


