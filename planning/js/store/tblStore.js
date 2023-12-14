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
                render: $.fn.dataTable.render.number('.', ',', 0),
            },
            {
                title: 'Cantidades',
                data: null,
                className: 'uniqueClassName',
                render: function (data, type, full, meta) {  
                    const store = full.delivery_store;
                    const pending = full.delivery_pending;

                    return `Entregado: ${store}<br>Pendiente: ${pending}`;
                },
            },
            {
                title: 'Acción',
                data: null,
                className: 'uniqueClassName',
                render: function (data) {
                    if (!data.delivery_date)
                        action = `<button class="btn btn-warning deliver" id="delivery">Entregar MP</button>`;
                    else 
                        action = `Entregado: ${data.delivery_date}`;

                    return action;
                },
            },
        ],
        rowGroup: {
            dataSrc: function (row) {
                return `<th class="text-center" colspan="8" style="font-weight: bold;"> No Pedido - ${row.num_order} </th>`;
            },
            startRender: function (rows, group) {
                return $('<tr/>').append(group);
            },
            className: 'odd',
        },
    });
});
