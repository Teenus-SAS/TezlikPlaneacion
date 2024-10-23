$(document).ready(function () {
  /* Cargue tabla Empresas licencia */

  tblCompaniesLic = $('#tblCompaniesLicense').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/licenses',
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
        title: 'NIT',
        data: 'nit',
      },
      {
        title: 'Empresa',
        data: 'company',
      },
      {
        title: 'Inicio Licencia',
        data: 'license_start',
      },
      {
        title: 'Final Licencia',
        data: 'license_end',
      },
      {
        title: 'Días de Licencia',
        data: 'license_days',
      },
      {
        title: 'Cant. Usuarios',
        data: 'quantity_user',
      },
      {
        title: 'Estado',
        data: 'license_status',
        render: function (data) {
          if (data == 1) {
            return 'Activo';
          } else {
            return 'Inactivo';
          }
        },
      },
      {
        title: 'Tipo plan',
        data: 'plan',
        render: function (data) {
          if (data == 0) {
            return '';
          } else if (data == 1) {
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
        title: "Accesos",
        data: null,
        //width: "200px",
        render: function (data, type, row) {
          const permissions = []; 
          
          permissions.push({
            name: "Medidas Producto",
            icon: data.flag_products_measure == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          }); 

          permissions.push({
            name: "Tipo Programacion",
            icon: data.flag_type_program == 1
              ? "bi bi-check-circle-fill text-success"
              : "bi bi-x-circle-fill text-danger",
            color: { text: "black" },
          }); 

          let output =
            '<div class="stacked-column text-left" style="width:190px">';
          for (const permission of permissions) {
            output += `<span class="text-${permission.color} mx-1" style="display: flex; justify-content: flex-start;">
            <i class="${permission.icon}"></i> ${permission.name}
          </span>`;
          }
          output += "</div>";

          return output;
        },
      },
      {
        title: 'Acciones',
        data: 'id_company',
        className: 'uniqueClassName dt-head-center',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateLicenses" data-toggle='tooltip' title='Actualizar Licencia' style="font-size: 30px;"></i></a>                              
          <a href="javascript:;" <i id="ls-${data}" class="bx bx-check-circle licenseStatus" data-toggle='tooltip' title='Estado Licencia' style="font-size: 30px;"></i></a>
          `;
        },
      },
    ],
  });
});
