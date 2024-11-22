$(document).ready(function () {
  /* Ocultar panel para crear materiales */

  $(".cardRawMaterials").hide();

  /* Abrir panel para crear materiales */
  $("#btnNewMaterial").click(function (e) {
    e.preventDefault();
    $(".cardImportMaterials").hide(800);
    $(".cardRawMaterials").toggle(800);
    $("#units").empty();

    sessionStorage.removeItem("id_material");

    $("#formCreateMaterial").trigger("reset");
  });

  /* Crear producto */
  $("#btnCreateMaterial").click(function (e) {
    e.preventDefault();
    let idMaterial = sessionStorage.getItem("id_material") || null;
    const apiUrl = idMaterial ? "/api/updateMaterials" : "/api/addMaterials";
    checkDataMaterial(apiUrl, idMaterial);
  });

  /* Actualizar productos */

  $(document).on("click", ".updateRawMaterials", function (e) {
    $(".cardImportMaterials").hide(800);
    $("#units").empty();
    $(".cardRawMaterials").show(800);
    // Obtener el ID del elemento
    let idMaterial = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_material", idMaterial);

    //obtener data
    const row = $(this).closest("tr")[0];
    let data = tblRawMaterials.fnGetData(row);

    $("#refRawMaterial").val(data.reference);
    $("#nameRawMaterial").val(data.material);
    $(`#materialType option[value=${data.id_material_type}]`).prop(
      "selected",
      true
    );
    $(`#magnitudes option[value=${data.id_magnitude}]`).prop("selected", true);
    loadUnitsByMagnitude(data.id_magnitude, 1);
    $(`#units option[value=${data.id_unit}]`).prop("selected", true);
    $("#costMaterial").val(data.cost);

    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data materia prima */
  const checkDataMaterial = async (url, idMaterial) => {
    let ref = $("#refRawMaterial").val();
    let material = $("#nameRawMaterial").val();
    let materialType = parseInt($("#materialType").val());
    let unity = parseInt($("#units").val());
    let cost = parseFloat($("#costMaterial").val());

    let data = unity * cost;

    if (ref == "" || material == "" ||
      data <= 0 || isNaN(data)
    ) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataMaterial = new FormData(formCreateMaterial);
    dataMaterial.append("idMaterialType", materialType);

    if (idMaterial) {
      dataMaterial.append("idMaterial", idMaterial);
    }

    let resp = await sendDataPOST(url, dataMaterial);

    messageMaterials(resp);
  };

  /* Eliminar productos */

  deleteMaterialsFunction = () => {
    //obtener data
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblRawMaterials.fnGetData(row);

    let idMaterial = data.id_material;

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
            `/api/deleteMaterial/${idMaterial}`,
            function (data, textStatus, jqXHR) {
              messageMaterials(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageMaterials = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportMaterials, .cardRawMaterials").hide(800);
      $("#formImportMaterials, #formCreateMaterial").trigger("reset");
      toastr.success(message);
      updateTable();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblRawMaterials").DataTable().clear();
    $("#tblRawMaterials").DataTable().ajax.reload();
  }
});
