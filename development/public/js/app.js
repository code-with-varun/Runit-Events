// public/js/app.js - EventHub Frontend Scripts

document.addEventListener('DOMContentLoaded', function () {
    // 1. 10-Minute Seat Reservation Countdown Timer (Module 5 & Priority 2)
    const countdownEl = document.getElementById('seat-countdown-timer');
    if (countdownEl) {
        let totalSeconds = 10 * 60; // 10 minutes default
        const timerInterval = setInterval(function () {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            countdownEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                alert('Your 10-minute seat reservation lock has expired. Releasing capacity slot.');
                window.location.reload();
            }
            totalSeconds--;
        }, 1000);
    }

    // 2. Auto-initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
