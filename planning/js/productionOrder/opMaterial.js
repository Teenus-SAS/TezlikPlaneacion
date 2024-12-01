$(document).ready(function () {
  loadTblOPMaterial = (id_programming, visible) => {
    tblOPMaterial = $("#tblOPMaterial").DataTable({
      destroy: true,
      dom: "t",
      paging: false,
      info: false,
      searching: false,
      ajax: {
        url: `/api/productionOrderMaterial/${id_programming}`,
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
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Material",
          data: "material",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Medida",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Operador",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            return `${data.firstname} ${data.lastname}`;
          },
        },
        {
          title: "Cantidad",
          data: "quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 0, ""),
        },
        {
          title: "Fecha Creacion",
          data: "creation_date",
          className: "uniqueClassName dt-head-center",
          width: "200px",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY hh:mm A");
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center", 
          visible: visible,
          render: function (data) {
            let action;
            if (!data.receive_date || data.receive_date == "0000-00-00") {
              action = `<a href="javascript:;" <i id="upd-${data.id_prod_order_material}" class="bx bx-edit-alt updateOPMaterial" data-toggle='tooltip' title='Actualizar Material' style="font-size: 30px;"></i></a>
                            <a href="javascript:;" <i id="${data.id_prod_order_material}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Material' style="font-size: 30px;color:red" onclick="deleteOPMaterialFunction()"></i></a>`;
            } else {
              let fechaHora = new Date(data.receive_date);
              let fechaHoraFormateada = fechaHora.toLocaleDateString("es-CO", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
              });

              action = `Recibido: ${data.firstname_receive} ${data.lastname_receive}<br>${fechaHoraFormateada}`;
            }
            return action;
          },
        },
      ],
    });
  };

  /* Crear OP Parcial */
  $("#btnAddOPMP").click(function (e) {
    e.preventDefault();

    const idOPM = sessionStorage.getItem("id_prod_order_material") || null;
    const apiUrl = !idOPM ? "/api/addOPMaterial" : "/api/updateOPMaterial";

    checkDataOPMaterial(apiUrl, idOPM);
  });

  /* Actualizar OP Parcial */
  $(document).on("click", ".updateOPMaterial", function (e) {
    $("#btnAddOPMP").text("Actualizar");

    // Obtener el ID del elemento
    const idOPM = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_prod_order_material", idOPM);

    // Obtener data 
    let row = $(this).closest('tr');
    let data = $('#tblOPMaterial').DataTable().row(row).data();

    // Asignar valores a los campos del formulario y animar
    $(`#refMaterial option[value=${data.id_material}]`).prop("selected", true);
    $(`#material option[value=${data.id_material}]`).prop("selected", true);
    $("#quantityMP").val(data.quantity);
  });

  // Entregas Parciales
  const checkDataOPMaterial = async (url, idOPM) => {
    let id_material = parseInt($("#refMaterial").val());
    let quantity = parseInt($("#quantityMP").val());

    let data = id_material * quantity;

    if (isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let id_programming = sessionStorage.getItem("id_programming");

    let dataOP = new FormData(formAddOPMP);
    dataOP.append("idProgramming", id_programming);

    if (idOPM) dataOP.append("idOPM", idOPM);

    let resp = await sendDataPOST(url, dataOP);

    messageOPMaterial(resp);
  };

  /* Eliminar productos */
  deleteOPMaterialFunction = () => { 
    let row = $(this.activeElement).parent().parent()[0];
    let data = $('#tblOPMaterial').DataTable().row(row).data();

    const { id_prod_order_material } = data;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta materia prima? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $.get(
            `/api/deleteOPMaterial/${id_prod_order_material}`,
            function (data, textStatus, jqXHR) {
              messageOPMaterial(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  const messageOPMaterial = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $("#formAddOPMP").trigger("reset");
      toastr.success(message);
      let id_programming = sessionStorage.getItem('id_programming');
      loadTblOPMaterial(id_programming, true);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
