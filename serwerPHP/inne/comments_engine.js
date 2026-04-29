document.addEventListener('DOMContentLoaded', function() {
    
    // FUNKCJA DOPASOWANIA WYSOKOŚCI
    function adjustHeight(el) {
        el.style.height = 'auto'; 
        el.style.height = el.scrollHeight + 'px';
    }

    
    document.addEventListener('input', function(e) {
        if (e.target.tagName.toLowerCase() === 'textarea') {
            adjustHeight(e.target);
        }
    });

    
    document.querySelectorAll('.comments-section textarea').forEach(textarea => {
        adjustHeight(textarea);
    });

    // --- OBSŁUGA ODPOWIEDZI NA KOMENTARZE ---
    
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('reply-btn')) {
            const commentId = e.target.getAttribute('data-comment-id');
            const postId = e.target.getAttribute('data-post-id');
            const container = document.getElementById('reply-form-container-' + commentId);

            
            if (container.querySelector('form')) return;

            
            const formHtml = `
                <form action="../skrypty-php/post-interaction.php" method="POST" class="reply-form" style="margin-top: 15px;">
                    <input type="hidden" name="post_id" value="${postId}">
                    <input type="hidden" name="parent_id" value="${commentId}">
                    
                    <textarea name="content" placeholder="Napisz odpowiedź..." required 
                              style="width: 100%; min-height: 50px; background: rgba(0,0,0,0.4); 
                                     border: 1px solid var(--MAIN); color: white; padding: 10px; 
                                     border-radius: 4px; resize: none;"></textarea>
                    
                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                        <button type="submit" class="comment-submit-btn">Wyślij</button>
                        <button type="button" class="cancel-reply" style="background: none; border: none; color: #888; cursor: pointer; font-size: 0.8rem;">Anuluj</button>
                    </div>
                </form>
            `;

            container.innerHTML = formHtml;

            
            const newTextarea = container.querySelector('textarea');
            newTextarea.focus();
            adjustHeight(newTextarea);
        }

        
        if (e.target.classList.contains('cancel-reply')) {
            e.target.closest('form').remove();
        }
    });
});
