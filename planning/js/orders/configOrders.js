$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/orders',
    success: function (r) {
      let data = r.filter(item => item.status !== 'Despacho');
      data = data.reduce((acc, current) => {
        if (!acc.some(item => item.num_order === current.num_order)) {
          acc.push(current);
        }
        return acc;
      }, []);

      let $select = $(`#order`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(data, function (i, value) {
        $select.append(
          `<option value = ${value.id_order}> ${value.num_order} </option>`
        );
      });
    },
  });
});
