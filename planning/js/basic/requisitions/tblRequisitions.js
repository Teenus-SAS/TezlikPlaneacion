$(document).ready(function () { 
    /* Cargue tabla de Productos Materiales */
    tblRequisitions = $('#tblRequisitions').dataTable({
        destroy: true,
        pageLength: 50,
        ajax: {
            url: '/api/requisitions',
            dataSrc: '',
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
        },
        columns: [
            {
                title: 'No.',
                data: null,
                className: 'uniqueClassName',
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                title: 'Referencia',
                data: 'reference',
                className: 'uniqueClassName',
            },
            {
                title: 'Material',
                data: 'material',
                className: 'classCenter',
            },
            {
                title: 'Fecha Solicitud',
                data: 'application_date',
                className: 'classCenter',
            },
            {
                title: 'Fecha Entrega',
                data: 'delivery_date',
                className: 'classCenter', 
            },
            {
                title: 'Cantidad',
                data: 'quantity',
                className: 'classCenter',
                render: $.fn.dataTable.render.number('.', ',', 4, ''),
            },
            {
                title: 'Orden de Compra',
                data: 'purchase_order',
                className: 'classCenter', 
            },
            {
                title: 'Recibir Material',
                data: null,
                className: 'classCenter',
                render: function (data, type, full, meta) {  
                    return `<a href="javascript:;" <i class="bi bi-calendar-plus-fill changeDate" id="${meta.row +1}" data-toggle='tooltip' title='Actualizar Fecha' style="font-size: 30px;"></i></a>`;
                }
            },
            {
                title: 'Acciones',
                data: 'id_requisition',
                className: 'uniqueClassName',
                render: function (data) {
                    return `
                    <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateRequisition" data-toggle='tooltip' title='Actualizar Requisicion' style="font-size: 30px;"></i></a>
                    <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Requisicion' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
                },
            },
        ],
    });
});
