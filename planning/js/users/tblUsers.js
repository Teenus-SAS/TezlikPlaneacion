// $(document).ready(function () {
//   // Cargue tabla de Proyectos
//   tblUsers = $("#tblUsers").dataTable({
//     fixedHeader: true,
//     scrollY: "400px",
//     scrollCollapse: true,
//     pageLength: 50,
//     ajax: {
//       url: "/api/planningUsersAccess",
//       dataSrc: "",
//     },
//     language: {
//       url: "/assets/plugins/i18n/Spanish.json",
//     },
//     columns: [
//       {
//         title: "No.",
//         data: null,
//         className: "uniqueClassName dt-head-center",
//         render: function (data, type, full, meta) {
//           return meta.row + 1;
//         },
//       },
//       {
//         title: "Nombres",
//         data: "firstname",
//         className: "uniqueClassName dt-head-center",
//       },
//       {
//         title: "Email",
//         data: "email",
//         className: "uniqueClassName dt-head-center",
//       },
//       {
//         title: "Basicos",
//         data: null,
//         width: "300px",
//         render: function (data, type, row) {
//           const permissions = [];

//           permissions.push({
//             name: "Productos y Materiales",
//             icon: data.create_product
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Procesos y Maquinas",
//             icon: data.create_machine
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });

//           permissions.push({
//             name: "Nomina",
//             icon: data.payroll
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });

//           let output = '<div class="stacked-column">';
//           for (const permission of permissions) {
//             output += `<span class="text-${permission.color} mx-1">
//             <i class="${permission.icon}"></i> ${permission.name}
//           </span>`;
//           }
//           output += "</div>";

//           return output;
//         },
//       },
//       {
//         title: "Configuracion",
//         data: null,
//         width: "200px",
//         render: function (data, type, row) {
//           const permissions = [];

//           permissions.push({
//             name: "Ficha Técnica Materiales",
//             icon: data.products_material
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Programación Maquinas",
//             icon: data.programs_machine
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Stock",
//             icon: data.stock
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Calendario",
//             icon: data.calendar
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });

//           let output = '<div class="stacked-column">';
//           for (const permission of permissions) {
//             output += `<span class="text-${permission.color} mx-1">
//             <i class="${permission.icon}"></i> ${permission.name}
//           </span>`;
//           }
//           output += "</div>";

//           return output;
//         },
//       },
//       {
//         title: "General",
//         data: null,
//         width: "100px",
//         render: function (data, type, row) {
//           const permissions = [];

//           permissions.push({
//             name: "Clientes y Proveedores",
//             icon: data.client
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//           });
//           permissions.push({
//             name: "Vendedores",
//             icon: data.seller
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//           });
//           permissions.push({
//             name: "Ventas",
//             icon: data.sale
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//           });

//           let output = '<div class="stacked-column">';
//           for (const permission of permissions) {
//             output += `<span class="text-${permission.color} mx-1">
//             <i class="${permission.icon}"></i> ${permission.name}
//           </span>`;
//           }
//           output += "</div>";

//           return output;
//         },
//       },
//       {
//         title: "Administrador",
//         data: null,
//         width: "100px",
//         render: function (data, type, row) {
//           const permissions = [];

//           permissions.push({
//             name: "Usuarios",
//             icon: data.user
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });

//           let output = '<div class="stacked-column">';
//           for (const permission of permissions) {
//             output += `<span class="text-${permission.color} mx-1">
//             <i class="${permission.icon}"></i> ${permission.name}
//           </span>`;
//           }
//           output += "</div>";

//           return output;
//         },
//       },
//       {
//         title: "Navegación",
//         data: null,
//         width: "200px",
//         render: function (data, type, row) {
//           const permissions = [];

//           permissions.push({
//             name: "Inventarios",
//             icon: data.inventory
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill",
//             color: {
//               text: data.inventory ? "green" : "red",
//             },
//           });

//           permissions.push({
//             name: "Compras",
//             icon: data.requisition
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });

//           permissions.push({
//             name: "Pedidos",
//             icon: data.plan_order
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Programar Producción",
//             icon: data.program
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Explosión de MP",
//             icon: data.explosion_of_material
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Orden de Producción",
//             icon: data.production_order
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Entregas OP a Almacen",
//             icon: data.op_to_store
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Almacen",
//             icon: data.store
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });
//           permissions.push({
//             name: "Despachos",
//             icon: data.office
//               ? "bi bi-check-circle-fill text-success"
//               : "bi bi-x-circle-fill text-danger",
//             color: { text: "black" },
//           });

//           let output = '<div class="stacked-column">';
//           for (const permission of permissions) {
//             output += `<span class="text-${permission.color} mx-1">
//             <i class="${permission.icon}"></i> ${permission.name}
//           </span>`;
//           }
//           output += "</div>";

//           return output;
//         },
//       },
//       {
//         title: "Acciones",
//         data: "id_user",
//         className: "uniqueClassName dt-head-center",
//         render: function (data) {
//           return `
//             <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
//             <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
//         },
//       },
//     ],
//     headerCallback: function (thead, data, start, end, display) {
//       $(thead).find("th").css({
//         "background-color": "#386297",
//         color: "white",
//         "text-align": "center",
//         "font-weight": "bold",
//         padding: "10px",
//         border: "1px solid #ddd",
//       });
//     },
//     columnDefs: [
//       {
//         targets: [1],
//         render: function (data, type, row) {
//           return data + "  " + row.lastname + " ";
//         },
//       },
//     ],
//   });
// });

$(document).ready(function () {
  /* Cargue tabla de Proyectos */
  tblUsers = $("#tblUsers").dataTable({
    fixedHeader: true,
    scrollY: "400px",
    scrollCollapse: true,
    pageLength: 50,
    ajax: {
      url: "/api/planningUsersAccess",
      dataSrc: "",
    },
    language: {
      url: "/assets/plugins/i18n/Spanish.json",
    },
    columns: [
      {
        title: "No.",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Nombres",
        data: "firstname",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Email",
        data: "email",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Basicos",
        data: null,
        width: "300px",
        render: function (data, type, row) {
          const permissions = [
            { name: "Productos y Materiales", icon: data.create_product },
            { name: "Procesos y Maquinas", icon: data.create_machine },
            { name: "Nomina", icon: data.payroll },
          ];

          let output = '<div class="stacked-column" style="font-size: small;">';
          permissions.forEach(permission => {
            output += `<span class="d-block mx-2">
              <i class="${permission.icon ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle-fill text-danger'}"></i> ${permission.name}
            </span>`;
          });
          output += "</div>";
          return output;
        },
      },
      {
        title: "Configuracion",
        data: null,
        width: "200px",
        render: function (data, type, row) {
          const permissions = [
            { name: "Ficha Técnica Materiales", icon: data.products_material },
            { name: "Programación Maquinas", icon: data.programs_machine },
            { name: "Stock", icon: data.stock },
            { name: "Calendario", icon: data.calendar },
          ];

          let output = '<div class="stacked-column" style="font-size: small;">';
          permissions.forEach(permission => {
            output += `<span class="d-block mx-2">
              <i class="${permission.icon ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle-fill text-danger'}"></i> ${permission.name}
            </span>`;
          });
          output += "</div>";
          return output;
        },
      },
      {
        title: "General",
        data: null,
        width: "100px",
        render: function (data, type, row) {
          const permissions = [
            { name: "Clientes y Proveedores", icon: data.client },
            { name: "Vendedores", icon: data.seller },
            { name: "Ventas", icon: data.sale },
          ];

          let output = '<div class="stacked-column" style="font-size: small;">';
          permissions.forEach(permission => {
            output += `<span class="d-block mx-2">
              <i class="${permission.icon ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle-fill text-danger'}"></i> ${permission.name}
            </span>`;
          });
          output += "</div>";
          return output;
        },
      },
      {
        title: "Administrador",
        data: null,
        width: "100px",
        render: function (data, type, row) {
          return `<span class="d-block mx-2" style="font-size: small;">
            <i class="${data.user ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle-fill text-danger'}"></i> Usuarios
          </span>`;
        },
      },
      {
        title: "Navegación",
        data: null,
        width: "200px",
        render: function (data, type, row) {
          const permissions = [
            { name: "Inventarios", icon: data.inventory },
            { name: "Compras", icon: data.requisition },
            { name: "Pedidos", icon: data.plan_order },
            { name: "Programar Producción", icon: data.program },
            { name: "Explosión de MP", icon: data.explosion_of_material },
            { name: "Orden de Producción", icon: data.production_order },
            { name: "Entregas OP a Almacen", icon: data.op_to_store },
            { name: "Almacen", icon: data.store },
            { name: "Despachos", icon: data.office },
          ];

          let output = '<div class="stacked-column" style="font-size: small;">';
          permissions.forEach(permission => {
            output += `<span class="d-block mx-2">
              <i class="${permission.icon ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle-fill text-danger'}"></i> ${permission.name}
            </span>`;
          });
          output += "</div>";
          return output;
        },
      },
      {
        title: "Acciones",
        data: "id_user",
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `
            <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
            <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
    headerCallback: function (thead, data, start, end, display) {
      $(thead).find("th").css({
        "background-color": "#386297",
        color: "white",
        "text-align": "center",
        "font-weight": "bold",
        padding: "10px",
        border: "1px solid #ddd",
      });
    },
    columnDefs: [
      {
        targets: [1],
        render: function (data, type, row) {
          return data + "  " + row.lastname + " ";
        },
      },
    ],
  });
});
