$(document).ready(function () {
  /* Ocultar panel para crear Areas */
  $(".cardCreateArea").hide();

  /* Abrir panel para crear Areas */

  $("#btnNewArea").click(function (e) {
    e.preventDefault();
    $(".cardImportAreas").hide(800);
    $(".cardCreateArea").toggle(800);
    $("#btnCreateArea").html("Crear");

    sessionStorage.removeItem("id_plan_area");

    $("#formCreateArea").trigger("reset");
  });

  /* Crear area */
  $("#btnCreateArea").click(function (e) {
    e.preventDefault();

    const idArea = sessionStorage.getItem("id_plan_area") || null;
    const apiUrl = idArea ? "/api/updateArea" : "/api/addPlanArea";

    checkDataArea(apiUrl, idArea);
  });

  /* Actualizar area */
  $(document).on("click", ".updateArea", function (e) {
    $(".cardImportAreas").hide(800);
    $(".cardCreateArea").show(800);
    $("#btnCreateArea").html("Actualizar");

    // Obtener el ID del elemento
    let id = $(this).attr("id");
    // Obtener la parte después del guion '-'
    let idArea = id.split("-")[1];

    sessionStorage.setItem("id_plan_area", idArea);

    let row = $(this).parent().parent()[0];
    let data = tblAreas.fnGetData(row);

    $("#area").val(data.area);

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Verificar datos */
  const checkDataArea = async (url, idArea) => {
    let area = $("#area").val();

    if (!area.trim()) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataArea = new FormData(formCreateArea);
    if (idArea) dataArea.append("idArea", idArea);
    let resp = await sendDataPOST(url, dataArea);
    messageArea(resp);
  };

  /* Eliminar areas */
  deleteAreaFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblAreas.fnGetData(row);

    let id_plan_area = data.id_plan_area;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta area? Esta acción no se puede reversar.",
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
            `/api/deleteArea/${id_plan_area}`,
            function (data, textStatus, jqXHR) {
              messageArea(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageArea = (data) => {
    if (data.success == true) {
      $(".cardImportAreas").hide(800);
      $("#formImportAreas").trigger("reset");
      $(".cardCreateArea").hide(800);
      $("#formCreateArea").trigger("reset");
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblAreas").DataTable().clear();
    $("#tblAreas").DataTable().ajax.reload();
  }
});
