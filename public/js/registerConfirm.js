document.addEventListener("DOMContentLoaded", function() {
    const mainPopup = document.getElementById('dataPopup');
    const popupText = document.getElementById('userData');
    const buttonYes = document.querySelector('.popupButtonYes');
    const buttonNo = document.querySelector('.popupButtonNo');
    const form = document.querySelector(".register-form");
    const registerButton = document.getElementById('register-btn');

    let canSubmit = false;
    let isSubmitting = false;

    form.addEventListener("submit", function (event) {
        if (isSubmitting) {
            event.preventDefault();
            return;
        }

        if (canSubmit) {
            isSubmitting = true;
            if (registerButton) {
                registerButton.disabled = true;
                registerButton.innerText = 'Laden...';
            }
            return;
        }

        event.preventDefault();

        const name = form.querySelector('input[name="name"]').value;
        const email = form.querySelector('input[name="email"]').value;
        const select = form.querySelector('select[name="opleiding"]');
        const course = select.options[select.selectedIndex].text;
        const klas = form.querySelector('select[name="klas"]').value;

        popupText.innerText = `Naam: ${name}\nEmail: ${email}\nOpleiding: ${course}\nKlas: ${klas}`;
        mainPopup.style.display = "flex";
    });

    buttonYes.addEventListener("click", function () {
        if (isSubmitting) {
            return;
        }

        buttonYes.disabled = true;
        buttonNo.disabled = true;
        canSubmit = true;
        mainPopup.style.display = "none";
        form.requestSubmit();
    });

    buttonNo.addEventListener("click", function () {
        mainPopup.style.display = "none";
    });
});
