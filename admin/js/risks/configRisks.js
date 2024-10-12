$(document).ready(function () {
  $.ajax({
    url: '/api/risks',
    success: function (r) {
      sessionStorage.removeItem('dataRisks');

      let dataRisks = JSON.stringify(r);
      sessionStorage.setItem('dataRisks', dataRisks);

      let $select = $('#risk');

      $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value =${value.id_risk}> ${value.risk_level} </option>`
        );
      });
    },
  });
});
