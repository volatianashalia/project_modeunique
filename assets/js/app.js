class ModeUniqueApp {
    constructor() {
        this.cart = new CartManager();
        this.search = new SearchManager();
        this.admin = new AdminManager();
        this.appointments = new AppointmentManager();
        
        this.init();
    }
    
    init() {
        this.setupCSRFToken();
        this.setupGlobalEventListeners();
        this.loadCartCount();
        if (document.querySelector('.products-page')) {
            this.search.init();
        }
        
        if (document.querySelector('.admin-page')) {
            this.admin.init();
        }
        
        if (document.querySelector('.appointment-page')) {
            this.appointments.init();
        }
    }
    
    setupCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            window.csrfToken = token;
        }
    }
    
    setupGlobalEventListeners() {
        this.setupNotifications();
 
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart')) {
                this.handleAddToCart(e.target);
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('search-input')) {
                this.handleSearchInput(e.target);
            }
        });
    }
    
    setupNotifications() {
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
    }
    
    handleAddToCart(button) {
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        const productPrice = button.dataset.productPrice;
        
        this.cart.add(productId, 1, productName, productPrice);
    }
    
    handleSearchInput(input) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.search.performSearch(input.value);
        }, 300);
    }
    
    loadCartCount() {
        this.cart.updateCount();
    }
    
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.style.minWidth = '300px';
        
        const iconMap = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        
        notification.innerHTML = `
            <i class="fas fa-${iconMap[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.getElementById('notification-container').appendChild(notification);
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 150);
            }
        }, duration);
    }
}
class CartManager {
    constructor() {
        this.apiUrl = '/api/cart';
    }
    
    async add(productId, quantity, name, price) {
        try {
            const response = await this.makeRequest('/add', 'POST', {
                product_id: productId,
                quantity: quantity,
                name: name,
                price: price
            });
            
            if (response.success) {
                this.updateCartUI(response.cart_count);
                window.app.showNotification('Produit ajouté au panier !', 'success');
            } else {
                window.app.showNotification(response.message || 'Erreur lors de l\'ajout', 'error');
            }
        } catch (error) {
            console.error('Erreur panier:', error);
            window.app.showNotification('Erreur technique', 'error');
        }
    }
    
    async remove(productId) {
        try {
            const response = await this.makeRequest('/remove', 'POST', {
                product_id: productId
            });
            
            if (response.success) {
                this.updateCartUI(response.cart_count);
                window.app.showNotification('Produit retiré du panier', 'info');
            }
        } catch (error) {
            console.error('Erreur suppression panier:', error);
        }
    }
    
    async updateQuantity(productId, quantity) {
        try {
            const response = await this.makeRequest('/update', 'POST', {
                product_id: productId,
                quantity: quantity
            });
            
            if (response.success) {
                this.updateCartUI(response.cart_count);
            }
        } catch (error) {
            console.error('Erreur mise à jour panier:', error);
        }
    }
    
    async updateCount() {
        try {
            const response = await this.makeRequest('/count', 'GET');
            if (response.success) {
                this.updateCartUI(response.cart_count);
            }
        } catch (error) {
            console.error('Erreur comptage panier:', error);
        }
    }
    
    updateCartUI(count) {
        const badge = document.getElementById('cart-badge');
        const counter = document.getElementById('cart-count');
        
        if (counter) {
            counter.textContent = count;
        }
        
        if (badge) {
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
    
    async makeRequest(endpoint, method, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.csrfToken || ''
            }
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        const response = await fetch(this.apiUrl + endpoint, options);
        return await response.json();
    }
}

class SearchManager {
    constructor() {
        this.apiUrl = '/api/products';
        this.searchTimeout = null;
    }
    
    init() {
        this.setupSearchInputs();
        this.setupFilters();
    }
    
    setupSearchInputs() {
        const searchInputs = document.querySelectorAll('.product-search');
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.handleSearchInput(e.target.value);
            });
        });
    }
    
    setupFilters() {
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) {
            categoryFilter.addEventListener('change', (e) => {
                this.handleCategoryChange(e.target.value);
            });
        }
    }
    
    handleSearchInput(query) {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch(query);
        }, 500);
    }
    
    async performSearch(query, category = null) {
        try {
            const params = new URLSearchParams();
            if (query) params.append('q', query);
            if (category) params.append('category', category);
            
            const response = await fetch(`${this.apiUrl}/search?${params.toString()}`);
            const data = await response.json();
            
            if (data.success) {
                this.updateProductGrid(data.products);
            }
        } catch (error) {
            console.error('Erreur recherche:', error);
        }
    }
    
    handleCategoryChange(categoryId) {
        const searchQuery = document.querySelector('.product-search')?.value || '';
        this.performSearch(searchQuery, categoryId);
    }
    
    updateProductGrid(products) {
        const grid = document.getElementById('productsGrid');
        if (!grid) return;
        
        if (products.length === 0) {
            grid.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-search fa-2x mb-3"></i>
                        <h4>Aucun produit trouvé</h4>
                        <p>Essayez de modifier vos critères de recherche.</p>
                    </div>
                </div>
            `;
            return;
        }
        
        grid.innerHTML = products.map(product => this.createProductCard(product)).join('');
    }
    
    createProductCard(product) {
        return `
            <div class="col-md-4 mb-4">
                <div class="card h-100 product-card">
                    ${product.image ? 
                        `<img src="uploads/products/${product.image}" class="card-img-top" style="height:250px; object-fit:cover;" alt="${product.name}">` :
                        `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:250px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>`
                    }
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text text-muted small">${product.category_name || 'Sans catégorie'}</p>
                        <p class="card-text flex-grow-1">${product.description.substring(0, 100)}...</p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 text-primary mb-0" style="color: #D4AF37 !important;">
                                    ${parseFloat(product.price).toFixed(2)} €
                                </span>
                                <small class="text-muted">Stock: ${product.stock}</small>
                            </div>
                            <div class="btn-group w-100">
                                <a href="/products/${product.id}" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                ${product.stock > 0 ? 
                                    `<button class="btn btn-primary add-to-cart" 
                                            style="background-color: #D4AF37; border-color: #D4AF37;"
                                            data-product-id="${product.id}"
                                            data-product-name="${product.name}"
                                            data-product-price="${product.price}">
                                        <i class="fas fa-cart-plus"></i> Panier
                                    </button>` :
                                    `<button class="btn btn-secondary" disabled>
                                        <i class="fas fa-times"></i> Rupture
                                    </button>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}
class AdminManager {
    constructor() {
        this.apiUrl = '/api/admin';
    }
    
    init() {
        this.setupProductManagement();
        this.setupOrderManagement();
        this.setupClientManagement();
        this.loadDashboardData();
    }
    
    setupProductManagement() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('edit-product')) {
                this.editProduct(e.target.dataset.productId);
            }
            
            if (e.target.classList.contains('delete-product')) {
                this.deleteProduct(e.target.dataset.productId);
            }
        });

        const addProductForm = document.getElementById('addProductForm');
        if (addProductForm) {
            addProductForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitProductForm(addProductForm);
            });
        }
    }
    
    async editProduct(productId) {
        try {
            const response = await fetch(`${this.apiUrl}/products/${productId}`);
            const data = await response.json();
            
            if (data.success) {
                this.populateProductForm(data.product);
                const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                modal.show();
            }
        } catch (error) {
            console.error('Erreur chargement produit:', error);
        }
    }
    
    async deleteProduct(productId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
            return;
        }
        
        try {
            const response = await fetch(`${this.apiUrl}/products/${productId}/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                document.querySelector(`[data-product-id="${productId}"]`).closest('.col-md-4').remove();
                window.app.showNotification('Produit supprimé', 'success');
            } else {
                window.app.showNotification(data.message || 'Erreur de suppression', 'error');
            }
        } catch (error) {
            console.error('Erreur suppression produit:', error);
            window.app.showNotification('Erreur technique', 'error');
        }
    }
    
    async submitProductForm(form) {
        const formData = new FormData(form);
        formData.append('csrf_token', window.csrfToken);
        
        try {
            const response = await fetch('/admin/products', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                form.reset();
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                modal.hide();
                
                window.app.showNotification('Produit enregistré', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                window.app.showNotification(data.message || 'Erreur d\'enregistrement', 'error');
            }
        } catch (error) {
            console.error('Erreur soumission formulaire:', error);
            window.app.showNotification('Erreur technique', 'error');
        }
    }
    
    populateProductForm(product) {
        const form = document.getElementById('editProductForm');
        if (!form) return;
        
        form.querySelector('[name="name"]').value = product.name || '';
        form.querySelector('[name="description"]').value = product.description || '';
        form.querySelector('[name="price"]').value = product.price || '';
        form.querySelector('[name="stock"]').value = product.stock || '';
        form.querySelector('[name="category_id"]').value = product.category_id || '';
    }
    
    setupOrderManagement() {
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('order-status')) {
                this.updateOrderStatus(e.target.dataset.orderId, e.target.value);
            }
        });
    }
    
    async updateOrderStatus(orderId, status) {
        try {
            const response = await fetch(`${this.apiUrl}/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken
                },
                body: JSON.stringify({ status: status })
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.app.showNotification('Statut mis à jour', 'success');
            }
        } catch (error) {
            console.error('Erreur mise à jour statut:', error);
        }
    }
    
    setupClientManagement() {
        const clientSearch = document.getElementById('clientSearch');
        if (clientSearch) {
            let searchTimeout;
            clientSearch.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchClients(e.target.value);
                }, 300);
            });
        }
    }
    
    async searchClients(query) {
        try {
            const response = await fetch(`${this.apiUrl}/clients/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.updateClientsTable(data.clients);
            }
        } catch (error) {
            console.error('Erreur recherche clients:', error);
        }
    }
    
    updateClientsTable(clients) {
        const tbody = document.getElementById('clientsTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = clients.map(client => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                            ${client.name.split(' ').map(n => n[0]).join('')}
                        </div>
                        <strong>${client.name}</strong>
                    </div>
                </td>
                <td>${client.email}</td>
                <td><span class="badge bg-info">${client.total_orders}</span></td>
                <td><strong>${parseFloat(client.total_spent).toFixed(2)} €</strong></td>
                <td><span class="badge bg-success">${client.status}</span></td>
            </tr>
        `).join('');
    }
    
    async loadDashboardData() {
        try {
            const response = await fetch(`${this.apiUrl}/dashboard/stats`);
            const data = await response.json();
            
            if (data.success) {
                this.updateDashboardStats(data.stats);
            }
        } catch (error) {
            console.error('Erreur chargement dashboard:', error);
        }
    }
    
    updateDashboardStats(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.getElementById(`stat-${key}`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    }
}
class AppointmentManager {
    constructor() {
        this.selectedDate = null;
        this.selectedTime = null;
        this.apiUrl = '/api/appointments';
    }
    
    init() {
        this.setupCalendar();
        this.setupTimeSlots();
        this.setupForm();
    }
    
    setupCalendar() {
        this.renderCalendar();
        this.bindCalendarEvents();
    }
    
    setupTimeSlots() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('time-slot') && !e.target.classList.contains('unavailable')) {
                this.selectTimeSlot(e.target.dataset.time);
            }
        });
    }
    
    async selectTimeSlot(time) {
        if (!this.selectedDate) return;
        const isAvailable = await this.checkAvailability(this.selectedDate, time);
        
        if (isAvailable) {
            this.selectedTime = time;
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            
            document.querySelector(`[data-time="${time}"]`).classList.add('selected');
            
            this.updateSummary();
        }
    }
    
    async checkAvailability(date, time) {
        try {
            const response = await fetch(`${this.apiUrl}/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': window.csrfToken
                },
                body: JSON.stringify({ date: date, time: time })
            });
            
            const data = await response.json();
            return data.available;
        } catch (error) {
            console.error('Erreur vérification disponibilité:', error);
            return false;
        }
    }
    
    setupForm() {
        const form = document.getElementById('appointmentForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitAppointment(form);
            });
        }
    }
    
    async submitAppointment(form) {
        if (!this.selectedDate || !this.selectedTime) {
            window.app.showNotification('Veuillez sélectionner une date et une heure', 'warning');
            return;
        }
        
        const formData = new FormData(form);
        formData.append('date', this.selectedDate);
        formData.append('time', this.selectedTime);
        formData.append('csrf_token', window.csrfToken);
        
        try {
            const response = await fetch('/appointments/book', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccessModal();
                form.reset();
                this.resetSelection();
            } else {
                window.app.showNotification(data.message || 'Erreur lors de la réservation', 'error');
            }
        } catch (error) {
            console.error('Erreur réservation:', error);
            window.app.showNotification('Erreur technique', 'error');
        }
    }
    
    showSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.classList.add('show');
        }
    }
    
    resetSelection() {
        this.selectedDate = null;
        this.selectedTime = null;
        
        document.querySelectorAll('.selected').forEach(el => {
            el.classList.remove('selected');
        });
    }
    
    updateSummary() {
        const summary = document.getElementById('appointmentSummary');
        if (summary && this.selectedDate && this.selectedTime) {
            summary.style.display = 'block';
            
            const dateElement = document.getElementById('summaryDate');
            const timeElement = document.getElementById('summaryTime');
            
            if (dateElement) {
                dateElement.textContent = new Date(this.selectedDate).toLocaleDateString('fr-FR', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            
            if (timeElement) {
                timeElement.textContent = this.selectedTime;
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', function() {
    window.app = new ModeUniqueApp();
});
window.ModeUniqueUtils = {
    formatPrice: function(price) {
        return parseFloat(price).toFixed(2) + ' €';
    },
    
    formatDate: function(dateString) {
        return new Date(dateString).toLocaleDateString('fr-FR');
    },
    
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }
};