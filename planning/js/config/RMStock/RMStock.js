$(document).ready(function () {
  loadClients(2);

  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    $(".cardGeneralSN").hide();

    if (this.id == "nProducts") {
      $(".cardProducts").show();
      $(".cardMaterials").hide();
    } else if (this.id == "nMaterials") {
      $(".cardMaterials").show();
      $(".cardProducts").hide();
    }

    let tables = document.getElementsByClassName("dataTable");

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = "1217.55px";
      attr = tables[i].firstElementChild;
      attr.style.width = "1217.55px";
    }
  });

  /* Ocultar panel crear stock */

  $(".cardCreateRMStock").hide();

  /* Abrir panel crear stock */

  $("#btnNewRMStock").click(function (e) {
    e.preventDefault();

    $(".cardImportRMStock, .cardRMDescription").hide(800);
    $(".cardCreateRMStock").toggle(800);
    $("#formCreateRMStock").trigger("reset");
    $("#btnCreateRMStock").text("Crear");
    $(".cardSelect").show();
    // $('#client').empty();

    sessionStorage.removeItem("idStock");
  });

  /* Crear nuevo proceso */

  $("#btnCreateRMStock").click(function (e) {
    e.preventDefault();
    const idStock = sessionStorage.getItem("idStock") || null;
    const apiUrl = idStock ? "/api/updateRMStock" : "/api/addRMStock";
    checkDataRMStock(apiUrl, idStock);
  });

  /* Actualizar procesos */

  $(document).on("click", ".updateRMStock", function (e) {
    $(".cardImportRMStock, .cardSelect").hide(800);
    $(".cardCreateRMStock, .cardRMDescription").show(800);
    $("#btnCreateRMStock").text("Actualizar");

    //obtener data
    const row = $(this).closest("tr")[0];
    const data = tblRMStock.fnGetData(row);

    sessionStorage.setItem("idStock", data.id_stock_material);

    //cargar formulario
    $(`#refMaterial option[value=${data.id_material}]`).prop("selected", true);
    $(`#material option[value=${data.id_material}]`).prop("selected", true); 
    $("#referenceMName").val(data.reference);
    $("#materialName").val(data.material);

    // $("#providerName").val(data.client);

    $(`#client option[value=${data.id_provider}]`).prop("selected", true);
    $("#rMMin").val(data.min_term);
    $("#rMMax").val(data.max_term);
    $("#rMQuantity").val(data.min_quantity);
    $("#abbreviation").val(data.abbreviation);

    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataRMStock = async (url, idStock) => {
    const material = parseFloat($("#material").val());
    const provider = parseFloat($("#client").val());
    const min = parseFloat($("#rMMin").val());
    const max = parseFloat($("#rMMax").val());
    const quantity = parseFloat($("#rMQuantity").val());

    let data = material * provider * min * max * quantity;

    if (!data) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (min > max) {
      toastr.error(
        "El tiempo mínimo de producción debe ser menor al tiempo máximo"
      );
      return false;
    }

    const dataStock = new FormData(formCreateRMStock);
    dataStock.append("idMaterial", material);

    if (idStock) dataStock.append("idStock", idStock);

    const resp = await sendDataPOST(url, dataStock);
    messageRMS(resp);
  };

  /* Eliminar proceso 

    deleteFunction = () => {
        const row = $(this.activeElement).closest("tr")[0];
        let data = tblRMStock.fnGetData(row);

        // // let id_Stock = data.id_Stock;

        bootbox.confirm({
            title: 'Eliminar',
            message:
                'Está seguro de eliminar este proceso? Esta acción no se puede reversar.',
            buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-success',
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger',
                },
            },
            callback: function (result) {
                if (result) {
                    $.get(
                        // `/api/deletePlanProcess/${id_Stock}`,
                        function (data, textStatus, jqXHR) {
                            message(data);
                        }
                    );
                }
            },
        });
    }; */

  /* Mensaje de exito */

  messageRMS = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportRMStock, .cardCreateRMStock").hide(800);
      $("#formCreateRMStock").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblRMStock").DataTable().clear();
    $("#tblRMStock").DataTable().ajax.reload();
  }
});
