$(document).ready(function () {
  loadClients = async (op) => {
    let data = await searchData('/api/clients');

    if (op == 1)
      data = data.filter(item => item.type_client == 1 || item.type_client == 0);
    else
      data = data.filter(item => item.type_client == 2);
    
    let $select = $(`.client`);
    $select.empty();

    $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value =${value.id_client}> ${value.client} </option>`
      );
    });
  };

  setInputClient = async (data, element) => {
    let $select = $(`#${element}`);
    $select.empty();

    $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value =${value.id_client}> ${value.client} </option>`
      );
    });
  };

  // loadProviders = async () => {
  //   let data = await searchData('/api/clients');

  //   data = data.filter(item => item.type_client == 2);
    
  //   let $select = $(`#client`);
  //   $select.empty();

  //   $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
  //   $.each(data, function (i, value) {
  //     $select.append(
  //       `<option value =${value.id_client}> ${value.client} </option>`
  //     );
  //   });
  // };
});
