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
                visible: false,
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
            {
                title: 'Acciones',
                data: 'id_material',
                className: 'uniqueClassName',
                render: function (data) {
                    return `<a href="javascript:;" <i id="${data}" class="bi bi-box-seam-fill deliver" data-toggle='tooltip' title='Entregar' style="font-size: 30px;"></i></a>`;
                },
            },
        ],
        rowGroup: {
            dataSrc: function (row) {
                return `<th class="text-center" colspan="5" style="font-weight: bold;"> No Pedido - ${row.num_order} </th>`;
            },
            startRender: function (rows, group) {
                return $('<tr/>').append(group);
            },
            className: 'odd',
        },
    });
});