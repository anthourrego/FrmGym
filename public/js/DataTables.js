let busquedaModal = false;
var language = {
	lengthMenu: "Mostrar _MENU_ registros por página.",
	zeroRecords: "No se ha encontrado ningún registro.",
	emptyTable: "Ningún dato disponible en esta tabla",
	info: "Mostrando _START_ a _END_ de _TOTAL_ entradas.",
	infoEmpty: "Registros no disponibles.",
	search: "",
	searchPlaceholder: "Buscar",
	loadingRecords: "Cargando...",
	processing: "Procesando...",
	paginate: {
		first: "«",
		last: "»",
		next: "›",
		previous: "‹"
	},
	buttons: {
		pageLength: {
			_: 'Mostrar %d registros'
			, '-1': "Mostrar todo"
		},
		colvis: 'Visibilidad Columnas', 
		copy: 'Copiar', 
		csv: 'CVS', 
		excel: 'Excel', 
		pdf: 'PDF', 
		print: 'Imprimir'
	},
	infoFiltered: "(_MAX_ Registros filtrados en total)"
};

var domBftrip = "<'row no-gutters pt-1 px-1'<'col-sm-12 col-md-8 mb-2 mb-md-0'B><'col-sm-12 col-md-4 d-flex justify-content-end'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>";
var domBftri50 = "<'row no-gutters pt-1 px-1'<'col-12 col-xl-8 mb-2 mb-md-0'B><'col-12 col-xl-4 d-flex justify-content-end'f>><'row'<'col-12'tr>><'row'<'col-12'i>>";
var domBftri = "<'row no-gutters pt-1 px-1'<'col-sm-12 col-md-8 mb-2 mb-md-0'B><'col-sm-12 col-md-4 d-flex justify-content-end'f>><'row'<'col-sm-12'tr>><'row'<'col-12 d-flex justify-content-end'i>>";
var domlftrip = "<'row no-gutters pt-1 px-1'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>";
var domftrip = "<'row no-gutters pt-1 px-1'<'col-sm-12 d-flex justify-content-end'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>";
var domftr = "<'row no-gutters pt-1 px-1'<'col-12'f>><'row'<'col-md-12't>><'row'<'col-md-6'><'col-md-6'>>r";
var domlftri = "<'row no-gutters pt-1 px-1'<'col-sm-12 d-flex justify-content-end'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5 d-flex justify-content-end'i>>";
var domSearch = "<'row no-gutters pt-3 px-1'<'col-12'f>><'row'<'col-sm-12'tr>><'row'<'col-12'i>>";
var domSearch1 = "<'row no-gutters pt-0 px-1'<'col-12'f>><'row'<'col-sm-12'tr>><'row'<'col-12'i>>";
var domBSearch = "<'row no-gutters pt-0 px-1'<'col-sm-12 col-md-8 mb-2 mb-md-0'B><'col-sm-12 col-md-4'f>><'row'<'col-sm-12'tr>><'row'<'col-12'i>>";

$.extend(true, $.fn.dataTable.defaults, {
	processing: true,
	serverSide: true,
	language,
	pageLength: 25,
	deferRender: true,
	dom: domBftrip,
	search: {
		return: true
	},
	lengthMenu: [
		[10, 25, 50, -1],
		['10', '25', '50', 'Todos']
	],
	initComplete: function (settings, json) {
		let table = "#" + settings.sTableId;
		$(`${table}_wrapper [data-toggle="tooltip"]`).tooltip();
		
		if (busquedaModal) {
			setTimeout(() => {
				$(`${table}_filter input`).trigger("focus");
			}, 500);
		} else {
			$(`${table}_filter input`).trigger("focus");
		}
	},
	buttons: [
		{
			extend: 'collection',
			text:'<i class="fa-solid fa-download"></i>',
			className: 'btn-primary',
			autoClose: true,
			buttons: [
				{
					extend: 'copyHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
				{
					extend: 'excelHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
				{
					extend: 'csvHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
				{
					extend: 'pdfHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
			],
			attr: { title: "Exportar", "data-toggle":"tooltip" }
		},
		{ 
			extend: 'print', 
			className: 'printButton btn-info', 
			text: '<i class="fa-solid fa-print"></i>', 
			attr: { 
				title: "Imprimir", "data-toggle":"tooltip" 
			},
			exportOptions: {
				columns: ':visible:not(.noExport)'
			},
		},
		{ extend: 'pageLength' },
	],
});

/**
 * Genera un array de configuraciones de botones de DataTable basado en las opciones proporcionadas y botones adicionales.
 *
 * @param {Array<string>} options - Un array de tipos de botones a incluir. Valores válidos son 'collection', 'print' y 'pageLength'.
 * @param {Array<Object>} [btnAdicional=[]] - Un array de configuraciones de botones adicionales a incluir.
 * @returns {Array<Object>} Un array de objetos de configuración de botones para DataTables.
 *
 * @example
 * const options = ['collection', 'print'];
 * const additionalButtons = [{ extend: 'colvis', text: 'Visibilidad de Columnas' }];
 * const buttons = buttonsDT(options, additionalButtons);
 * // buttons contendrá las configuraciones para 'collection', 'print' y el botón adicional 'colvis'.
 */
function buttonsDT(options, btnAdicional = []) {
	let btns = [];
	let botones = {
		collection:{
			extend: 'collection',
			text:'<i class="fa-solid fa-download"></i>',
			className: 'btn-primary',
			autoClose: true,
			buttons: [
				{
					extend: 'copyHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
				{
					extend: 'excelHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
				{
					extend: 'csvHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
				{
					extend: 'pdfHtml5',
					exportOptions: {
							columns: ':visible:not(.noExport)'
					},
				},
			],
			attr: { title: "Exportar", "data-toggle":"tooltip" }
		},
		print: { 
			extend: 'print', 
			className: 'printButton btn-info', 
			text: '<i class="fa-solid fa-print"></i>', 
			attr: { 
				title: "Imprimir", "data-toggle":"tooltip" 
			},
			exportOptions: {
				columns: ':visible:not(.noExport)'
			},
		},
		pageLength: { extend: "pageLength" }
	};

	options.forEach((it) => {
		btns.push(botones[it]);
	});

	btnAdicional.forEach((btnAdd) => {
		btns.push(btnAdd);
	});

	return btns;
}

var oldExportAction = function (self, e, dt, button, config) {
	if (button[0].className.indexOf('buttons-excel') >= 0) {
		if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
			$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
		}else{
			$.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
		}
	} else if (button[0].className.indexOf('buttons-print') >= 0) {
		$.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
	}
};

var newExportAction = function (e, dt, button, config) {
	var self = this;
	var oldStart = dt.settings()[0]._iDisplayStart;

	dt.one('preXhr', function (e, s, data) {
		// Just this once, load all data from the server...
		data.start = 0;
		//data.length = 2147483647;
		data.length = -1;

		dt.one('preDraw', function (e, settings) {
			// Call the original action function 
			oldExportAction(self, e, dt, button, config);

			dt.one('preXhr', function (e, s, data) {
				// DataTables thinks the first item displayed is index 0, but we're not drawing that.
				// Set the property to what it was before exporting.
				settings._iDisplayStart = oldStart;
				data.start = oldStart;
			});

			// Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
			setTimeout(dt.ajax.reload, 0);

			// Prevent rendering of the full data to the DOM
			return false;
		});
	});

	// Requery the server with the new one-time export settings
	dt.ajax.reload();
};