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

    $("#formCreateMaterial").trigger("reset");
  });

  /* Crear producto */
  $("#btnCreateMaterial").click(function (e) {
    e.preventDefault(); 
    // const apiUrl = idMaterial ? "/api/updateMaterials" : "/api/addMaterials";
    checkDataMaterial('/api/addInvMaterials');
  });

  /* Actualizar productos */

  $(document).on("click", ".updateRawMaterials", function (e) {
    $(".cardImportMaterials").hide(800);
    $("#units").empty();
    $(".cardRawMaterials").show(800);
    $("#btnCreateMaterial").text("Actualizar");

    // Obtener el ID del elemento
    // let idMaterial = $(this).attr("id").split("-")[1];

    // sessionStorage.setItem("id_material", idMaterial);

    //obtener data
    const row = $(this).closest("tr")[0];
    let data = tblRawMaterials.fnGetData(row);
 
    $(`#refMaterial option[value=${data.id_material}]`).prop(
      "selected",
      true
    );
    $(`#material option[value=${data.id_material}]`).prop(
      "selected",
      true
    );
    $(`#materialType option[value=${data.id_material_type}]`).prop(
      "selected",
      true
    ); 
 
    $("#mQuantity").val(data.quantity); 
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
  const checkDataMaterial = async (url) => { 
    let id_material = parseInt($("#material").val());  
    let quantity = parseFloat($("#mQuantity").val()); 
    let grammage = parseFloat($("#grammage").val());

    let data = id_material * quantity;

    if (data <= 0 || isNaN(data)) {
      toastr.error("Ingrese todos los campos");
      return false;
    } 
    
    if (flag_products_measure == "1") {
      if (
        isNaN(grammage) ||
        grammage <= 0 
      ) {
        toastr.error("Ingrese todos los campos");
        return false;
      }
    }

    let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

    let arr = dataMaterials.find(item => item.id_material == id_material);

    if (quantity < parseFloat(arr.reserved)) {
      toastr.error("Existencias con menor cantidad de las reservadas");
      return false;
    }

    let dataMaterial = new FormData(formCreateMaterial); 

    dataMaterial.append("idMaterial", id_material);

    let resp = await sendDataPOST(url, dataMaterial);

    messageMaterials(resp);
  };

  /* Eliminar productos 
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
  }; */

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
