function initAvatarPreview() {
    const avatarInput = document.getElementById('avatar_upload');
    const avatarPreview = document.getElementById('avatar-preview');

    
    if (!avatarInput || !avatarPreview) return;

    avatarInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            
            const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
            if (!allowedTypes.includes(file.type)) {
                alert("Niedozwolony format pliku! Wybierz JPG, JPEG lub PNG.");
                this.value = ""; 
                return;
            }

            // Odczyt pliku i zmiana src obrazka
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
}


function toggleProfileDetails() {
    const defaultView = document.getElementById('view-default');
    const detailsView = document.getElementById('view-details');
    const btn = document.getElementById('toggleBtn');

    if (!defaultView || !detailsView || !btn) return;

    if (detailsView.style.display === 'none') {
        defaultView.style.display = 'none';
        detailsView.style.display = 'block';
        btn.innerText = 'Pokaż profil';
    } else {
        defaultView.style.display = 'block';
        detailsView.style.display = 'none';
        btn.innerText = 'Szczegółowe informacje';
    }
}

function smartBack(fallbackUrl) {
    const referrer = document.referrer;
    const currentUrl = window.location.href.split('?')[0];

    if (referrer.includes(currentUrl) || referrer === "") {
        window.location.href = fallbackUrl;
    } else {
        window.history.back();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initAvatarPreview();
    
    const successMessage = document.querySelector('.text-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 500);
        }, 3000);
    }
});
