$(document).ready(function () {
  /* Ocultar panel crear producto */

  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    $(".cardGeneral").hide();

    let card = this.id;

    const sections = {
      "nav-materials": ".cardProductsMaterials",
      "nav-planCicles": ".cardPlanCicles",
      "nav-route": ".cardRoutes",
      "nav-plans": ".cardPlans",
    };

    // Mostrar la sección correspondiente según la opción seleccionada
    $(sections[card] || "").show();

    /* switch (card) {
      case "nav-materials":
        $(".cardProductsMaterials").show();
        break;
      case "nav-planCicles":
        $(".cardPlanCicles").show();
        break;
      case "nav-route":
        $(".cardRoutes").show();
        break;
      case "nav-plans":
        $(".cardPlans").show();
        break;
    } */

    let tables = document.getElementsByClassName("dataTable");

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = "100%";
      attr = tables[i].firstElementChild;
      attr.style.width = "100%";
    }
  });

  // Tipo Material
  $("#materialType").change(async function (e) {
    e.preventDefault();

    $(".inputQuantityCalc").hide();
    $("#units").empty();
    $("#quantity, #quantityCalc").val("");

    const type = this.value;

    const dataMaterials = JSON.parse(sessionStorage.getItem("dataMaterials"));

    const dataM = dataMaterials.filter((item) => item.id_material_type == type);

    setSelectsMaterials('#refMaterial', dataM, 'reference');
    setSelectsMaterials('#material', dataM, 'material');

    if (type != "1") $(".inputQuantityCalc").show();
  });

  // if (flag_products_measure == "1") {
  $("#quantity").prop("readonly", true);

  $(document).on("change keyup", ".calcMWeight", async function () {
    const idProduct = parseInt($("#selectNameProduct").val());
    const idMaterial = parseInt($("#refMaterial").val());
    const type = parseInt($("#materialType").val());

    const validate = idProduct * idMaterial * type;

    if (!validate) return false;

    let quantity = parseFloat($("#quantityCalc").val());

    isNaN(quantity) ? (quantity = 0) : quantity;

    $.ajax({
      type: "POST",
      url: "/api/calcQuantityFTM",
      data: {
        idProduct: idProduct,
        idMaterial: idMaterial,
        type: type,
        quantityCalc: quantity,
      },
      success: function (resp) {
        $("#quantity").val(resp.weight);
      },
    });
  });
  // }

  $(document).on("change keyup", ".calcAMWeight", async function () {
    const idProduct = parseInt($("#selectNameProduct").val());
    const idMaterial = parseInt($("#aMaterial").val());
    // const type = parseInt($("#materialType").val());

    const validate = idProduct * idMaterial;

    if (!validate) return false;

    // let quantity = parseFloat($("#quantityCalc").val());

    // isNaN(quantity) ? (quantity = 0) : quantity;

    $.ajax({
      type: "POST",
      url: "/api/calcQuantityFTM",
      data: {
        idProduct: idProduct,
        idMaterial: idMaterial,
      },
      success: function (resp) {
        $("#aQuantity").val(resp.weight);
      },
    });
  });

  $(".cardAddMaterials").hide();

  /* Abrir panel crear producto */

  $("#btnCreateProduct").click(function (e) {
    e.preventDefault();

    $(
      ".cardAddNewProduct, .inputQuantityCalc, .cardImportProductsMaterials, .cardAddNewProduct"
    ).hide(800);
    // $('.cardTableConfigMaterials').show(800);
    $(".cardAddMaterials").toggle(800);
    $("#btnAddMaterials").text("Asignar");

    sessionStorage.removeItem("id_product_material");

    $("#formAddMaterials").trigger("reset");
  });

  /* Adicionar nueva materia prima */

  $("#btnAddMaterials").click(function (e) {
    e.preventDefault();

    let idProductMaterial =
      sessionStorage.getItem("id_product_material") || null;

    const apiUrl = idProductMaterial
      ? "/api/updatePlanProductsMaterials"
      : "/api/addProductsMaterials";

    checkDataPMaterial(apiUrl, idProductMaterial);
  });

  /* Actualizar productos materials */

  $(document).on("click", ".updateMaterials", function (e) {
    $(".cardImportProductsMaterials").hide(800);
    $(".cardAddNewProduct").hide(800);
    $(".cardAddMaterials").show(800);
    $("#btnAddMaterials").text("Actualizar");
    $("#units").empty();

    // Obtener el ID del elemento
    let id_product_material = $(this).attr("id").split("-")[1];

    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem("id_product_material", id_product_material);

    $(`#refMaterial option[value=${data.id_material}]`).prop("selected", true);
    $(`#material option[value=${data.id_material}]`).prop("selected", true);

    $("#quantity").val(data.quantity);

    if (data.id_magnitude == 0 || data.id_unit == 0) {
      let dataMaterials = JSON.parse(sessionStorage.getItem("dataMaterials"));

      let arr = dataMaterials.find(
        (item) => item.id_material == data.id_material
      );

      data.id_magnitude = arr.id_magnitude;
      data.id_unit = arr.id_unit;
    }

    loadUnitsByMagnitude(data, 2);
    $(`#units option[value=${data.id_unit}]`).prop("selected", true);

    if (flag_products_measure == "1") {
      $(`#materialType option[value=${data.id_material_type}]`).prop(
        "selected",
        true
      );

      $("#quantity").prop("readonly", true);

      if (data.id_material_type == "1") $(".inputQuantityCalc").hide();
      else $(".inputQuantityCalc").show();
    }

    // Animación de desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataPMaterial = async (url, idProductMaterial) => {
    const ref = $("#material").val();
    const quan = $("#quantity").val();
    const idProduct = $("#selectNameProduct").val();
    const unit = $("#units").val();

    const data = ref * idProduct * unit;

    if (!data) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let dataMaterials = new FormData(formAddMaterials);
    dataMaterials.append("idProduct", idProduct);

    if (idProductMaterial != "" || idProductMaterial != null)
      dataMaterials.append("idProductMaterial", idProductMaterial);

    let resp = await sendDataPOST(url, dataMaterials);

    messageMaterial(resp);
  };

  /* Eliminar materia prima */

  deleteMaterial = (op) => {
    // let allData = tblConfigMaterials.rows().data().toArray();
    // let data = allData.find(item => item.id_product_material == id);

    // let dataMaterials = {};

    // dataMaterials['idProductMaterial'] = data.id_product_material;
    // dataMaterials['idMaterial'] = data.id_material;
    // dataMaterials['idProduct'] = data.id_product;

    // bootbox.confirm({
    //   title: 'Eliminar',
    //   message:
    //     'Está seguro de eliminar esta Materia prima? Esta acción no se puede reversar.',
    //   buttons: {
    //     confirm: {
    //       label: 'Si',
    //       className: 'btn-success',
    //     },
    //     cancel: {
    //       label: 'No',
    //       className: 'btn-danger',
    //     },
    //   },
    //   callback: function (result) {
    //     if (result) {
    //       $.post('/api/deletePlanProductMaterial', dataMaterials,
    //         function (data, textStatus, jqXHR) {
    //           messageMaterial(data);
    //         },
    //       );
    //     }
    //   },
    // });

    //obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblConfigMaterials.fnGetData(row);

    let idProduct = $("#selectNameProduct").val();
    let dataP = {};
    dataP["idProduct"] = idProduct;

    if (op == "1") {
      let idProductMaterial = data.id_product_material;
      dataP["idProductMaterial"] = idProductMaterial;
      dataP["idMaterial"] = data.id_material;

      url = "/api/deletePlanProductMaterial";
    } else {
      dataP["idCompositeProduct"] = data.id_composite_product;
      url = "/api/deleteCompositeProduct";
    }

    bootbox.confirm({
      title: "Eliminar",
      message: `Está seguro de eliminar ${op == "1" ? "esta Materia prima" : "este Producto Compuesto"
        }? Esta acción no se puede reversar.`,
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
        if (result == true) {
          $.post(url, dataP, function (data, textStatus, jqXHR) {
            messageMaterial(data);
          });
        }
      },
    });
  };

  // Maquina alterna
  $(document).on('click', '.alternalMachine', function () {
    $('#formSaveAlternalMachine').trigger('reset');
    $(".cardCreatePlanCiclesMachine").hide(800);
    $('.cardSaveAlternalMachine').show(800);
    $('.inputsAlternalUnds').hide();

    // Obtener el ID del elemento
    const id_cicles_machine = $(this).attr("id").split("-")[1];
    sessionStorage.setItem("id_cicles_machine", id_cicles_machine);

    //obtener data
    const row = $(this).closest("tr")[0];
    const data = tblPlanCiclesMachine.fnGetData(row);

    if (data.id_alternal_machine != 0) {
      $('.inputsAlternalUnds').show();

      $(`#idMachine1 option[value=${data.id_alternal_machine}]`).prop("selected", true);
      $('#ciclesHour1').val(data.alternal_cicles_hour);
      $('#unitsTurn').val(parseFloat(data.alternal_units_turn).toLocaleString('es-CO', {
        minimumFractionDigits: 0, maximumFractionDigits: 2
      }));
      $('#unitsMonth').val(parseFloat(data.alternal_units_month).toLocaleString('es-CO', {
        minimumFractionDigits: 0, maximumFractionDigits: 2
      }));
    }


    //animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $("#btnSaveAlternalMachine").click(async function (e) {
    e.preventDefault();
 
    const idMachine = parseInt($("#idMachine").val());
    const idMachine1 = parseInt($("#idMachine1").val());
    const ciclesHour = $("#ciclesHour1").val();

    let data = idMachine * ciclesHour;

    if (!data || isNaN(idMachine)) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (idMachine == idMachine1) {
      toastr.error("Seleccione una maquina diferente");
      return false;
    }

    let idCiclesMachine = sessionStorage.getItem('id_cicles_machine');

    let dataPlanCiclesMachine = new FormData(formSaveAlternalMachine);
 
    dataPlanCiclesMachine.append("idCiclesMachine", idCiclesMachine);

    let resp = await sendDataPOST('/api/saveAlternalMachine', dataPlanCiclesMachine);

    messageMachine(resp);
  });

  /* Mensaje de exito */

  messageMaterial = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImport, .cardAddMaterials, .cardAddNewProduct").hide(800);
      $("#formImport, #formAddMaterials").trigger("reset");

      const idProduct = $("#selectNameProduct").val();
      if (idProduct) loadAllDataMaterials(idProduct);

      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
