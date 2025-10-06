document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!contactForm.checkValidity()) {
                contactForm.classList.add('was-validated');
                return;
            }
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
            
            try {
                const formData = new FormData(contactForm);
                
                const response = await fetch('process_contact.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                formMessage.style.display = 'block';
                
                if (result.success) {
                    formMessage.className = 'alert alert-success';
                    formMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + result.message;
                    contactForm.reset();
                    contactForm.classList.remove('was-validated');
                } else {
                    formMessage.className = 'alert alert-danger';
                    formMessage.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>' + result.message;
                }
                
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
            } catch (error) {
                console.error('Erreur:', error);
                formMessage.style.display = 'block';
                formMessage.className = 'alert alert-danger';
                formMessage.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Une erreur est survenue. Veuillez réessayer.';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
});