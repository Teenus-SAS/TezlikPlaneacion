$(document).ready(function () {
  // loadOrdersProgramming = async () => {
  //   let data = await searchData('/api/ordersProgramming');

  //   if (data.length == 0) {
  //     return 1;
  //   }
    
  //   data = data.reduce((acc, current) => {
  //     if (!acc.some(item => item.num_order === current.num_order)) {
  //       acc.push(current);
  //     }
  //     return acc;
  //   }, []);

  //   let $select = $(`#order`);
  //   $select.empty();

  //   $select.append(`<option disabled selected>Seleccionar</option>`);
  //   $.each(data, function (i, value) {
  //     $select.append(
  //       `<option value ='${value.id_order}'> ${value.num_order} </option>`
  //     );
  //   });
  // }
  // loadOrdersProgramming = async () => {
  //   try {
  //     let data = await searchData('/api/ordersProgramming');

  //     if (data.length === 0) {
  //       return 1;
  //     }

  //     data = data.reduce((acc, current) => {
  //       if (!acc.some(item => item.num_order === current.num_order)) {
  //         acc.push(current);
  //       }
  //       return acc;
  //     }, []);

  //     let $select = $(`#order`);
  //     $select.empty();

  //     $select.append(`<option disabled selected>Seleccionar</option>`);

  //     $.each(data, function (i, value) {
  //       $select.append(
  //         `<option value ='${value.id_order}'> ${value.num_order} </option>`
  //       );
  //     });
  //   } catch (error) {
  //     console.error('Error loading orders for programming:', error);
  //   }
  // };

  // loadOrdersProgramming();
});
