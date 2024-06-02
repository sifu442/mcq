import './bootstrap';
import 'flowbite';

document.addEventListener('DOMContentLoaded', () => {
    const togglePasswordButton = document.querySelector('#togglePassword');
    if (togglePasswordButton) {
        togglePasswordButton.addEventListener('click', () => {
            const passwordInput = document.querySelector('#password');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            // Optional: Change the icon if using one, e.g., toggle eye/eye-slash icon
            togglePasswordButton.classList.toggle('showing-password');
        });
    }
});
