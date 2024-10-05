$(document).ready(function () {
  loadAllDataSellers = async () => {
    let data = await searchData("/api/sellers");

    sessionStorage.setItem("dataSellers", JSON.stringify(data));

    loadTblSellers(data);
  };

  const loadTblSellers = (data) => {
    tblSellers = $("#tblSellers").dataTable({
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
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
          title: "Nombre",
          data: "firstname",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Apellido",
          data: "lastname",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Email",
          data: "email",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Avatar",
          data: "avatar",
          className: "uniqueClassName dt-head-center",
          render: (data, type, row) => {
            return data
              ? `<img src="${data}" alt="" style="width:50px;border-radius:100px">`
              : "";
          },
        },
        {
          title: "",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data, type, row) => {
            return `<input type="checkbox" class="form-control-updated checkSeller" id="check-${
              data.id_seller
            }" ${data.status == 1 ? "checked" : ""}>`;
          },
        },
        {
          title: "Acciones",
          data: "id_seller",
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            return `
                <a href="javascript:;" <i class="bx bx-edit-alt updateSeller" id="${data}" data-toggle='tooltip' title='Actualizar Vendedor' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i class="mdi mdi-delete-forever deleteSeller" id="${data}" data-toggle='tooltip' title='Eliminar Vendedor' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
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

  loadAllDataSellers();
});
