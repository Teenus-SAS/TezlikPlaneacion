$(document).ready(function () {
  /*Ocualtar panel de actualización*/

  $('.cardCreateLicense').hide();

  $('#newCompanyLicense').click(function (e) {
    e.preventDefault();
    $('#company').prop('disabled', false);
    sessionStorage.removeItem('id_company');
    $('#formAddLicense').trigger('reset');
    $('.cardCreateLicense').toggle(800);
    $('#btnAddLicense').html('Crear');
  });

  /* Agregar licencia */
  $('#btnAddLicense').click(function (e) {
    e.preventDefault();

    idCompany = sessionStorage.getItem('id_company');
    if (!idCompany || idCompany == null) {
      company = $('#company').val();
      let license_start = $('#license_start').val();
      let license_end = $('#license_end').val();
      let quantityUsers = $('#quantityUsers').val();
      let plan = $('#plan').val();

      data = company * quantityUsers * plan;

      if (license_start == '' || license_end == '' || !data || data == null) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      if (license_start > license_end) {
        toastr.error('La fecha inicial no debe ser mayor a la final');
        return false;
      }

      license = $('#formAddLicense').serialize();

      $.post('/api/addLicense', license, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else updateCompany();
  });

  /*Actualizar licencia*/
  $(document).on('click', '.updateLicenses', function (e) {
    e.preventDefault();
    $('.cardCreateLicense').show(800);
    $('#formAddLicense').trigger('reset');
    let row = $(this).parent().parent()[0];
    $('#btnAddLicense').html('Actualizar');
    let data = tblCompaniesLic.fnGetData(row);

    sessionStorage.setItem('id_company', data.id_company);

    $(`#company option[value=${data.id_company}]`).prop('selected', true);
    $('#license_start').val(data.license_start);
    $('#license_end').val(data.license_end);
    $('#quantityUsers').val(data.quantity_user);
    $(`#plan option[value=${data.plan}]`).prop('selected', true);

    $('#company').prop('disabled', true);
    $('html, body').animate({ scrollTop: 0 }, 1000);
  });

  updateCompany = () => {
    idCompany = sessionStorage.getItem('id_company');

    $('#company').prop('disabled', false);

    dataCompany = $('#formAddLicense').serialize();

    $.post('/api/updateLicense', dataCompany, function (data) {
      message(data);
    });
  };

  /* Cambiar Estado Licencia */

  $(document).on('click', '.licenseStatus', function (e) {
    e.preventDefault();
    id_company = this.id;

    $.ajax({
      type: 'POST',
      url: `/api/changeStatusCompany/${id_company}`,
      success: function (resp) {
        if (resp.success == true) {
          updateTable();
          toastr.success(resp.message);
          return false;
        } else if (resp.error == true) toastr.error(resp.message);
        else if (resp.info == true) toastr.info(resp.message);
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateLicense').hide(800);
      $('#formAddLicense').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCompaniesLicense').DataTable().clear();
    $('#tblCompaniesLicense').DataTable().ajax.reload();
  }
});
