$(document).ready(function () {
    tblPartialsDelivery = $('#tblPartialsDelivery').dataTable({
        destroy: true,
        pageLength: 50,
        ajax: {
            url: `/api/productionOrderPartial`,
            dataSrc: '',
        },
        language: {
            url: '/assets/plugins/i18n/Spanish.json',
        },
        columns: [
            {
                title: 'No.',
                data: null,
                className: 'uniqueClassName dt-head-center',
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                title: "Fechas",
                data: null,
                className: "uniqueClassName dt-head-center",
                width: "200px",
                render: function (data, type, full, meta) {
                    const start_date = full.start_date;
                    const end_date = full.end_date;

                    return `Inicio: ${moment(start_date).format(
                        "DD/MM/YYYY HH:mm A"
                    )}<br>Fin: ${moment(end_date).format("DD/MM/YYYY HH:mm A")}`;
                },
            },
            {
                title: 'Operador',
                data: 'operator',
                className: 'uniqueClassName dt-head-center',
            },
            {
                title: 'Desperdicio',
                data: 'waste',
                className: 'uniqueClassName dt-head-center',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                title: 'Cantidad Entregada',
                data: 'partial_quantity',
                className: 'uniqueClassName dt-head-center',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
        ],
    });
});