sessionStorage.removeItem("machinesData");

loadDataMachines = async (op) => {
  let data = await searchData("/api/machines");

  machinesData = JSON.stringify(data);
  sessionStorage.setItem("machinesData", machinesData);

  let $selectMachine = $(`.idMachine`);

  // Vaciar el select y agregar la opción por defecto
  $selectMachine
    .empty()
    .append("<option disabled selected>Seleccionar</option>");

  if (op == 1)
    $selectMachine.append(`<option value="0">PROCESO MANUAL</option>`);
  else if (op == 3) $selectMachine.append(`<option value="0">Todos</option>`);

  // Usar map para optimizar el ciclo de iteración
  const options = data.map(
    (value) => `<option value="${value.id_machine}">${value.machine}</option>`
  );

  // Insertar todas las opciones de una vez para mejorar el rendimiento
  $selectMachine.append(options.join(""));
};
