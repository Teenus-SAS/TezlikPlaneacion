$(document).ready(function () {
    /* Cargar pedidos */
    loadTblMaterials = async () => {
        let data = await searchData('/api/explosionMaterials');

        $('#inventory').val(data[0].quantity_product.toLocaleString('es-CO'));

        tblMaterials = $('#tblMaterials').dataTable({
            destroy: true,
            pageLength: 50,
            // ajax: {
            //     url: '/api/explosionMaterials',
            //     dataSrc: '',
            // },
            data: data,
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
                    className: 'uniqueClassName',
                },
                {
                    title: 'Materia Prima',
                    data: 'material',
                    className: 'uniqueClassName',
                },
                {
                    title: 'En transito',
                    data: 'quantity_material',
                    className: 'uniqueClassName',
                    render: $.fn.dataTable.render.number('.', ',', 2, ''),
                },
                {
                    title: 'Necesidad',
                    data: 'quantity',
                    className: 'uniqueClassName',
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
    }

    loadTblMaterials();
});
