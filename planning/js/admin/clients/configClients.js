$(document).ready(function () {
  loadClients = async (op) => {
    let data = await searchData('/api/clients');

    if (op == 1)
      data = data.filter(item => item.type_client == 1);
    else
      data = data.filter(item => item.type_client == 2);
    let $select = $(`#client`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_client}> ${value.client} </option>`
      );
    });
  }
});
