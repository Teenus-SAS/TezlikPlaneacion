$(document).ready(function () {
  var loadTblProductType;

  loadAllDataPType = async () => {
    const data = await searchData("/api/productsType");

    loadSelectProductType(data);

    if (loadTblProductType) loadTblProductType(data);
  };

  const loadSelectProductType = (data) => {
    const $selectProductType = $(`#idProductType`);

    // Vaciar el select y agregar la opción por defecto
    $selectProductType
      .empty()
      .append(`<option disabled selected>Seleccionar</option>`);

    // Usar map para optimizar el ciclo de iteración
    const options = data.map(
      (value) =>
        `<option value="${value.id_product_type}">${value.product_type}</option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $selectProductType.append(options.join(""));
  };

  loadAllDataPType();
});
