$(document).ready(function () {
  /* Ocultar panel crear producto */

  $(".cardCreateCategory").hide();

  /* Abrir panel crear categoria */

  $("#btnNewCategory").click(function (e) {
    e.preventDefault();

    $(".cardImportCategories").hide(800);
    $(".cardCreateCategory").toggle(800);
    $("#btnCreateCategory").text("Crear");

    sessionStorage.removeItem("id_category");

    $("#formCreateCategory").trigger("reset");
  });

  /* Crear nuevo categoria */

  $("#btnCreateCategory").click(function (e) {
    e.preventDefault();

    let idCategory = sessionStorage.getItem("id_category");

    if (!idCategory) {
      category = $("#category").val();
      type = $("#typeCategory").val();

      if (!category) {
        toastr.error("Ingrese todos los campos");
        return false;
      }

      category = $("#formCreateCategory").serialize();

      $.post(
        "../../api/addCategory",
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

  $(document).on("click", ".updateCategory", function (e) {
    $(".btnImportNewCategories").hide(800);
    $(".cardCreateCategory").show(800);
    $("#btnCreateCategory").text("Actualizar");

    //obtener data
    let row = $(this).closest("tr")[0];
    let data = tblCategories.fnGetData(row);

    sessionStorage.setItem("id_category", data.id_category);
    $("#category").val(data.category);
    $(`#typeCategory option:contains(${data.type_category})`).prop(
      "selected",
      true
    );

    //Animacion desplazamiento
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateCategory = () => {
    let data = $("#formCreateCategory").serialize();
    idCategory = sessionStorage.getItem("id_category");
    data = data + "&idCategory=" + idCategory;

    $.post(
      "../../api/updateCategory",
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar categoria */

  deleteFunction = () => {
    //obtener data
    let row = $(this.activeElement).closest("tr")[0];
    let data = tblCategories.fnGetData(row);

    let id_category = data.id_category;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este categoria? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
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
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreateCategory").hide(800);
      $("#formCreateCategory").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblCategories").DataTable().clear();
    $("#tblCategories").DataTable().ajax.reload();
  }
});
