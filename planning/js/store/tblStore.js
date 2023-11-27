$(document).ready(function () {
    tblStore = $('#tblStore').dataTable({
        destroy: true,
        pageLength: 50,
        ajax: {
            url: '/api/store',
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
                title: 'No Pedido',
                data: 'num_order',
                className: 'uniqueClassName',
            },
            {
                title: 'Referencia Material',
                data: 'reference',
                className: 'uniqueClassName',
            },
            {
                title: 'Materia Prima',
                data: 'material',
                className: 'uniqueClassName',
            },
            {
                title: 'Cantidad',
                data: 'quantity',
                className: 'uniqueClassName',
            },
            // {
            //     title: 'Inventario',
            //     data: 'quantity_material',
            //     className: 'uniqueClassName',
            //     render: $.fn.dataTable.render.number('.', ',', 2, ''),
            // },
            // {
            //     title: 'En Transito',
            //     data: 'transit',
            //     className: 'uniqueClassName',
            //     render: $.fn.dataTable.render.number('.', ',', 2, ''),
            // },
            // {
            //     title: 'Necesidad',
            //     data: 'need',
            //     className: 'uniqueClassName',
            //     render: $.fn.dataTable.render.number('.', ',', 2, ''),
            // },
            // {
            //     title: 'Disponible',
            //     data: 'available',
            //     className: 'uniqueClassName',
            //     render: $.fn.dataTable.render.number('.', ',', 2, ''),
            // }, 
        ],
    }); 
});
