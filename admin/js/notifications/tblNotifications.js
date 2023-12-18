$(document).ready(function () {
  tblNotifications = $('#tblNotifications').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/notifications`,
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
        title: 'Descripción',
        data: 'description',
      },
      {
        title: 'Empresa',
        data: 'company',
      },
      {
        title: 'Fecha Creación',
        data: 'date_notification',
        render: function (data) {
          date = new Date(data);
          year = date.getFullYear();

          month = `${date.getMonth() + 1}`.padStart(2, 0);

          day = `${date.getDate()}`.padStart(2, 0);

          hour = date.toLocaleTimeString(undefined, {
            hour: '2-digit',
            minute: '2-digit',
          });

          stringDate = `${[year, month, day].join('-')} ${hour}`;

          return stringDate;
        },
      },
      {
        title: 'Acciones',
        data: 'id_notification',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateNotification" data-toggle='tooltip' title='Actualizar notificación' style="font-size: 30px;"></i></a>
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Notificación' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>
          
          `;
        },
      },
    ],
  });
});
