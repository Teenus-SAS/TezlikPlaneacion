$(document).ready(function () {
  /* Seleccion producto */

  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value; 
    $(".cardAddProcess").hide(800);
    loadtableProcess(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value; 
    $(".cardAddProcess").hide(800);
    loadtableProcess(id);
  });

  /* Cargue tabla de Proyectos */

  const loadtableProcess = (idProduct) => {
    tblConfigProcess = $('#tblConfigProcess').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsProcess/${idProduct}`,
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
          title: 'Proceso',
          data: 'process',
        },
        {
          title: 'Máquina',
          data: 'machine',
          render: function (data, type, row) {
            if (data === null) {
              return 'Proceso Manual';
            } else {
              return data;
            }
          },
        },
        {
          title: 'Tiempo Alistamiento (min)',
          data: 'enlistment_time',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 2, ''),
        },
        {
          title: 'Tiempo Operación  (min)',
          data: 'operation_time',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 2, ''),
        },
        {
          title: 'Acciones',
          data: 'id_product_process',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        enlistmentTime = this.api()
          .column(3)
          .data()
          .reduce(function (a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);

        $(this.api().column(3).footer()).html(
          new Intl.NumberFormat('de-DE').format(enlistmentTime)
        );
        operationTime = this.api()
          .column(4)
          .data()
          .reduce(function (a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);

        $(this.api().column(4).footer()).html(
          new Intl.NumberFormat('de-DE').format(operationTime)
        );
      },
    });
  };
});
