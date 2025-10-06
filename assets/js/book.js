class AppointmentBooking {
    constructor() {
        this.selectedDate = null;
        this.selectedTime = null;
        this.currentDate = new Date();
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.availableSlots = []; // Sera chargé dynamiquement depuis le serveur
        
        this.init();
    }
    
    init() {
        this.renderCalendar();
        this.bindEvents();
        this.updateSubmitButton();
    }
    
    bindEvents() {
        // Navigation du calendrier
        document.getElementById('prevMonth').addEventListener('click', () => {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }
            this.renderCalendar();
        });
        
        document.getElementById('nextMonth').addEventListener('click', () => {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }
            this.renderCalendar();
        });
        
        // Soumission du formulaire
        document.getElementById('appointmentForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
        
        // Validation du formulaire
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                this.clearFieldError(input);
                this.updateSubmitButton();
                this.updateSummary();
            });
        });
        
        // Modal de succès
        document.getElementById('closeModal').addEventListener('click', () => {
            this.closeSuccessModal();
        });
        
        document.getElementById('successModal').addEventListener('click', (e) => {
            if (e.target.id === 'successModal') {
                this.closeSuccessModal();
            }
        });
    }
    
    renderCalendar() {
        const grid = document.getElementById('calendarGrid');
        const monthNames = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        const dayNames = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        
        document.getElementById('currentMonth').textContent = 
            `${monthNames[this.currentMonth]} ${this.currentYear}`;
        
        grid.innerHTML = '';
        
        // Ajouter les en-têtes des jours
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day header';
            dayHeader.textContent = day;
            grid.appendChild(dayHeader);
        });
        
        // Obtenir le premier jour du mois et le nombre de jours
        const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Ajouter les cellules vides avant le début du mois
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            grid.appendChild(emptyDay);
        }
        
        // Ajouter les jours du mois
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            const dayDate = new Date(this.currentYear, this.currentMonth, day);
            dayDate.setHours(0, 0, 0, 0);
            const dateString = this.formatDate(dayDate);
            
            // Vérifier si le jour est dans le passé
            if (dayDate < today) {
                dayElement.classList.add('past');
            } else {
                dayElement.classList.add('available');
                dayElement.addEventListener('click', () => this.selectDate(dayDate));
            }
            
            // Vérifier si cette date est sélectionnée
            if (this.selectedDate && this.formatDate(this.selectedDate) === dateString) {
                dayElement.classList.add('selected');
            }
            
            grid.appendChild(dayElement);
        }
    }
    
    async selectDate(date) {
        this.selectedDate = date;
        this.selectedTime = null;
        this.renderCalendar();
        await this.loadAvailableSlots();
        this.renderTimeSlots();
        this.updateSubmitButton();
        this.updateSummary();
    }
    
    async loadAvailableSlots() {
        const dateString = this.formatDate(this.selectedDate);
        const container = document.getElementById('timeSlots');
        
        // Afficher un indicateur de chargement
        container.innerHTML = '<p class="loading">Chargement des créneaux disponibles...</p>';
        
        try {
            const response = await fetch(`get_availability.php?date=${dateString}`);
            
            if (!response.ok) {
                throw new Error('Erreur lors du chargement des créneaux');
            }
            
            this.availableSlots = await response.json();
            
        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = '<p class="error-message">Erreur lors du chargement des créneaux. Veuillez réessayer.</p>';
            this.availableSlots = [];
        }
    }
    
    renderTimeSlots() {
        const container = document.getElementById('timeSlots');
        
        if (this.availableSlots.length === 0) {
            container.innerHTML = '<p class="no-slots">Aucun créneau disponible pour cette date.</p>';
            return;
        }
        
        container.innerHTML = '';
        
        this.availableSlots.forEach(time => {
            const slot = document.createElement('div');
            slot.className = 'time-slot';
            slot.textContent = time;
            slot.addEventListener('click', () => this.selectTime(time, slot));
            container.appendChild(slot);
        });
    }
    
    selectTime(time, slotElement) {
        // Retirer la sélection précédente
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        // Ajouter la sélection au créneau cliqué
        slotElement.classList.add('selected');
        this.selectedTime = time;
        this.updateSubmitButton();
        this.updateSummary();
    }
    
    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    formatDateDisplay(date) {
        return date.toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        let isValid = true;
        let errorMessage = '';
        
        switch (name) {
            case 'fullName':
                if (!value) {
                    errorMessage = 'Le nom complet est requis';
                    isValid = false;
                } else if (value.length < 2) {
                    errorMessage = 'Le nom doit contenir au moins 2 caractères';
                    isValid = false;
                }
                break;
                
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!value) {
                    errorMessage = "L'email est requis";
                    isValid = false;
                } else if (!emailRegex.test(value)) {
                    errorMessage = 'Veuillez entrer une adresse email valide';
                    isValid = false;
                }
                break;
                
            case 'phone':
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
                if (!value) {
                    errorMessage = 'Le numéro de téléphone est requis';
                    isValid = false;
                } else if (!phoneRegex.test(value)) {
                    errorMessage = 'Veuillez entrer un numéro de téléphone valide';
                    isValid = false;
                }
                break;
                
            case 'service':
                if (!value) {
                    errorMessage = 'Veuillez sélectionner un service';
                    isValid = false;
                }
                break;
        }
        
        const errorElement = document.getElementById(name + 'Error');
        const formGroup = field.closest('.form-group');
        
        if (isValid) {
            if (errorElement) errorElement.textContent = '';
            if (formGroup) {
                formGroup.classList.remove('has-error');
                formGroup.classList.add('has-success');
            }
        } else {
            if (errorElement) errorElement.textContent = errorMessage;
            if (formGroup) {
                formGroup.classList.add('has-error');
                formGroup.classList.remove('has-success');
            }
        }
        
        return isValid;
    }
    
    clearFieldError(field) {
        const errorElement = document.getElementById(field.name + 'Error');
        const formGroup = field.closest('.form-group');
        
        if (errorElement) {
            errorElement.textContent = '';
        }
        if (formGroup) {
            formGroup.classList.remove('has-error');
        }
    }
    
    validateForm() {
        const requiredFields = ['fullName', 'email', 'phone', 'service'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    updateSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('appointmentForm');
        const formData = new FormData(form);
        
        const hasRequiredFields = formData.get('fullName') && 
                                 formData.get('email') && 
                                 formData.get('phone') && 
                                 formData.get('service');
        
        const hasDateTime = this.selectedDate && this.selectedTime;
        
        submitBtn.disabled = !(hasRequiredFields && hasDateTime);
    }
    
    updateSummary() {
        const summary = document.getElementById('appointmentSummary');
        const service = document.getElementById('service').value;
        
        if (this.selectedDate && this.selectedTime && service) {
            summary.style.display = 'block';
            document.getElementById('summaryDate').textContent = this.formatDateDisplay(this.selectedDate);
            document.getElementById('summaryTime').textContent = this.selectedTime;
            
            const serviceSelect = document.getElementById('service');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            document.getElementById('summaryService').textContent = selectedOption.textContent;
        } else {
            summary.style.display = 'none';
        }
    }
    
    async handleSubmit() {
        console.log('=== DÉBUT DE LA SOUMISSION ===');
        
        if (!this.validateForm()) {
            alert('Veuillez corriger les erreurs dans le formulaire.');
            return;
        }
        
        if (!this.selectedDate || !this.selectedTime) {
            alert('Veuillez sélectionner une date et une heure pour votre rendez-vous.');
            return;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Réservation en cours...';
        submitBtn.disabled = true;
        
        const form = document.getElementById('appointmentForm');
        const formData = new FormData(form);
        formData.append('date', this.formatDate(this.selectedDate));
        formData.append('time', this.selectedTime);

        // Afficher les données envoyées
        console.log('Données du formulaire:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        try {
            console.log('Envoi de la requête vers process_appointment.php...');
            
            const response = await fetch('process_appointment.php', {
                method: 'POST',
                body: formData
            });

            console.log('Réponse reçue:', response.status, response.statusText);

            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Réponse non-JSON reçue:', text);
                alert('Erreur: Le serveur a renvoyé une réponse invalide. Vérifiez la console pour plus de détails.');
                throw new Error('Réponse invalide du serveur');
            }

            const result = await response.json();
            console.log('Résultat JSON:', result);

            if (result.success) {
                console.log('✓ Succès! ID du rendez-vous:', result.appointment_id);
                this.showSuccessModal();
            } else {
                console.error('✗ Échec:', result.message);
                alert('Erreur: ' + (result.message || 'Une erreur inconnue est survenue.'));
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        } catch (error) {
            console.error('Erreur de soumission:', error);
            alert('Une erreur est survenue lors de la réservation. Consultez la console pour plus de détails.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
        
        console.log('=== FIN DE LA SOUMISSION ===');
    }
    
    showSuccessModal() {
        document.getElementById('successModal').classList.add('show');
    }
    
    closeSuccessModal() {
        document.getElementById('successModal').classList.remove('show');
        window.location.href = 'confirmation.php';
    }
    
    resetForm() {
        document.getElementById('appointmentForm').reset();
        this.selectedDate = null;
        this.selectedTime = null;
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
        });
        document.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('has-error', 'has-success');
        });
        document.querySelectorAll('.error-message').forEach(error => {
            error.textContent = '';
        });
        
        document.getElementById('timeSlots').innerHTML = 
            '<p class="select-date-first">Veuillez d\'abord sélectionner une date</p>';
        document.getElementById('appointmentSummary').style.display = 'none';
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.textContent = 'Réserver le Rendez-vous';
        submitBtn.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AppointmentBooking();
});
