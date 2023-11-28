$(document).ready(function () {
    /* Cargue tabla de Productos Materiales */
    loadTblRequisitions = (min_date, max_date) => {
        if (min_date == null && max_date == null)
            url = '/api/requisitions';
        else
            url = `/api/requisitions/${min_date}/${max_date}`;

        tblRequisitions = $('#tblRequisitions').dataTable({
            destroy: true,
            pageLength: 50,
            ajax: {
                url: url,
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
                    title: 'Unidad',
                    data: 'unit',
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
                    render: $.fn.dataTable.render.number('.', ',', 2, ''),
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
                        if (data.application_date == '0000-00-00' && data.delivery_date == '0000-00-00' && data.purchase_order == '')
                            date = '';
                        else if (!data.admission_date)
                            date = `<a href="javascript:;" <i class="bi bi-calendar-plus-fill changeDate" id="${meta.row + 1}" data-toggle='tooltip' title='Actualizar Fecha' style="font-size: 30px;"></i></a>`;
                        else
                            date = `Recibido<br>${data.admission_date}`;
                        
                        return date;
                    }
                },
                {
                    title: 'Acciones',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) {
                        !data.admission_date ? action = `<a href="javascript:;" <i id="${data.id_requisition}" class="bx bx-edit-alt updateRequisition" data-toggle='tooltip' title='Actualizar Requisicion' style="font-size: 30px;"></i></a>
                                                     <a href="javascript:;" <i id="${data.id_requisition}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Requisicion' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`
                            : action = '';
                        return action;
                    },
                },
            ],
        });
    }

    loadTblRequisitions(null, null);
});
