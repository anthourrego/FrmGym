<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - KORPUS Training Club</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS with Bootstrap 5 integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.6.0/css/searchBuilder.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css">
    <!-- Admin Dashboard Styles -->
    <link rel="stylesheet" href="<?= base_url('css/admin-dashboard.css') ?>">
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner-border spinner-border-lg text-primary" role="status"></div>
            <div class="mt-3">
                <h5>Cargando datos...</h5>
                <p class="text-muted">Por favor espera un momento</p>
            </div>
        </div>
    </div>

    <!-- Header con clases Bootstrap estándar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="<?= base_url('assets/isotipo.webp') ?>" alt="KORPUS" height="28" class="me-2">
            </a>
            <span class="navbar-text me-auto">
                KORPUS Training Club - Gestión de Registros
            </span>
            <div class="d-flex">
                <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="stats-number" id="totalRegistros">0</div>
                            <div>Total de Registros</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="stats-number" id="registrosHoy">0</div>
                            <div>Registros Hoy</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="stats-number" id="consentimientos">0</div>
                            <div>Con Consentimiento</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="stats-number" id="promedioEdad">0</div>
                            <div>Edad Promedio</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Registros -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="mb-0">
                            <i class="fas fa-table me-2 text-primary"></i>
                            Registros de Usuarios
                        </h4>
                        <p class="text-muted mb-0">Gestión completa de todos los registros del gimnasio</p>
                    </div>
                    <div class="card-body">
                        <table id="registrosTable" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Edad</th>
                                    <th>Email</th>
                                    <th>WhatsApp</th>
                                    <th>Objetivo</th>
                                    <th>Horario</th>
                                    <th>Fuente</th>
                                    <th>Consentimiento</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargan dinámicamente vía AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        Detalles del Registro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este registro?</p>
                    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Extensions -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.6.0/js/dataTables.searchBuilder.min.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.6.0/js/searchBuilder.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
    <!-- JSZip for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- PDFMake for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    <!-- Custom DataTables JavaScript -->
    <script src="<?= base_url('js/DataTables.js') ?>"></script>
    
    <script>
        let dataTable;
        let deleteRecordId = null;

        $(document).ready(function() {
            initDataTable();
            loadStats();
            
            // Auto-refresh cada 5 minutos
            setInterval(function() {
                refreshData();
            }, 300000);
        });

        function initDataTable() {
            showLoading();
            
            dataTable = $('#registrosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= base_url('admin/datatables') ?>',
                    type: 'POST',
                    error: function(xhr, error, thrown) {
                        hideLoading();
                        console.error('Error loading data:', error);
                        showNotification('Error al cargar los datos', 'error');
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
                        action: function (e, dt, node, config) {
                            exportData();
                        }
                    },
                    {
                        text: '<i class="fas fa-sync-alt me-2"></i>Actualizar',
                        className: 'btn btn-info btn-sm',
                        action: function (e, dt, node, config) {
                            refreshData();
                        }
                    },
                    'excel', 
                    'pdf', 
                    'print', 
                    'colvis'
                ],
                drawCallback: function() {
                    hideLoading();
                    // Aplicar tooltips de Bootstrap
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
                initComplete: function() {
                    hideLoading();
                    loadStats();
                }
            });
        }

        function viewDetails(id) {
            showLoading();
            
            fetch(`<?= base_url('admin/view') ?>/${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    
                    if (data.success) {
                        showDetailModal(data.data);
                    } else {
                        showNotification('Error al cargar los detalles', 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showNotification('Error de conexión', 'error');
                });
        }

        function showDetailModal(record) {
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

        function deleteRecord(id) {
            deleteRecordId = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteRecordId) {
                showLoading();
                
                fetch(`<?= base_url('admin/delete') ?>/${deleteRecordId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    
                    if (data.success) {
                        showNotification(data.message, 'success');
                        dataTable.ajax.reload();
                        loadStats();
                    } else {
                        showNotification(data.message, 'error');
                    }
                    
                    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                    deleteRecordId = null;
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showNotification('Error al eliminar el registro', 'error');
                });
            }
        });

        function loadStats() {
            // Las estadísticas se pueden cargar desde el DataTable o una API separada
            // Por simplicidad, las calculamos desde el DataTable cuando se carga
            setTimeout(function() {
                if (dataTable) {
                    const info = dataTable.page.info();
                    document.getElementById('totalRegistros').textContent = info.recordsTotal;
                }
            }, 1000);
        }

        function refreshData() {
            if (dataTable) {
                dataTable.ajax.reload();
                loadStats();
                showNotification('Datos actualizados', 'success');
            }
        }

        function exportData() {
            showLoading();
            window.location.href = '<?= base_url('admin/export') ?>';
            setTimeout(hideLoading, 2000);
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showNotification(message, type = 'info') {
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
    </script>
</body>
</html>