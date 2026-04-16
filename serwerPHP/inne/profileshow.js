// --- FUNKCJA PODGLĄDU AVATARA ---
function initAvatarPreview() {
    const avatarInput = document.getElementById('avatar_upload');
    const avatarPreview = document.getElementById('avatar-preview');

    // Jeśli nie jesteśmy na stronie edycji profilu, przerywamy
    if (!avatarInput || !avatarPreview) return;

    avatarInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            // Zabezpieczenie po stronie klienta
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

// --- TWOJE ISTNIEJĄCE FUNKCJE ---

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

    // Jeśli poprzednia strona to ten sam post (odświeżenie przez lajk/komentarz)
    // lub jeśli referrer jest pusty
    if (referrer.includes(currentUrl) || referrer === "") {
        // Wracamy do strony, z której pierwotnie przyszliśmy (Profil lub Przegląd)
        window.location.href = fallbackUrl;
    } else {
        // Jeśli to pierwsze wejście na post z innej strony, cofnij normalnie
        window.history.back();
    }
}

// --- URUCHOMIENIE FUNKCJI PO ZAŁADOWANIU STRONY ---
document.addEventListener('DOMContentLoaded', () => {
    initAvatarPreview();
    
    // Obsługa znikania komunikatów (jeśli masz ją w tym pliku)
    const successMessage = document.querySelector('.text-success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            setTimeout(() => successMessage.remove(), 500);
        }, 3000);
    }
});