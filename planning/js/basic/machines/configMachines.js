sessionStorage.removeItem("machinesData");

loadDataMachines = async (op) => {
  let r = await searchData("/api/machines");

  machinesData = JSON.stringify(r);
  sessionStorage.setItem("machinesData", machinesData);

  let $select = $(`.idMachine`);

  // Vaciar el select y agregar la opción por defecto
  $select.empty().append("<option disabled selected>Seleccionar</option>");

  if (op == 1) $select.append(`<option value="0">PROCESO MANUAL</option>`);
  else if (op == 3) $select.append(`<option value="0">Todos</option>`);

  // Usar map para optimizar el ciclo de iteración
  const options = r.map(
    (value) => `<option value="${value.id_machine}">${value.machine}</option>`
  );

  // Insertar todas las opciones de una vez para mejorar el rendimiento
  $select.append(options.join(""));
};
