$(document).ready(function () {
  /* Cargar data compañia */
  loadCompanyData = async () => {
    let data = await searchData('/api/company');

    $('#company').html(data[0].company);
    $('#nit').html(data[0].nit);
    $('#city').html(data[0].city);
    $('#country').html(data[0].country);
    $('#phone').html(data[0].telephone);
    $('#address').html(data[0].address);
    $('#logo').prop('src', data[0].logo);

    $('#qFooter').html(
      `Autorizo a ${data[0].company}. para recaudar, almacenar, utilizar y actualizar mis datos personales con fines exclusivamente comerciales y garantizándome que esta información no será revelada a terceros salvo orden de autoridad competente. Ley 1581 de 2012, Decreto 1377 de 2013.`
    );
  };

  loadCompanyData();
});
