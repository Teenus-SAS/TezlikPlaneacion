$(document).ready(function () {
  sessionStorage.removeItem('machinesData');

  $('.cardCreateMachines').hide();

  $('#btnNewMachine').click(function (e) {
    e.preventDefault();
    $('.cardCreateMachines').toggle(800);
  });

  $.ajax({
    type: 'GET',
    url: '/api/machines',
    success: function (r) {
      machinesData = JSON.stringify(r);
      sessionStorage.setItem('machinesData', machinesData);

      let $select = $(`#idMachine`);
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      $select.append(`<option value="0">Proceso Manual</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_machine}> ${value.machine} </option>`
        );
      });

      // $select1.append(`<option disabled selected>Seleccionar</option>`)
      // $.each(r, function(i, value) {
      //     $select1.append(
      //         `<option value = ${value.id_material}> ${value.product} </option>`,
      //     )
      // })
    },
  });
});
