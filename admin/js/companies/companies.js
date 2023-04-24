$(document).ready(function () {
  /* Abrir modal crear empresa */

  $('#btnNewCompany').click(function (e) {
    e.preventDefault();
    $('#createCompany').modal('show');
    $('#license').show();
    logo.src = '';
    sessionStorage.removeItem('id_company');
    $('#btnCreateCompany').html('Crear');
    $('#formCreateCompany').trigger('reset');
  });

  /* Cargar foto de perfil */
  $('#formFile').change(function (e) {
    e.preventDefault();
    logo.src = URL.createObjectURL(event.target.files[0]);
  });

  /* Cerrar Modal*/
  $('#btnCloseCompany').click(function (e) {
    e.preventDefault();
    $('#createCompany').modal('hide');
  });

  /* Crear Empresa */
  $('#btnCreateCompany').click(function (e) {
    e.preventDefault();

    idCompany = sessionStorage.getItem('id_company');
    if (!idCompany || idCompany == '') {
      company = $('#company').val();
      companyNIT = $('#companyNIT').val();
      companyCity = $('#companyCity').val();
      companyState = $('#companyState').val();
      companyCountry = $('#companyCountry').val();
      companyAddress = $('#companyAddress').val();
      companyTel = $('#companyTel').val();

      if (
        company === '' ||
        companyNIT === '' ||
        companyCity === '' ||
        companyState === '' ||
        companyCountry === '' ||
        companyAddress === '' ||
        companyTel === ''
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      dataCompany = new FormData(document.getElementById('formCreateCompany'));
      // dataCompany.append('companyStatus', companyStatus);
      let logo = $('#formFile')[0].files[0];
      dataCompany.append('logo', logo);

      $.ajax({
        type: 'POST',
        url: '/api/addNewCompany',
        data: dataCompany,
        contentType: false,
        cache: false,
        processData: false,

        success: function (resp) {
          message(resp);
        },
      });
    } else updateCompany();
  });

  /* Cargar datos en el modal Empresa */
  $(document).on('click', '.updateCompany', function (e) {
    e.preventDefault();
    $('#createCompany').modal('show');
    $('#license').hide();
    $('#btnCreateCompany').html('Actualizar');

    idCompany = this.id;
    sessionStorage.setItem('id_company', idCompany);

    let row = $(this).parent().parent()[0];
    let data = tblCompanies.fnGetData(row);

    $('#company').val(data.company);
    $('#companyNIT').val(data.nit);
    if (data.logo) logo.src = data.logo;

    $('#companyCity').val(data.city);
    $('#companyState').val(data.state);
    $('#companyCountry').val(data.country);
    $('#companyAddress').val(data.address);
    $('#companyTel').val(data.telephone);
    $('html, body').animate({ scrollTop: 0 }, 1000);
  });

  updateCompany = () => {
    idCompany = sessionStorage.getItem('id_company');
    logo = $('#formFile')[0].files[0];

    dataCompany = new FormData(formCreateCompany);
    dataCompany.append('idCompany', idCompany);
    dataCompany.append('logo', logo);

    $.ajax({
      type: 'POST',
      url: '/api/updateDataCompany',
      data: dataCompany,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        message(resp);
      },
    });
  };

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      $('#createCompany').modal('hide');
      $('#formCreateCompany').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCompanies').DataTable().clear();
    $('#tblCompanies').DataTable().ajax.reload();
  }
});
