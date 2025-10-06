let clients = [];
let orders = [];
let appointments = [];
let products = [];
let creations = [];
$(document).ready(function() {
    initializeApp();
    loadSampleData();
    bindEvents();
    loadDashboard();
});
$(document).ready(function() {
    $('.logout-link').on('click', function(e) {
        console.log('CLIC SUR LOGOUT DÉTECTÉ');
        console.log('href:', $(this).attr('href'));
    });
});


function initializeApp() {

    const today = new Date().toISOString().split('T')[0];
    $('input[type="date"]').val(today);
    $('[data-bs-toggle="tooltip"]').tooltip();
}

function bindEvents() {

    $('.nav-link:not(.logout-link)').on('click', function(e) {
        e.preventDefault();
        const section = $(this).data('section');
        if (section) {
            showSection(section);
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('#pageTitle').text($(this).find('span').text());
        }
    });

    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
    });
    

    $('#clientSearch').on('keyup', function() {
        filterClients();
    });
    
    $('#clientFilter').on('change', function() {
        filterClients();
    });
    
    $('#orderSearch').on('keyup', function() {
        filterOrders();
    });
    
    $('#orderStatusFilter, #orderDateFilter').on('change', function() {
        filterOrders();
    });
    
    $('#productSearch').on('keyup', function() {
        filterProducts();
    });
    
    $('#categoryFilter, #availabilityFilter').on('change', function() {
        filterProducts();
    });
    
    $('#appointmentDateFilter, #appointmentTypeFilter').on('change', function() {
        filterAppointments();
    });
    
    $('#saveClient').on('click', function() {
        saveClient();
    });
    
    $('#saveOrder').on('click', function() {
        saveOrder();
    });
    
    $('#saveAppointment').on('click', function() {
        saveAppointment();
    });
    
    $('#saveProduct').on('click', function() {
        saveProduct();
    });

}
$(document).ready(function() {
    $(document).on('change', '.update-order-status', function() {
        console.log("Changement de statut détecté !"); 
        
        const selectElement = $(this);
        const orderId = selectElement.data('order-id');
        const newStatus = selectElement.val();
        const currentStatus = selectElement.data('current-status');

        if (newStatus === currentStatus) {
            return; 
        }

        let confirmMessage = `Voulez-vous vraiment changer le statut de la commande #${orderId} ?`;
        
        if (newStatus === 'shipped') {
            confirmMessage = `Marquer la commande #${orderId} comme EXPÉDIÉE ?\n\nUn email sera envoyé au client.`;
        } else if (newStatus === 'delivered') {
            confirmMessage = `Marquer la commande #${orderId} comme LIVRÉE ?\n\nUn email de confirmation sera envoyé au client.`;
        }

        if (!confirm(confirmMessage)) {
            selectElement.val(currentStatus);
            return;
        }

        selectElement.prop('disabled', true);

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('new_status', newStatus);
        formData.append('csrf_token', window.csrfToken);
        fetch('CRUD/update_order_status.php', {
    method: 'POST',
    body: formData
})
.then(response => {
    return response.text(); 
})
.then(data => {
    console.log('=== RÉPONSE COMPLÈTE DU SERVEUR ===');
    console.log(data);
    console.log('=== FIN RÉPONSE ===');
    
    try {
        const jsonData = JSON.parse(data);
        console.log('JSON parsé:', jsonData);
    } catch (e) {
        console.error('Impossible de parser JSON. Erreur PHP probable.');
    }
})
    });
});

function showSection(sectionId) {
    $('.section').removeClass('active');
    $(`#${sectionId}`).addClass('active');
    
    switch(sectionId) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'clients':
            loadClients();
            break;
        case 'orders':
            loadOrders();
            break;
        case 'appointments':
            loadAppointments();
            break;
        case 'products':
            loadProducts();
            break;
            case 'creations':  
            loadCreations();
            break;
        case 'reviews':
            break;
    }
}
function loadSampleData() {

   $.ajax({
    url: '../../config/get_clients.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        console.log('=== RÉPONSE AJAX CLIENTS ===');
        console.log('Données brutes:', data);
        console.log('Nombre de clients:', Array.isArray(data) ? data.length : 0);
        
        clients = Array.isArray(data) ? data : (data.data || []);
        console.log('Clients après traitement:', clients);
        
        loadClients();
        populateClientSelect();
    },
    error: function(xhr, status, error) {
        console.error('=== ERREUR AJAX CLIENTS ===');
        console.error('Status:', status);
        console.error('Error:', error);
        console.error('Response:', xhr.responseText);
        showNotification('Impossible de charger les clients', 'danger');
    }
});
$.ajax({
    url: '../../config/get_orders.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        console.log('=== RÉPONSE AJAX COMMANDES ===');
        console.log('Type:', typeof data);
        console.log('Contenu:', data);
        console.log('Est un tableau?', Array.isArray(data));
        
        if (data && data.error) {
            console.error('Erreur serveur:', data.error);
            showNotification('Erreur: ' + data.error, 'danger');
            return;
        }
        
        orders = Array.isArray(data) ? data : (data.data || []);
        console.log('Orders final:', orders);
        console.log('Nombre de commandes:', orders.length);
        
        loadOrders();
    },
    error: function(xhr, status, error) {
        console.error('=== ERREUR AJAX ===');
        console.error('Status:', status);
        console.error('Error:', error);
        console.error('Response:', xhr.responseText);
        console.error('Status code:', xhr.status);
        showNotification('Impossible de charger les commandes', 'danger');
    }
});


    $.ajax({
        url: '../../config/get_appointments.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            appointments = Array.isArray(data) ? data : (data.data || []);
            loadAppointments();
        },
        error: function() {
            showNotification('Impossible de charger les rendez-vous', 'danger');
        }
    });


    $.ajax({
        url: '../../config/get_products.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            products = Array.isArray(data) ? data : (data.data || []);
            loadProducts();
        },
        error: function() {
            showNotification('Impossible de charger les produits', 'danger');
        }
    });
    $.ajax({
        url: '../../config/get_creations.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            creations = Array.isArray(data) ? data : (data.data || []);
            loadCreations();
        },
        error: function() {
            console.log('Impossible de charger les créations');
        }
    });
}

function loadDashboard() {
    loadRecentOrders();
    loadUpcomingAppointments();
}

function loadRecentOrders() {
    const recentOrders = orders.slice(0, 5);
    let html = '';
    
    recentOrders.forEach(order => {
        const statusClass = getStatusClass(order.status);
        html += `
            <tr>
                <td><strong>${order.order_number}</strong></td>
                <td>${order.clientName}</td>
                <td>${order.product}</td>
                <td><span class="status-badge ${statusClass}">${order.status}</span></td>
                <td><strong>${parseFloat(order.amount).toFixed(2)} Ar</strong></td>
            </tr>
        `;
    });
    
    $('#recentOrdersTable').html(html);
}

function loadUpcomingAppointments() {
    const upcomingAppointments = appointments.slice(0, 4);
    let html = '';
    
    upcomingAppointments.forEach(appointment => {
        const date = new Date(appointment.date);
        const dateStr = date.toLocaleDateString();
        const isToday = dateStr === new Date().toLocaleDateString();
        
        html += `
            <div class="appointment-item">
                <div class="appointment-time">${appointment.time}</div>
                <div class="appointment-details">
                    <div class="appointment-client">${appointment.clientName}</div>
                    <div class="appointment-type">${appointment.type}</div>
                    <small class="text-muted">${isToday ? 'Today' : dateStr}</small>
                </div>
            </div>
        `;
    });
    
    $('#upcomingAppointments').html(html);
}

function loadClients() {
    displayClients(clients);
    populateClientSelect();
    $('#client-count-sidebar').text(`Clients (${clients.length})`);
}
function displayClients(clientsToShow) {
    console.log('displayClients() appelée avec', clientsToShow.length, 'clients'); 
    let html = '';
    
    if (!clientsToShow || clientsToShow.length === 0) {
        html = `<tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                        Aucun client à afficher
                    </td>
                </tr>`;
    } else {
        clientsToShow.forEach(client => {
            const statusClass = getStatusClass(client.status || 'Active');
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-weight: bold;">
                                    ${getInitials(client.name)}
                                </div>
                            </div>
                            <div>
                                <strong>${client.name}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${client.email}</td>
                    <td><span class="status-badge ${statusClass}">${client.status || 'Active'}</span></td>
                    <td>${new Date(client.created_at).toLocaleDateString('fr-FR')}</td>
                </tr>
            `;
        });
    }
    
    $('#clientsTable').html(html);
    console.log('HTML inséré dans #clientsTable');
}

function filterClients() {
    const searchTerm = $('#clientSearch').val().toLowerCase();
    const statusFilter = $('#clientFilter').val();
    
    let filteredClients = clients.filter(client => {
        const matchesSearch = client.name.toLowerCase().includes(searchTerm) || 
                             client.email.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || client.status === statusFilter;
        
        return matchesSearch && matchesStatus;
    });
    
    displayClients(filteredClients);
}

function saveClient() {
    const formData = new FormData($('#addClientForm')[0]);
    const clientData = {
        id: clients.length + 1,
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        location: formData.get('location'),
        preferences: formData.get('preferences'),
        totalOrders: 0,
        totalSpent: 0,
        lastOrder: null,
        status: 'Active'
    };
    
    clients.push(clientData);
    loadClients();
    $('#addClientModal').modal('hide');
    $('#addClientForm')[0].reset();
    
    showNotification('Client added successfully!', 'success');
}

function populateClientSelect() {
    const clientSelects = $('select[name="clientId"]');
    let options = '<option value="">Select Client</option>';
    
    clients.forEach(client => {
        options += `<option value="${client.id}">${client.name}</option>`;
    });
    
    clientSelects.html(options);
}


function loadOrders() {
    
    console.log('loadOrders() appelée avec', orders.length, 'commandes');
    displayOrders(orders);
}
function displayOrders(ordersToShow) {
    let html = '';
    
    if (!ordersToShow || ordersToShow.length === 0) {
        html = `<tr><td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    Aucune commande à afficher
                </td></tr>`;
    } else {
        ordersToShow.forEach(order => {
            const statusLabels = {
                'pending': 'En attente',
                'processing': 'En traitement',
                'shipped': 'Expédiée',
                'delivered': 'Livrée',
                'cancelled': 'Annulée'
            };
            const paymentLabels = {
                'carte': 'Carte bancaire',
                'paypal': 'PayPal',
                'virement': 'Virement',
                'especes': 'Espèces'
            };

            const paymentLabel = paymentLabels[order.payment_method] || 'N/A';
            const clientName = (order.first_name || '') + ' ' + (order.last_name || '');

            let statusSelectHtml = `
                <select class="form-select form-select-sm update-order-status" 
                        data-order-id="${order.id}" 
                        data-current-status="${order.status}"
                        style="min-width: 150px;">
            `;
            
            for (const [statusKey, statusLabel] of Object.entries(statusLabels)) {
                const selected = order.status === statusKey ? 'selected' : '';
                statusSelectHtml += `<option value="${statusKey}" ${selected}>${statusLabel}</option>`;
            }
            
            statusSelectHtml += `</select>`;

            html += `
                <tr>
                    <td><strong>#${order.order_number || order.id}</strong></td>
                    <td>${clientName || 'N/A'}</td>
                    <td><strong>${parseFloat(order.total_amount).toFixed(2)} Ariary</strong></td>
                    <td>${statusSelectHtml}</td>
                    <td>${paymentLabel}</td>
                    <td>${new Date(order.created_at).toLocaleDateString('fr-FR')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(${order.id})" title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#ordersTable').html(html);
    console.log('HTML inséré dans #ordersTable');
}
function filterOrders() {
    const searchTerm = $('#orderSearch').val().toLowerCase();
    const statusFilter = $('#orderStatusFilter').val();
    const dateFilter = $('#orderDateFilter').val();
    
    let filteredOrders = orders.filter(order => {
        const clientName = ((order.first_name || '') + ' ' + (order.last_name || '')).toLowerCase();
        const orderNumber = (order.order_number || '').toLowerCase();

        const matchesSearch = orderNumber.includes(searchTerm) || 
                             clientName.includes(searchTerm);
        const matchesStatus = !statusFilter || order.status === statusFilter;
        const matchesDate = !dateFilter || order.created_at.startsWith(dateFilter);

        return matchesSearch && matchesStatus && matchesDate;
    });
    
    displayOrders(filteredOrders);
}

function saveOrder() {
    const formData = new FormData($('#addOrderForm')[0]);
    const clientId = parseInt(formData.get('clientId'));
    const client = clients.find(c => c.id === clientId);
    
    const orderData = {
        id: `ORD-${String(orders.length + 1).padStart(3, '0')}`,
        clientId: clientId,
        clientName: client.name,
        product: formData.get('product'),
        orderDate: formData.get('orderDate'),
        dueDate: formData.get('dueDate'),
        status: formData.get('status'),
        amount: parseFloat(formData.get('amount')),
        notes: formData.get('notes')
    };
    
    orders.push(orderData);
    
    client.totalOrders++;
    client.totalSpent += orderData.amount;
    client.lastOrder = orderData.orderDate;
    
    loadOrders();
    $('#addOrderModal').modal('hide');
    $('#addOrderForm')[0].reset();
    
    showNotification('Order added successfully!', 'success');
}

function loadAppointments() {
    displayAppointments(appointments);
    loadTodaySchedule();
}

function displayAppointments(appointmentsToShow) {
    let html = '';
    
    appointmentsToShow.forEach(appointment => {
        const statusClass = getStatusClass(appointment.status);
        const dateTime = `${formatDate(appointment.date)} ${appointment.time}`;
        
        html += `
            <tr>
                <td>${dateTime}</td>
                <td>${appointment.clientName}</td>
                <td>${appointment.type}</td>
                <td><span class="status-badge ${statusClass}">${appointment.status}</span></td>
                <td>${appointment.notes || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editAppointment(${appointment.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAppointment(${appointment.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#appointmentsTable').html(html);
}

function filterAppointments() {
    const dateFilter = $('#appointmentDateFilter').val();
    const typeFilter = $('#appointmentTypeFilter').val();
    
    let filteredAppointments = appointments.filter(appointment => {
        const matchesDate = !dateFilter || appointment.date === dateFilter;
        const matchesType = !typeFilter || appointment.type === typeFilter;
        
        return matchesDate && matchesType;
    });
    
    displayAppointments(filteredAppointments);
}

function loadTodaySchedule() {
    const today = new Date().toISOString().split('T')[0];
    const todayAppointments = appointments.filter(app => app.date === today);
    
    let html = '';
    
    if (todayAppointments.length === 0) {
        html = '<p class="text-muted">No appointments scheduled for today.</p>';
    } else {
        todayAppointments.forEach(appointment => {
            html += `
                <div class="appointment-item">
                    <div class="appointment-time">${appointment.time}</div>
                    <div class="appointment-details">
                        <div class="appointment-client">${appointment.clientName}</div>
                        <div class="appointment-type">${appointment.type}</div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#todaySchedule').html(html);
}

function saveAppointment() {
    const formData = new FormData($('#addAppointmentForm')[0]);
    const clientId = parseInt(formData.get('clientId'));
    const client = clients.find(c => c.id === clientId);
    
    const appointmentData = {
        id: appointments.length + 1,
        clientId: clientId,
        clientName: client.name,
        date: formData.get('date'),
        time: formData.get('time'),
        type: formData.get('type'),
        status: 'Scheduled',
        notes: formData.get('notes')
    };
    
    appointments.push(appointmentData);
    loadAppointments();
    $('#addAppointmentModal').modal('hide');
    $('#addAppointmentForm')[0].reset();
    
    showNotification('Appointment scheduled successfully!', 'success');
}


function loadProducts() {
    displayProducts(products);
}

function displayProducts(productsToShow) {
    let html = '';
    
    productsToShow.forEach(product => {
        const availabilityClass = product.availability === 'Available' ? 'text-success' : 'text-danger';
        const stockStatus = product.stock > 0 ? `${product.stock} in stock` : 'Out of stock';
        const sizes = product.sizes || product.sizes_array || [];

        html += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <img src="${product.image || ''}" alt="${product.name}" class="product-image">
                    <div class="product-info">
                        <h5 class="product-title">${product.name}</h5>
                        <p class="product-category">${product.category}</p>
                        <p class="product-price">${product.price}Ar</p>
                        <p class="product-stock ${availabilityClass}">${stockStatus}</p>
                        <p class="text-muted small">${product.description}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editProduct(${product.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#productsGrid').html(html);
}


function createProductCard(product) {
    const imagePath = `../../${product.image}`; 
    const description = escapeHtml(rawDescription); 
     const availabilityClass = product.availability === 'Available' ? 'text-danger' : 'text-success';
        const stockStatus = product.stock > 0 ? `${product.stock} in stock` : 'Out of stock';
    return `
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="${imagePath}" 
                     class="card-img-top" 
                     alt="${product.name}" 
                     style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title">${product.name}</h5>
                    <p class="text-muted small">${product.category_name || 'Non catégorisé'}</p>
                    
                    <p class="fw-bold fs-5 text-primary mb-2">
                        ${Number(product.price).toFixed(2)} Ariary
                    </p>
                    
                 <p class="product-stock ${availabilityClass}">${stockStatus}</p>
                    <p class="text-muted small">${product.description}</p>
                </div>
                 <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editProduct(${product.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
            </div>
        </div>
    `;
}

function filterProducts() {
    const searchTerm = $('#productSearch').val().toLowerCase();
    const categoryFilter = $('#categoryFilter').val();
    const availabilityFilter = $('#availabilityFilter').val();
    
    let filteredProducts = products.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                             product.description.toLowerCase().includes(searchTerm);
        const matchesCategory = !categoryFilter || product.category === categoryFilter;
        const matchesAvailability = !availabilityFilter || product.availability === availabilityFilter;
        
        return matchesSearch && matchesCategory && matchesAvailability;
    });
    
    displayProducts(filteredProducts);
}

function saveProduct() {
    const formData = new FormData($('#addProductForm')[0]);
    const sizes = [];
    $('input[name="sizes"]:checked').each(function() {
        sizes.push($(this).val());
    });
    
    const productData = {
        id: products.length + 1,
        name: formData.get('name'),
        category: formData.get('category'),
        price: parseFloat(formData.get('price')),
        stock: parseInt(formData.get('stock')),
        description: formData.get('description'),
        sizes: sizes,
        availability: parseInt(formData.get('stock')) > 0 ? 'Available' : 'Out of Stock'
    };
    
    products.push(productData);
    loadProducts();
    $('#addProductModal').modal('hide');
    $('#addProductForm')[0].reset();
    
    showNotification('Product added successfully!', 'success');
}

function getStatusClass(status) {
    const statusClasses = {
        'Active': 'status-active',
        'Inactive': 'status-inactive',
        'VIP': 'status-vip',
        'Pending': 'status-pending',
        'In Progress': 'status-pending',
        'Completed': 'status-completed',
        'Cancelled': 'status-cancelled',
        'Scheduled': 'status-active'
    };
    
    return statusClasses[status] || 'status-pending';
}

function getInitials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function showNotification(message, type = 'success') {

    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    setTimeout(() => {
        notification.alert('close');
    }, 3000);
}

function editClient(clientId) {
    showNotification('Edit client functionality would be implemented here.', 'info');
}

function deleteClient(clientId) {
    if (confirm('Are you sure you want to delete this client?')) {
        clients = clients.filter(c => c.id !== clientId);
        loadClients();
        showNotification('Client deleted successfully!', 'success');
    }
}

function editOrder(orderId) {
    showNotification('Edit order functionality would be implemented here.', 'info');
}

function deleteOrder(orderId) {
    if (confirm('Are you sure you want to delete this order?')) {
        orders = orders.filter(o => o.id !== orderId);
        loadOrders();
        showNotification('Order deleted successfully!', 'success');
    }
}

function editAppointment(appointmentId) {
    showNotification('Edit appointment functionality would be implemented here.', 'info');
}

function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        appointments = appointments.filter(a => a.id !== appointmentId);
        loadAppointments();
        showNotification('Appointment deleted successfully!', 'success');
    }
}

function editProduct(productId) {
    const product = products.find(p => p.id == productId);
    
    if (product) {
        populateEditForm(product);
        const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
        editModal.show();
    } else {
        fetch('../../config/get_products.php?id=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditForm(data.product);
                const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                editModal.show();
            } else {
                showNotification('Erreur : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la récupération des données', 'danger');
        });
    }
}

console.log('Modal exists:', document.getElementById('editCreationModal'));
console.log('Form exists:', document.getElementById('editCreationForm'));
console.log('Creations array:', creations);
function deleteProduct(productId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        console.log('Deleting product ID:', productId); 
        
        const formData = new FormData();
        formData.append('id', productId);
        formData.append('csrf_token', window.csrfToken);

        fetch('../../views/users/CRUD/delete_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Delete response status:', response.status); 
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data); 
            if (data.success) {
                showNotification('Produit supprimé avec succès !', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            showNotification('Une erreur réseau est survenue.', 'danger');
        });
    }
}

$(document).ready(function() {
    setTimeout(function() {
        const addCreationForm = document.getElementById('addCreationForm');
        if (addCreationForm) {
            addCreationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Formulaire soumis'); 
                
                const formData = new FormData(this);
                
                fetch('../../views/users/CRUD/add_creation.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Réponse reçue:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Données:', data);
                    if (data.success) {
                        const modal = document.getElementById('addCreationModal');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        
                        this.reset();
                        
                        alert('Création ajoutée avec succès !');
                        
                        setTimeout(() => location.reload(), 500);
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur de communication avec le serveur');
                });
            });
        } else {
            console.error('Formulaire addCreationForm non trouvé');
        }
    }, 1000);
});
$(document).ready(function() {
    $(document).on('submit', '#editCreationForm', function(e) {
        e.preventDefault();
        console.log('Form editCreationForm submitted!'); 
        
        const formData = new FormData(this);
        
        fetch('../../views/users/CRUD/edit_creation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Edit response:', data);
            if (data.success) {
                $('#editCreationModal').modal('hide');
                alert('Création modifiée avec succès !');
                setTimeout(() => location.reload(), 500);
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de communication avec le serveur');
        });
    });
});

function editCreation(creationId) {
    console.log('Editing creation ID:', creationId); 
    const creation = creations.find(c => c.id == creationId);
    
    if (creation) {
        document.getElementById('editCreationId').value = creation.id;
        document.querySelector('#editCreationForm [name="title"]').value = creation.title || '';
        document.querySelector('#editCreationForm [name="description"]').value = creation.description || '';
        
        const editModal = new bootstrap.Modal(document.getElementById('editCreationModal'));
        editModal.show();
    } else {
        console.error('Création non trouvée:', creationId);
        alert('Création non trouvée');
    }
}
function deleteCreation(creationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette creation ?')) {
        console.log('Deleting creation ID:', creationId); 
        const formData = new FormData();
        formData.append('id',creationId);
        formData.append('csrf_token', window.csrfToken);

        fetch('../../views/users/CRUD/delete_creation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Delete response status:', response.status); 
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data); 
            if (data.success) {
                showNotification('Produit supprimé avec succès !', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            showNotification('Une erreur réseau est survenue.', 'danger');
        });
    }
}
function attachProductEvents() {
    document.querySelectorAll('.deleteProduct').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                const productId = this.getAttribute('data-id');
                
                const formData = new FormData();
                formData.append('id', productId);
                formData.append('csrf_token', window.csrfToken);
                
                fetch('CRUD/delete_product.php', {  
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produit supprimé avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        });
    });

    document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../../views/users/CRUD/edit_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#editProductModal').modal('hide');
            const index = products.findIndex(p => p.id == data.product.id);
            if (index !== -1) {
                products[index] = data.product;
            }

            displayProducts(products);
            
            showNotification('Produit modifié avec succès !', 'success');
        }
    });
});
document.querySelectorAll('.deleteProduct').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
            const productId = this.getAttribute('data-id');
            
            const formData = new FormData();
            formData.append('id', productId);
            formData.append('csrf_token', window.csrfToken);
            
            fetch('../../views/users/CRUD/delete_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                    alert('Produit supprimé avec succès !');
                } else {
                    alert('Erreur : ' + data.message);
                }
            });
        }
    });
});

};

function populateEditForm(product) {
    console.log('Populating form with product:', product); 
    
    document.getElementById('editProductId').value = product.id;
    document.querySelector('#editProductForm [name="name"]').value = product.name || '';
    document.querySelector('#editProductForm [name="description"]').value = product.description || '';
    document.querySelector('#editProductForm [name="price"]').value = product.price || '';
    document.querySelector('#editProductForm [name="stock"]').value = product.stock || '';
    document.querySelector('#editProductForm [name="category_id"]').value = product.category_id || '';
    document.querySelectorAll('#editProductForm input[name="sizes[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    if (product.size) {
        try {
            const sizes = typeof product.size === 'string' ? JSON.parse(product.size) : product.size;
            if (Array.isArray(sizes)) {
                sizes.forEach(size => {
                    const checkbox = document.querySelector(`#editProductForm input[name="sizes[]"][value="${size}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        } catch (e) {
            console.error('Erreur parsing sizes:', e);
        }
    }
}
$(document).ready(function() {
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted!'); 
        
        const formData = new FormData(this);
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('../../views/users/CRUD/edit_product.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Produit modifié avec succès !');
                $('#editProductModal').modal('hide');
                location.reload(); 
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de communication avec le serveur');
        });
    });
    
    $(document).on('click', '.editProduct', function(e) {
        e.preventDefault();
        const productId = $(this).data('id');
        console.log('Editing product ID:', productId);
        $('#editProductId').val(productId);
        $('#editProductModal').modal('show');
    });
});

function populateEditForm(product) {
    console.log('Populating form with:', product);
    
    $('#editProductId').val(product.id);
    $('#editProductForm [name="name"]').val(product.name || '');
    $('#editProductForm [name="description"]').val(product.description || '');
    $('#editProductForm [name="price"]').val(product.price || '');
    $('#editProductForm [name="stock"]').val(product.stock || '');
    $('#editProductForm [name="category_id"]').val(product.category_id || '');
    $('#editProductForm input[name="sizes[]"]').prop('checked', false);
    if (product.size) {
        try {
            const sizes = typeof product.size === 'string' ? JSON.parse(product.size) : product.size;
            if (Array.isArray(sizes)) {
                sizes.forEach(size => {
                    $(`#editProductForm input[name="sizes[]"][value="${size}"]`).prop('checked', true);
                });
            }
        } catch (e) {
            console.error('Erreur parsing sizes:', e);
        }
    }
}
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../../views/users/CRUD/add_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            modal.hide();
            this.reset();
            if (data.product) {
                products.unshift(data.product); 

                const newCard = createProductCard(data.product);
                $('#productsGrid').prepend(newCard);
            }
            
            showNotification('Produit ajouté avec succès !', 'success');
        } else {
            showNotification('Erreur : ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'danger');
    });
});

document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../../views/users/CRUD/add_category.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const categoryModalEl = document.getElementById('addCategoryModal');
            const modal = bootstrap.Modal.getInstance(categoryModalEl);
            modal.hide();
            showNotification(data.message, 'success');
            const newCategory = data.category;
            const newRow = `
                <tr>
                    <td>${newCategory.id}</td>
                    <td>${escapeHtml(newCategory.Name)}</td>
                    <td>
                        <span class="badge bg-info">0 produits</span>
                    </td>
                    <td>
                        <img src="../../${escapeHtml(newCategory.image)}" alt="${escapeHtml(newCategory.Name)}" style="height:50px; width:50px; object-fit:cover; border-radius:5px;">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning editCategory" 
                                data-id="${newCategory.id}"
                                data-name="${escapeHtml(newCategory.Name)}"
                                data-bs-toggle="modal" 
                                data-bs-target="#editCategoryModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <button class="btn btn-sm btn-danger deleteCategory" 
                                data-id="${newCategory.id}"
                                data-name="${escapeHtml(newCategory.Name)}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#categoriesTable').prepend(newRow);

            this.reset();
        } else {
            showNotification('Erreur : ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur de communication est survenue', 'danger');
    });
});

$(document).ready(function() {

    $(document).on('click', '.editCategory', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const imageSrc = $(this).closest('tr').find('img').attr('src');

        $('#editCategoryId').val(id);
        $('#editCategoryName').val(name);
        $('#currentCategoryImage').html(`<img src="${imageSrc}" alt="Image actuelle" style="max-width: 100px; height: auto;">`);
        
        $('#editCategoryId').val(id);
        $('#editCategoryName').val(name);
    });

    $(document).on('submit', '#editCategoryForm', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('CRUD/edit_category.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#editCategoryModal').modal('hide');
                showNotification('Catégorie modifiée avec succès !', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Erreur : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error("Erreur fetch:", error);
            showNotification("Erreur de communication avec le serveur.", "danger");
        });
    });

    $(document).on('submit', '#addCategoryForm', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        
        fetch('../../views/users/CRUD/add_category.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#addCategoryModal').modal('hide');
                showNotification('Catégorie ajoutée avec succès !', 'success');
                
                const newCategory = data.category;
                const newRow = `
                    <tr>
                        <td>${newCategory.id}</td>
                        <td>${escapeHtml(newCategory.Name)}</td>
                        <td>
                            <span class="badge bg-info">0 produits</span>
                        </td>
                        <td>
                            <img src="../../${escapeHtml(newCategory.image)}" alt="${escapeHtml(newCategory.Name)}" style="height:50px; width:50px; object-fit:cover; border-radius:5px;">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning editCategory" 
                                    data-id="${newCategory.id}"
                                    data-name="${escapeHtml(newCategory.Name)}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCategoryModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <button class="btn btn-sm btn-danger deleteCategory" 
                                    data-id="${newCategory.id}"
                                    data-name="${escapeHtml(newCategory.Name)}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#categoriesTable').prepend(newRow);
                form.reset();
            } else {
                showNotification('Erreur : ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error("Erreur fetch:", error);
            showNotification("Erreur de communication avec le serveur.", "danger");
        });
    });
});

$(document).ready(function() {

    $(document).on('click', '.view-message', function() {
        const button = $(this);
        const messageId = button.data('id');
        const wasNew = button.hasClass('fw-bold') || button.closest('tr').hasClass('fw-bold');
        const row = button.closest('tr');

        const formData = new FormData();
        formData.append('id', messageId);

        fetch('CRUD/update_message_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const msg = data.data;
                $('#messageFromName').text(msg.first_name + ' ' + msg.last_name);
                $('#messageFromEmail').text(msg.email);
                $('#messageDate').text(new Date(msg.created_at).toLocaleString('fr-FR'));
                $('#messageSubject').text(msg.subject);
                $('#messageBody').text(msg.message);
                if (wasNew) {

                    row.removeClass('table-light fw-bold');
                    row.find('.badge.bg-primary').removeClass('bg-primary').addClass('bg-secondary').text('Lu');
                    $(`.dropdown-item.view-message[data-id="${messageId}"]`).removeClass('fw-bold');

                    const badge = $('#message-count-badge');
                    let currentCount = parseInt(badge.text());
                    if (currentCount > 1) {
                        badge.text(currentCount - 1);
                    } else {

                        badge.remove();
                    }
                }
            } else {
                showNotification('Erreur: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error("Erreur fetch:", error);
            showNotification("Erreur de communication avec le serveur.", "danger");
        });
    });

    $(document).on('click', '.delete-message', function() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
            return;
        }

        const messageId = $(this).data('id');
        const row = $(this).closest('tr');
        const formData = new FormData();
        formData.append('id', messageId);

        fetch('CRUD/delete_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.fadeOut(400, () => row.remove());
                showNotification('Message supprimé.', 'success');
            } else {
                showNotification('Erreur: ' + data.message, 'danger');
            }
        });
    });
});

document.querySelectorAll('.deleteCategory').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${name}" ?`)) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', window.csrfToken);
            
            fetch('CRUD/delete_category.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                    alert('Catégorie supprimée avec succès !');
                } else {
                    alert('Erreur : ' + data.message);
                }
            });
        }
    });
});

document.getElementById('categorySearch').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#categoriesTable tr');
    
    rows.forEach(row => {
        const categoryName = row.querySelector('td:nth-child(2)');
        if (categoryName) {
            const text = categoryName.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        }
    });
});
function loadCreations() {
    displayCreations(creations);
}

function displayCreations(creationsToShow) {
    let html = '';
    
    creationsToShow.forEach(creation => {
        html += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <img src="../../${creation.image || ''}" alt="${creation.title}" class="product-image">
                    <div class="product-info">
                        <h5 class="product-title">${creation.title}</h5>
                        <p class="text-muted small">${creation.description}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editCreation(${creation.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCreation(${creation.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#creationsGrid').html(html);
}

function viewOrder(orderId) {
    window.location.href = '../products/order_details.php?id=' + orderId;
}

$('#orderSearch, #orderStatusFilter, #orderDateFilter').on('change keyup', function() {
    filterOrders();
});

function filterOrders() {
    const searchTerm = $('#orderSearch').val().toLowerCase();
    const statusFilter = $('#orderStatusFilter').val();
    const dateFilter = $('#orderDateFilter').val();
    
    $('#ordersTable tr').each(function() {
        const $row = $(this);
        const text = $row.text().toLowerCase();
        const status = $row.find('.badge').text().trim().toLowerCase();
        const date = $row.find('td:eq(5)').text();
        
        const matchesSearch = text.includes(searchTerm);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        const matchesDate = !dateFilter || date.includes(dateFilter);
        
        $row.toggle(matchesSearch && matchesStatus && matchesDate);
    });
}
function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

$(document).ready(function() {
    initializeApp();
    loadSampleData();
    bindEvents(); 
    loadDashboard();
});

document.addEventListener('click', function(e) {

    if (e.target.matches('.approve-review, .approve-review *')) {
        const button = e.target.closest('.approve-review');
        const reviewId = button.dataset.id;
        
        if (confirm('Voulez-vous vraiment approuver cet avis ?')) {
            manageReview(reviewId, 'approve', button);
        }
    }


    if (e.target.matches('.delete-review, .delete-review *')) {
        const button = e.target.closest('.delete-review');
        const reviewId = button.dataset.id;

        if (confirm('Voulez-vous vraiment supprimer cet avis ? Cette action est irréversible.')) {
            manageReview(reviewId, 'delete', button);
        }
    }
});

function manageReview(reviewId, action, button) {
    const formData = new FormData();
    formData.append('review_id', reviewId);
    formData.append('action', action);
    formData.append('csrf_token', window.csrfToken);

    fetch('CRUD/manage_review.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            const row = document.getElementById('review-row-' + reviewId);
            if (action === 'approve') {

                const statusCell = row.querySelector('.review-status');
                statusCell.innerHTML = '<span class="badge bg-success">Approuvé</span>';
                button.remove();
            } else if (action === 'delete') {

                row.remove();
            }
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur de communication est survenue.');
    });
}
