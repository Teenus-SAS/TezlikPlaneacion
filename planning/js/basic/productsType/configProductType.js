$(document).ready(function () {
  var loadTblProductType;

  loadAllDataPType = async () => {
    let data = await searchData("/api/productsType");

    loadSelectProductType(data);

    if (loadTblProductType) loadTblProductType(data);
  };

  const loadSelectProductType = (data) => {
    let $select = $(`#idProductType`);
    // Vaciar el select y agregar la opción por defecto
    $select.empty().append(`<option disabled selected>Seleccionar</option>`);

    // Usar map para optimizar el ciclo de iteración
    const options = data.map(
      (value) =>
        `<option value="${value.id_product_type}">${value.product_type}</option>`
    );

    // Insertar todas las opciones de una vez para mejorar el rendimiento
    $select.append(options.join(""));
  };

  loadAllDataPType();
});
