import './bootstrap';
import 'flowbite';

document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');

    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
    const toggleLoginPasswordButton = document.getElementById('toggleLoginPassword');

    const eyeOffPassword = document.getElementById('eyeOffPassword');
    const eyeOnPassword = document.getElementById('eyeOnPassword');
    const eyeOffConfirmation = document.getElementById('eyeOffConfirmation');
    const eyeOnConfirmation = document.getElementById('eyeOnConfirmation');
    const eyeOffIcon = document.getElementById('eyeOff');
    const eyeOnIcon = document.getElementById('eyeOn');

    function toggleVisibility(input, eyeOff, eyeOn) {
        if (input.type === 'password') {
            input.type = 'text';
            eyeOff.classList.remove('hidden');
            eyeOn.classList.add('hidden');
        } else {
            input.type = 'password';
            eyeOff.classList.add('hidden');
            eyeOn.classList.remove('hidden');
        }
    }

    if (togglePassword) {
        togglePassword.addEventListener('click', () => {
            toggleVisibility(passwordInput, eyeOffPassword, eyeOnPassword);
        });
    }

    if (togglePasswordConfirmation) {
        togglePasswordConfirmation.addEventListener('click', () => {
            toggleVisibility(passwordConfirmationInput, eyeOffConfirmation, eyeOnConfirmation);
        });
    }

    if (toggleLoginPasswordButton) {
        toggleLoginPasswordButton.addEventListener('click', () => {
            toggleVisibility(passwordInput, eyeOffIcon, eyeOnIcon);
        });
    }
});




