// $(document).ready(function () {
  sessionStorage.removeItem('machinesData');

  loadDataMachines = async (op) => {
    let r = await searchData('/api/machines');

    machinesData = JSON.stringify(r);
    sessionStorage.setItem('machinesData', machinesData);

    let $select = $(`.idMachine`);
    $select.empty();
    $select.append(`<option value="0" disabled selected>Seleccionar</option>`);
    
    if(op == 1)
      $select.append(`<option value="0">Proceso Manual</option>`);
    
    $.each(r, function (i, value) {
      $select.append(
        `<option value = ${value.id_machine}> ${value.machine} </option>`
      );
    });
  }
  
// });
