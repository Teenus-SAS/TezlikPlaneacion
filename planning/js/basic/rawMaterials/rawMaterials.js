$(document).ready(function () {
  /* Ocultar panel para crear materiales */

  $(".cardRawMaterials").hide();

  /* Abrir panel para crear materiales */
  $("#btnNewMaterial").click(function (e) {
    e.preventDefault();
    $(".cardImportMaterials").hide(800);
    $(".cardRawMaterials").toggle(800);
    $("#btnCreateMaterial").text("Crear");
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
    $("#btnCreateMaterial").text("Actualizar");

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

    quantity = data.quantity;

    // if (quantity.isInteger) quantity = quantity.toLocaleString('es-CO');
    // else
    //   quantity = quantity.toLocaleString(undefined, {
    //     minimumFractionDigits: 2,
    //     maximumFractionDigits: 2,
    //   });
    $("#mQuantity").val(quantity);
    $("#grammage").val(data.grammage);

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
    let materialType = parseFloat($("#materialType").val());
    let unity = $("#unit").val();
    let quantity = parseFloat($("#mQuantity").val());
    let grammage = parseFloat($("#grammage").val());

    if (ref == "" || material == "" || unity == "") {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    // quantity = parseFloat(strReplaceNumber(quantity));

    quantity = 1 * quantity;

    if (quantity <= 0 || isNaN(quantity)) {
      toastr.error("La cantidad debe ser mayor a cero (0)");
      return false;
    }

    if (flag_products_measure == "1") {
      if (
        isNaN(grammage) ||
        grammage <= 0 ||
        isNaN(materialType) ||
        materialType <= 0
      ) {
        toastr.error("Ingrese todos los campos");
        return false;
      }
    } else {
      materialType = 0;
    }

    let dataMaterial = new FormData(formCreateMaterial);
    dataMaterial.append("idMaterialType", materialType);

    if (idMaterial) {
      let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

      let arr = dataMaterials.find(item => item.id_material == idMaterial);

      if (quantity < parseFloat(arr.reserved)) {
        toastr.error("Existencias con menor cantidad de las reservadas");
        return false;
      }

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
      loadAllData();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
