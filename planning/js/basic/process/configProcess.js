$(document).ready(function () {
  $('.cardCreateProcess').hide();

  $('#btnNewProcess').click(function (e) {
    e.preventDefault();
    $('.cardCreateProcess').toggle(800);
  });

  $.ajax({
    type: 'GET',
    url: '/api/process',
    success: function (r) {
      let $select = $(`#idProcess`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_process}> ${value.process} </option>`
        );
      });
    },
  });
});
