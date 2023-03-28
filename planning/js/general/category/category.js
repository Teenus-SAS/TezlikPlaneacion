$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardCreateCategory').hide();

  /* Abrir panel crear categoria */

  $('#btnNewCategory').click(function (e) {
    e.preventDefault();

    $('.cardImportCategories').hide(800);
    $('.cardCreateCategory').toggle(800);
    $('#btnCreateCategory').html('Crear');

    sessionStorage.removeItem('id_category');

    $('#formCreateCategory').trigger('reset');
  });

  /* Crear nuevo categoria */

  $('#btnCreateCategory').click(function (e) {
    e.preventDefault();

    let idCategory = sessionStorage.getItem('id_category');

    if (idCategory == '' || idCategory == null) {
      category = $('#category').val();
      type = $('#typeCategory').val();

      if (category == '' || type == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      category = $('#formCreateCategory').serialize();

      $.post(
        '../../api/addCategory',
        category,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateCategory();
    }
  });

  /* Actualizar categoria */

  $(document).on('click', '.updateCategory', function (e) {
    $('.btnImportNewCategories').hide(800);
    $('.cardCreateCategory').show(800);
    $('#btnCreateCategory').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblCategories.fnGetData(row);

    sessionStorage.setItem('id_category', data.id_category);
    $('#category').val(data.category);
    $(`#typeCategory option:contains(${data.type_category})`).prop(
      'selected',
      true
    );
    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateCategory = () => {
    let data = $('#formCreateCategory').serialize();
    idCategory = sessionStorage.getItem('id_category');
    data = data + '&idCategory=' + idCategory;

    $.post(
      '../../api/updateCategory',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar categoria */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblCategories.fnGetData(row);

    let id_category = data.id_category;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este categoria? Esta acción no se puede reversar.',
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          $.get(
            `../../api/deleteCategory/${id_category}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateCategory').hide(800);
      $('#formCreateCategory').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCategories').DataTable().clear();
    $('#tblCategories').DataTable().ajax.reload();
  }
});
