$(document).ready(function () {
  tblPlans = $('#tblPlans').dataTable({
    pageLength: 50,
    ajax: {
      url: `/api/plansAccess`,
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
        title: 'Tipo plan',
        data: 'id_plan',
        render: function (data) {
          if (data == 0) {
            return '';
          } else if (data === 1) {
            return 'Premium';
          } else if (data == 2) {
            return 'Pro';
          } else if (data == 3) {
            return 'Pyme';
          } else if (data == 4) {
            return 'Emprendedor';
          }
        },
      },
      {
        title: 'Inventarios',
        data: 'plan_inventory',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Pedidos',
        data: 'plan_order',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Programación',
        data: 'plan_program',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Cargues',
        data: 'plan_load',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Explosión de Materiales',
        data: 'plan_explosion_of_material',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'O. Produccion',
        data: 'plan_production_order',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Despachos',
        data: 'plan_office',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Almacen',
        data: 'plan_store',
        className: 'uniqueClassName dt-head-center',
        render: function (data, type, row) {
          return data == 1
            ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
            : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
        },
      },
      {
        title: 'Acciones',
        data: 'id_plan',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePlanAccess" data-toggle='tooltip' title='Actualizar Plan' style="font-size: 30px;"></i></a>`;
        },
      },
    ],
  });
});
