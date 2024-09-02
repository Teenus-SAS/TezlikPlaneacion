$(document).ready(function () {
  // loadClients(2);
  /* Ocultar panel crear stock */

  $(".cardCreatePStock").hide();

  /* Abrir panel crear stock */

  $("#btnNewPStock").click(function (e) {
    e.preventDefault();

    $(".cardImportPStock").hide(800);
    $(".cardCreatePStock").toggle(800);
    $("#formCreatePStock").trigger("reset");
    $("#btnCreatePStock").text("Crear");

    sessionStorage.removeItem("idStock");
  });

  /* Crear nuevo proceso */

  $("#btnCreatePStock").click(function (e) {
    e.preventDefault();

    const idStock = sessionStorage.getItem("idStock") || null;
    const apiUrl = idStock ? "/api/updatePStock" : "/api/addPStock";

    checkDataArea(apiUrl, idStock);
  });

  /* Actualizar procesos */

  $(document).on("click", ".updatePStock", function (e) {
    $(".cardImportPStock").hide(800);
    $(".cardCreatePStock").show(800);
    $("#btnCreatePStock").text("Actualizar");

    const row = $(this).closest("tr")[0];
    const data = tblPStock.fnGetData(row);

    sessionStorage.setItem("idStock", data.id_stock_product);
    $(`#refProduct option[value=${data.id_product}]`).prop("selected", true);
    $(`#selectNameProduct option[value=${data.id_product}]`).prop(
      "selected",
      true
    );
    $("#pMin").val(data.min_term);
    $("#pMax").val(data.max_term);

    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataPStock = async (url, idStock) => {
    let id_product = parseFloat($("#refProduct").val());
    let min = parseFloat($("#pMin").val());
    let max = parseFloat($("#pMax").val());

    let data = id_product * max * min;

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

    let dataStock = new FormData(formCreatePStock);
    dataStock.append("idProduct", id_product);

    if (idStock) dataStock.append("idStock", idStock);

    let resp = await sendDataPOST(url, dataStock);

    messagePS(resp);
  };

  /* Eliminar proceso 

    deleteFunction = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblPStock.fnGetData(row);

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
                        // `../../api/deletePlanProcess/${id_Stock}`,
                        function (data, textStatus, jqXHR) {
                            message(data);
                        }
                    );
                }
            },
        });
    }; */

  /* Mensaje de exito */

  messagePS = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportPStock").hide(800);
      $(".cardCreatePStock").hide(800);
      $("#formCreatePStock").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblPStock").DataTable().clear();
    $("#tblPStock").DataTable().ajax.reload();
  }
});
