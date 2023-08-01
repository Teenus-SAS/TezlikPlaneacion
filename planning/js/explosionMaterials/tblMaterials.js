$(document).ready(function () {
    /* Cargar pedidos */
    tblMaterials = $('#tblMaterials').dataTable({
        destroy: true,
        pageLength: 50,
        ajax: {
            url: '/api/explosionMaterials',
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
            // {
            //     title: 'Referencia',
            //     data: 'reference_product',
            //     className: 'uniqueClassName',
            //     visible: false,
            // },
            // {
            //     title: 'Producto',
            //     data: 'product',
            //     className: 'uniqueClassName',
            //     visible: false,
            // },
            {
                title: 'Referencia Material',
                data: 'reference_material',
                className: 'classCenter',
            },
            {
                title: 'Materia Prima',
                data: 'material',
                className: 'classCenter',
            },
            {
                title: 'Cantidad',
                data: 'quantity',
                className: 'classCenter',
                render: $.fn.dataTable.render.number('.', ',', 2, ''),
            },
            // {
            //   title: 'Fecha de Entrega',
            //   data: 'id_order',
            //   className: 'classCenter',
            //   render: function (data) { 
            //     return `<a href="javascript:;" <i class="bi bi-calendar-plus-fill changeDate" id="${data}" data-toggle='tooltip' title='Actualizar Fecha' style="font-size: 30px;"></i></a>`;
            //   }
            // },
        ], 
    });
});
