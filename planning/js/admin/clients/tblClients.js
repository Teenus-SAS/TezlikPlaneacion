$(document).ready(function () {
  loadAllDataClients = async () => {
    let data = await searchData("/api/clients");

    sessionStorage.setItem("dataClients", JSON.stringify(data));

    loadTblClients(data);
  };

  const loadTblClients = (data) => {
    tblClients = $("#tblClients").dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
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
          title: "NIT",
          data: "nit",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Dirección",
          data: "address",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Telefono",
          data: "phone",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Ciudad",
          data: "city",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Img",
          data: "img",
          className: "uniqueClassName dt-head-center",
          render: (data, type, row) => {
            return data
              ? `<img src="${data}" alt="" style="width:50px;border-radius:100px">`
              : "";
          },
        },
        {
          title: "Tipo",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            if (data.type_client == "1" || data.type_client == "0") {
              name = "Cliente";
              badge = "badge-success";
            } else if (data.type_client == "2") {
              name = "Proveedor";
              badge = "badge-info";
            }

            return `<a href="javascript:;" <span id="type-${data.id_client}" class="badge ${badge} changeType">${name}</span></a>`;
          },
        },
        // {
        //   title: "",
        //   data: null,
        //   className: "uniqueClassName dt-head-center",
        //   render: (data, type, row) => {
        //     return `<input type="checkbox" class="form-control-updated checkClient" id="check-${
        //       data.id_client
        //     }" ${data.status == 1 ? "checked" : ""}>`;
        //   },
        // },
        {
          title: "Acciones",
          data: "id_client",
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx copyClient bx-copy-alt" data-toggle='tooltip' title='Clonar Cliente' style="font-size: 30px; color:green"></i></a>
                <a href="javascript:;" <i class="bx bx-edit-alt updateClient" id="${data}" data-toggle='tooltip' title='Actualizar Cliente' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i class="mdi mdi-delete-forever deleteClient" id="${data}" data-toggle='tooltip' title='Eliminar Cliente' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
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
    });
  };

  loadAllDataClients();
});
