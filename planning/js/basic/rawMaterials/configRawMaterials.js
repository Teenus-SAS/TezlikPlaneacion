$(document).ready(function () {
  /* Cargar data materia prima */
  loadDataMaterial = async () => {
    sessionStorage.removeItem("dataMaterials");
    let data = await searchData("/api/materials");

    let dataMaterials = JSON.stringify(data);
    sessionStorage.setItem("dataMaterials", dataMaterials);

    setSelectsMaterials(data);
  };

  setSelectsMaterials = (data) => {
    const refMaterials = sortFunction(data, "reference");
    const materials = sortFunction(data, "material");

    const $selectRefMaterial = $(`#refMaterial`);
    const $selectMaterial = $(`#material`);

    // Vaciar el select y agregar la opción por defecto
    $selectRefMaterial
      .empty()
      .append(`<option disabled selected>Seleccionar</option>`);

    $selectMaterial
      .empty()
      .append(`<option disabled selected>Seleccionar</option>`);

    // Usar map para optimizar el ciclo de iteración
    const optionsrefMaterials = refMaterials.map(
      (value) =>
        `<option value ="${value.id_material}" class="${value.id_material_type}"> ${value.reference} </option>`
    );

    const optionsMaterials = materials.map(
      (value) =>
        `<option value ="${value.id_material}" class="${value.id_material_type}"> ${value.material} </option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $selectRefMaterial.append(optionsrefMaterials.join(""));
    $selectMaterial.append(optionsMaterials.join(""));
  };

  loadDataMaterial();

  /* Funciones cuando se selecciona una materia prima */

  if (viewRawMaterial == 1) {
    $("#refMaterial").change(function (e) {
      e.preventDefault();
      let id = this.value;

      $("#material option").prop("selected", function () {
        return $(this).val() == id;
      });

      let material_type = parseInt(
        $(this).find("option:selected").attr("class")
      );
      $(`#materialType option[value=${material_type}]`).prop("selected", true);

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));

      let arr = data.find((item) => item.id_material == id);

      if (flag_products_measure == "1") {
        let $select = $(`#units`);
        $select.empty().append(`<option disabled>Seleccionar</option>`);
        $select.append(
          `<option value ='${arr.id_unit}' selected> ${arr.unit} </option>`
        );
      } else loadUnitsByMagnitude(arr, 2);
    });

    $("#material").change(function (e) {
      e.preventDefault();
      let id = this.value;

      $("#refMaterial option").prop("selected", function () {
        return $(this).val() == id;
      });

      let material_type = parseInt(
        $(this).find("option:selected").attr("class")
      );
      $(`#materialType option[value=${material_type}]`).prop("selected", true);

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));

      let arr = data.find((item) => item.id_material == id);

      if (flag_products_measure == "1") {
        let $select = $(`#units`);
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

      $("#material option").prop("selected", function () {
        return $(this).val() == id;
      });

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));
      let arr = data.find((item) => item.id_material == id);
      $("#abbreviation").val(arr.abbreviation);
    });

    $("#material").change(function (e) {
      e.preventDefault();
      let id = this.value;

      $("#refMaterial option").prop("selected", function () {
        return $(this).val() == id;
      });

      let data = JSON.parse(sessionStorage.getItem("dataMaterials"));
      let arr = data.find((item) => item.id_material == id);
      $("#abbreviation").val(arr.abbreviation);
    });
  }
});
