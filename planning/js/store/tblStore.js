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
                title: 'Referencia',
                data: 'reference',
                className: 'uniqueClassName',
            },
            {
                title: 'Materia Prima',
                data: 'material',
                className: 'uniqueClassName',
            },
            {
                title: 'Unidad',
                data: 'abbreviation',
                className: 'uniqueClassName',
            },
            {
                title: 'Existencias',
                data: 'quantity',
                className: 'uniqueClassName',
                render: $.fn.dataTable.render.number('.', ',', 0),
            },
            {
                title: 'Reservado',
                data: 'reserved',
                className: 'uniqueClassName',
                render: $.fn.dataTable.render.number('.', ',', 2),
            },
            {
                title: 'Acción',
                data: 'id_material',
                className: 'uniqueClassName',
                render: function (data) {
                    return `<button class="btn btn-warning deliver " id="${data}" name="${data}">Entregar MP</button>`;
                },
            },
        ],
        rowGroup: {
            dataSrc: function (row) {
                return `<th class="text-center" colspan="7" style="font-weight: bold;"> No Pedido - ${row.num_order} </th>`;
            },
            startRender: function (rows, group) {
                return $('<tr/>').append(group);
            },
            className: 'odd',
        },
    });
});
