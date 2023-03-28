$(document).ready(function () {  
    /* Cargue tabla Usuarios */
  
    // const loadtableCompanies = (stat) => {
    tblCompanies = $("#tblUsersLog").DataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/lastLoginUsers`,
        dataSrc: "",
      },
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Empresa",
          data: "company",
        },
        {
          title: "Nombre",
          data: "firstname",
        },
        {
          title: "Apellido",
          data: "lastname",
        },
        {
          title: "Última Sesión",
          data: "last_login",
        },
        {
          title: "Acciones",
          data: "id_user",
          className: "uniqueClassName",
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-user-x text-danger closeSession" data-toggle='tooltip' title='Cerrar Sesión' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
    // }
  });
  