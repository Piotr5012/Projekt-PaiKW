const wrapper = document.querySelector('.wrapper');
const registerLink = document.querySelector('.register-link');
const loginLink = document.querySelector('.login-link');


const removeNotifications = () => {
    const notification = document.querySelector('.dynamic-error, .text-success');
    if (notification) {
        notification.style.transition = '0.2s';
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 200);
    }
};


document.addEventListener('DOMContentLoaded', () => {
    
    
    const fileUpload = document.getElementById('file_upload');
    const fileNameDisplay = document.getElementById('file-name');

    if (fileUpload && fileNameDisplay) {
        fileUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileNameDisplay.textContent = "Wybrano: " + this.files[0].name;
            } else {
                fileNameDisplay.textContent = "";
            }
        });
    }

    
    const successMessage = document.querySelector('.text-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.remove();
            }, 500);
        }, 3000);
    }

    
    if (registerLink && loginLink && wrapper) {
        registerLink.onclick = (e) => {
            e.preventDefault(); 
            removeNotifications(); 
            wrapper.classList.add('active');
        }

        loginLink.onclick = (e) => {
            e.preventDefault(); 
            removeNotifications();
            wrapper.classList.remove('active');
        }
    }
    
});

