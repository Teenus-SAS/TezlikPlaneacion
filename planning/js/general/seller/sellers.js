$(document).ready(function () {
  /* Ocultar panel crear vendedor */
  $(".cardCreateSeller").hide();

  /* Abrir panel crear vendedor */

  $("#btnNewSeller").click(function (e) {
    e.preventDefault();

    $(".cardImportSeller").hide(800);
    $(".cardCreateSeller").show(800);

    $("#btnCreateSeller").text("Crear");

    sessionStorage.removeItem("id_seller");

    $("#formCreateSeller").trigger("reset");
  });

  /* Crear nuevo vendedor */
  $("#btnCreateSeller").click(function (e) {
    e.preventDefault();

    let idSeller = sessionStorage.getItem("id_seller");

    if (idSeller == "" || idSeller == null) {
      checkDataSellers("/api/addSeller", idSeller);
    } else {
      checkDataSellers("/api/updateSeller", idSeller);
    }
  });

  /* Actualizar vendedores */
  $(document).on("click", ".updateSeller", function (e) {
    $(".cardImportSeller").hide(800);
    $("#btnCreateSeller").text("Actualizar");

    const row = $(this).closest("tr")[0];
    let data = tblSellers.fnGetData(row);

    sessionStorage.setItem("id_seller", data.id_seller);

    $("#firstname").val(data.firstname);
    $("#lastname").val(data.lastname);
    $("#email").val(data.email);

    $(".cardCreateSeller").show(800);
    $("#btnCreateSeller").text("Actualizar");

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataSellers = async (url, idSeller) => {
    let firstname = $("#firstname").val();
    let lastname = $("#lastname").val();
    let email = $("#email").val();

    if (firstname == "" || lastname == "" || email == "") {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let avatarSeller = $("#formFile")[0].files[0];

    let seller = new FormData(formCreateSeller);
    seller.append("avatar", avatarSeller);

    if (idSeller) {
      seller.append("idSeller", idSeller);
    }

    let resp = await sendDataPOST(url, seller);

    message(resp);
  };

  /* Eliminar vendedor */
  deleteFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblSellers.fnGetData(row);

    let id_seller = data.id_seller;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este vendedor? Esta acción no se puede reversar.",
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
            `/api/deleteSeller/${id_seller}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Camniar vendedor interno */
  $(document).on("click", ".checkSeller", function () {
    let id = this.id;

    let id_seller = id.slice(6, id.length);
    if ($(`#${id}`).is(":checked")) {
      bootbox.confirm({
        title: "Vendedor Interno",
        message:
          "Está seguro de cambiar este vendedor interno? Esta acción no se puede reversar.",
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
              `/api/changeStatusSeller/${id_seller}`,
              function (data, textStatus, jqXHR) {
                message(data);
              }
            );
          } else {
            $(`#${id}`).prop("checked", false);
          }
        },
      });
    } else {
      toastr.error("Debe haber por lo menos un vendedor interno");
      return false;
    }
  });

  /* Mensaje de exito */

  message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreateSeller, .cardImportSellers").hide(800);
      $("#formImportSellers, #formCreateSeller").trigger("reset");

      loadAllDataSellers();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
