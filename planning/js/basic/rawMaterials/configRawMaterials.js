$(document).ready(function () {
  /* Cargar data materia prima */
  const loadDataMaterial = async () => {
    sessionStorage.removeItem("dataMaterials");
    let data = await searchData("/api/materials");

    let dataMaterials = JSON.stringify(data);
    sessionStorage.setItem("dataMaterials", dataMaterials);

    setTimeout(() => {
      let btnDeliverPartialOP = document.getElementById('btnDeliverPartialOP');

      if (btnDeliverPartialOP) {
        let id_programming = sessionStorage.getItem('id_programming');
        let dataOP = JSON.parse(sessionStorage.getItem('dataOP'));

        let arr = dataOP.find(item => item.id_programming == id_programming);
        let dataFTMaterials = JSON.parse(sessionStorage.getItem('dataFTMaterials'));

        data = dataFTMaterials.filter(item => item.id_product == arr.id_product)
          .map((item) => ({ ...item, reference: item.reference_material }));
      }

      setSelectsMaterials('.refMaterial', data, 'reference');
      setSelectsMaterials('.material', data, 'material');
    }, 2000);
  };

  // setSelectsMaterials = (data) => {
  // const refMaterials = sortFunction(data, "reference");
  // const materials = sortFunction(data, "material");

  // const $selectRefMaterial = $(`#refMaterial`);
  // const $selectMaterial = $(`#material`);

  // // Vaciar el select y agregar la opción por defecto
  // $selectRefMaterial
  //   .empty()
  //   .append(`<option disabled selected>Seleccionar</option>`);

  // $selectMaterial
  //   .empty()
  //   .append(`<option disabled selected>Seleccionar</option>`);

  // // Usar map para optimizar el ciclo de iteración
  // const optionsRefMaterials = refMaterials.map(
  //   (value) =>
  //     `<option value ="${value.id_material}" class="${value.id_material_type}"> ${value.reference} </option>`
  // );

  // const optionsMaterials = materials.map(
  //   (value) =>
  //     `<option value ="${value.id_material}" class="${value.id_material_type}"> ${value.material} </option>`
  // );

  // // Insertar todas las opciones de una vez para mejorar el rendimiento
  // $selectRefMaterial.append(optionsRefMaterials.join("")); 
  // };

  setSelectsMaterials = (selector, data, property) => {
    let $select = $(selector);
    $select.empty();
  
    let sortedData = sortFunction(data, property);
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
  
    $.each(sortedData, function (i, value) {
      $select.append(`<option value ='${value.id_material}' class='${value.id_material_type}'> ${value[property]} </option>`);
    });
  };

  loadDataMaterial();

  /* Funciones cuando se selecciona una materia prima */
  $("#refMaterial").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $("#material option").prop("selected", function () {
      return $(this).val() == id;
    });
  });

  $("#material").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $("#refMaterial option").prop("selected", function () {
      return $(this).val() == id;
    });
  });

  $("#aRefMaterial").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $("#aMaterial option").prop("selected", function () {
      return $(this).val() == id;
    });
  });

  $("#aMaterial").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $("#aRefMaterial option").prop("selected", function () {
      return $(this).val() == id;
    });
  });
  
  if (viewRawMaterial == 1) {
    $(".refMaterial").change(function (e) {
      e.preventDefault();
      let id = this.value;

      let material_type = parseInt(
        $(this).find("option:selected").attr("class")
      );
      $(`#materialType option[value=${material_type}]`).prop("selected", true);

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));

      let arr = data.find((item) => item.id_material == id);

      if (flag_products_measure == "1") {
        let $select = $(`.units`);
        $select.empty().append(`<option disabled>Seleccionar</option>`);
        $select.append(
          `<option value ='${arr.id_unit}' selected> ${arr.unit} </option>`
        );
      } else loadUnitsByMagnitude(arr, 2);
    });

    $(".material").change(function (e) {
      e.preventDefault();
      let id = this.value;

      let material_type = parseInt(
        $(this).find("option:selected").attr("class")
      );
      $(`#materialType option[value=${material_type}]`).prop("selected", true);

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));

      let arr = data.find((item) => item.id_material == id);

      if (flag_products_measure == "1") {
        let $select = $(`.units`);
        $select.empty().append(`<option disabled>Seleccionar</option>`);
        $select.append(
          `<option value ='${arr.id_unit}' selected> ${arr.unit} </option>`
        );
      } else loadUnitsByMagnitude(arr, 2);
    });
  } else {
    $("#refMaterial").change(function (e) {
      e.preventDefault();
      let id = this.value;

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));
      let arr = data.find((item) => item.id_material == id);
      $("#abbreviation").val(arr.abbreviation);
      $("#units").val(arr.unit);
    });

    $("#material").change(function (e) {
      e.preventDefault();
      let id = this.value;

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));
      let arr = data.find((item) => item.id_material == id);
      $("#abbreviation").val(arr.abbreviation);
      $("#units").val(arr.unit);
    });
  }
});
