$(document).ready(function() {
    /* Cargue tabla de Materias Primas */

    tblRawMaterials = $('#tblRawMaterials').dataTable({
        pageLength: 50,
        ajax: {
            url: '../../api/planMaterials',
            dataSrc: '',
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
        },
        columns: [{
                title: 'No.',
                data: null,
                className: 'uniqueClassName',
                render: function(data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                title: 'Referencia',
                data: 'reference',
                className: 'classCenter',
            },
            {
                title: 'Materia Prima',
                data: 'material',
                className: 'classCenter',
            },
            {
                title: 'Categoria',
                data: null,
                className: 'classCenter',
                render: function(data) {
                    if (data.category == 1) category = 'Insumos';
                    if (data.category == 2) category = 'Materiales';
                    if (data.category == 0) category = '';

                    return `<p>${category}</p>`;
                },
            },
            {
                title: 'Unidad',
                data: 'unit',
                className: 'classCenter',
            },
            /*   {
                title: 'Cantidad Inventario',
                data: 'quantity',
                className: 'classCenter',
                render: $.fn.dataTable.render.number('.', ',', 2, ''),
              }, */
            {
                title: 'Acciones',
                data: 'id_material',
                className: 'uniqueClassName',
                render: function(data) {
                    return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
                },
            },
        ],
    });
});