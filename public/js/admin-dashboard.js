/**
 * KORPUS Training Club - Admin Dashboard JavaScript
 * Funcionalidades del panel de administración
 */

class AdminDashboard {
    constructor() {
        this.dataTable = null;
        this.deleteRecordId = null;
        
        // Obtener base_url desde el DOM o usar window.location.origin como fallback
        const baseUrlMeta = document.querySelector('meta[name="base-url"]');
        this.BASE_URL = baseUrlMeta ? baseUrlMeta.getAttribute('content') : window.location.origin;
        
        this.init();
    }
    
    init() {
        $(document).ready(() => {
            this.initDataTable();
            this.loadStats();
            this.bindEvents();
            
            // Auto-refresh cada 5 minutos
            setInterval(() => {
                this.refreshData();
            }, 300000);
        });
    }
    
    bindEvents() {
        // Event listener para el botón de confirmar eliminación
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            this.confirmDelete();
        });
    }
    
    initDataTable() {
        this.showLoading();
        
        this.dataTable = $('#registrosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `${this.BASE_URL}/admin/datatables`,
                type: 'POST',
                error: (xhr, error, thrown) => {
                    this.hideLoading();
                    console.error('Error loading data:', error);
                    this.showNotification('Error al cargar los datos', 'error');
                }
            },
            columns: [
                { data: 'no', orderable: false, searchable: false, className: 'text-center' },
                { data: 'full_name' },
                { data: 'document_number', defaultContent: '-' },
                { data: 'age', className: 'text-center' },
                { data: 'email' },
                { data: 'whatsapp' },
                { data: 'main_goal' },
                { data: 'preferred_schedule' },
                { data: 'how_did_you_know' },
                { data: 'consent', className: 'text-center' },
                { data: 'created_at', className: 'text-center' },
                { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[10, 'desc']], // Ordenar por fecha (más recientes primero)
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            responsive: true,
            // Layout estándar de Bootstrap con botones
            buttons: [
                {
                    text: '<i class="fas fa-download me-2"></i>Exportar CSV',
                    className: 'btn btn-success btn-sm',
                    action: (e, dt, node, config) => {
                        this.exportData();
                    }
                },
                {
                    text: '<i class="fas fa-sync-alt me-2"></i>Actualizar',
                    className: 'btn btn-info btn-sm',
                    action: (e, dt, node, config) => {
                        this.refreshData();
                    }
                },
                'excel', 
                'pdf', 
                'print', 
                'colvis'
            ],
            drawCallback: () => {
                this.hideLoading();
                // Aplicar tooltips de Bootstrap
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            initComplete: () => {
                this.hideLoading();
                this.loadStats();
            }
        });
    }
    
    viewDetails(id) {
        this.showLoading();
        
        fetch(`${this.BASE_URL}/admin/view/${id}`)
            .then(response => response.json())
            .then(data => {
                this.hideLoading();
                
                if (data.success) {
                    this.showDetailModal(data.data);
                } else {
                    this.showNotification('Error al cargar los detalles', 'error');
                }
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error:', error);
                this.showNotification('Error de conexión', 'error');
            });
    }
    
    showDetailModal(record) {
        const goals = {
            'lose_weight': 'Bajar de peso',
            'gain_muscle': 'Aumentar masa muscular',
            'improve_health': 'Mejorar mi salud',
            'tone_up': 'Tonificar',
            'other': record.other_goal || 'Otro'
        };
        
        const sources = {
            'social_media': 'Redes sociales',
            'friend_referral': 'Recomendación de amigo',
            'google_search': 'Búsqueda en Google',
            'advertisement': 'Publicidad',
            'other': record.other_source || 'Otro'
        };

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">ID:</div>
                        <div class="detail-value">${record.id}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Nombre Completo:</div>
                        <div class="detail-value">${record.full_name}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Documento:</div>
                        <div class="detail-value">${record.document_number || 'No proporcionado'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Edad:</div>
                        <div class="detail-value">${record.age} años</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email:</div>
                        <div class="detail-value">
                            <a href="mailto:${record.email}" class="text-primary">${record.email}</a>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">WhatsApp:</div>
                        <div class="detail-value">
                            <a href="https://wa.me/57${record.whatsapp.replace(/\D/g, '')}" target="_blank" class="text-success">
                                <i class="fab fa-whatsapp me-1"></i>${record.whatsapp}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <div class="detail-label">Objetivo Principal:</div>
                        <div class="detail-value">${goals[record.main_goal] || record.main_goal}</div>
                    </div>
                    ${record.other_goal ? `
                    <div class="detail-row">
                        <div class="detail-label">Objetivo Personalizado:</div>
                        <div class="detail-value">${record.other_goal}</div>
                    </div>
                    ` : ''}
                    <div class="detail-row">
                        <div class="detail-label">Horario Preferido:</div>
                        <div class="detail-value">${record.preferred_schedule}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Cómo nos conoció:</div>
                        <div class="detail-value">${sources[record.how_did_you_know] || record.how_did_you_know}</div>
                    </div>
                    ${record.other_source ? `
                    <div class="detail-row">
                        <div class="detail-label">Fuente Personalizada:</div>
                        <div class="detail-value">${record.other_source}</div>
                    </div>
                    ` : ''}
                    <div class="detail-row">
                        <div class="detail-label">Consentimiento:</div>
                        <div class="detail-value">
                            <span class="badge bg-${record.consent === 'si' ? 'success' : 'danger'}">
                                ${record.consent === 'si' ? 'Otorgado' : 'No otorgado'}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Fecha de Registro:</div>
                        <div class="detail-value">${new Date(record.created_at).toLocaleString('es-ES')}</div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('detailContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('detailModal')).show();
    }
    
    deleteRecord(id) {
        this.deleteRecordId = id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    confirmDelete() {
        if (this.deleteRecordId) {
            this.showLoading();
            
            fetch(`${this.BASE_URL}/admin/delete/${this.deleteRecordId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.hideLoading();
                
                if (data.success) {
                    this.showNotification(data.message, 'success');
                    this.dataTable.ajax.reload();
                    this.loadStats();
                } else {
                    this.showNotification(data.message, 'error');
                }
                
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                this.deleteRecordId = null;
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error:', error);
                this.showNotification('Error al eliminar el registro', 'error');
            });
        }
    }
    
    loadStats() {
        // Las estadísticas se pueden cargar desde el DataTable o una API separada
        // Por simplicidad, las calculamos desde el DataTable cuando se carga
        setTimeout(() => {
            if (this.dataTable) {
                const info = this.dataTable.page.info();
                document.getElementById('totalRegistros').textContent = info.recordsTotal;
            }
        }, 1000);
    }
    
    refreshData() {
        if (this.dataTable) {
            this.dataTable.ajax.reload();
            this.loadStats();
            this.showNotification('Datos actualizados', 'success');
        }
    }
    
    exportData() {
        this.showLoading();
        window.location.href = `${this.BASE_URL}/admin/export`;
        setTimeout(() => this.hideLoading(), 2000);
    }
    
    showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }
    
    hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }
    
    showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 320px;
            max-width: 500px;
            border-radius: 15px;
            border: none;
            backdrop-filter: blur(15px);
            box-shadow: 0 10px 30px rgba(18, 18, 18, 0.15);
            animation: slideInRight 0.4s ease-out;
        `;
        
        // Aplicar colores de KORPUS
        if (type === 'success') {
            notification.style.background = 'rgba(25, 135, 84, 0.1)';
            notification.style.color = '#0f5132';
            notification.style.border = '2px solid rgba(25, 135, 84, 0.2)';
        } else if (type === 'error') {
            notification.style.background = 'rgba(220, 53, 69, 0.1)';
            notification.style.color = '#842029';
            notification.style.border = '2px solid rgba(220, 53, 69, 0.2)';
        } else if (type === 'warning') {
            notification.style.background = 'rgba(255, 193, 7, 0.1)';
            notification.style.color = '#664d03';
            notification.style.border = '2px solid rgba(255, 193, 7, 0.2)';
        } else {
            notification.style.background = 'rgba(156, 175, 183, 0.1)';
            notification.style.color = '#121212';
            notification.style.border = '2px solid rgba(156, 175, 183, 0.2)';
        }
        
        notification.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    ${message}
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.4s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 400);
            }
        }, 5000);
    }
}

// Funciones globales para compatibilidad con el HTML existente
let adminDashboard;

function viewDetails(id) {
    adminDashboard.viewDetails(id);
}

function deleteRecord(id) {
    adminDashboard.deleteRecord(id);
}

// Inicializar el dashboard cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    adminDashboard = new AdminDashboard();
});