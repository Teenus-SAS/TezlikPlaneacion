$(document).ready(function () {
  /*Ocualtar panel de actualización*/
  $(".cardCreateLicense").hide();

  $("#newCompanyLicense").click(function (e) {
    e.preventDefault();
    $("#company").prop("disabled", false);
    sessionStorage.removeItem("id_company");
    $("#formAddLicense").trigger("reset");
    $(".cardCreateLicense").toggle(800);
    $("#btnAddLicense").text("Crear");
  });

  /* Agregar licencia */
  $("#btnAddLicense").click(function (e) {
    e.preventDefault();

    idCompany = sessionStorage.getItem("id_company");
    if (!idCompany || idCompany == null) {
      checkLicences("/api/addLicense", idCompany);
    } else {
      $("#company").prop("disabled", false);
      checkLicences("/api/updateLicense", idCompany);
    }
  });

  /*Actualizar licencia*/
  $(document).on("click", ".updateLicenses", function (e) {
    e.preventDefault();
    $(".cardCreateLicense").show(800);
    $("#formAddLicense").trigger("reset");
    const row = $(this).closest("tr")[0];
    $("#btnAddLicense").html("Actualizar");
    let data = tblCompaniesLic.fnGetData(row);

    sessionStorage.setItem("id_company", data.id_company);

    $(`#company option[value=${data.id_company}]`).prop("selected", true);
    $("#license_start").val(data.license_start);
    $("#license_end").val(data.license_end);
    $("#quantityUsers").val(data.quantity_user);
    $(`#plan option[value=${data.plan}]`).prop("selected", true);

    data.flag_products_measure == "1"
      ? (productsMeasures = "1")
      : (productsMeasures = "2");
    
    data.flag_type_program == "1"
      ? (typeProgramming = "1")
      : (typeProgramming = "2");

    $(`#productsMeasures option[value=${productsMeasures}]`).prop(
      "selected",
      true
    );
    $(`#typeProgramming option[value=${typeProgramming}]`).prop(
      "selected",
      true
    );

    $("#company").prop("disabled", true);
    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  const checkLicences = async (url, idCompany) => {
    let company = parseFloat($("#company").val());
    let license_start = $("#license_start").val();
    let license_end = $("#license_end").val();
    let quantityUsers = parseFloat($("#quantityUsers").val());
    let plan = parseFloat($("#plan").val());
    let productsMeasures = parseFloat($("#productsMeasures").val());
    let typeProgramming = parseFloat($("#typeProgramming").val());

    data = company * quantityUsers * plan * productsMeasures * typeProgramming;

    if (license_start == "" || license_end == "" || isNaN(data) || data <= 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (license_start > license_end) {
      toastr.error("La fecha inicial no debe ser mayor a la final");
      return false;
    }

    productsMeasures == 1 ? (productsMeasures = 1) : (productsMeasures = 0);
    typeProgramming == 1 ? (typeProgramming = 1) : (typeProgramming = 0);

    let dataCompany = new FormData(formAddLicense);
    dataCompany.append("productsMeasures", productsMeasures);
    dataCompany.append("typeProgramming", typeProgramming);

    if (idCompany != "" || idCompany != null)
      dataCompany.append("idCompany", idCompany);

    let resp = await sendDataPOST(url, dataCompany);

    message(resp);
  };

  /* Cambiar Estado Licencia */
  $(document).on("click", ".licenseStatus", function (e) {
    e.preventDefault();
    // Obtener el ID del elemento
    let id_company = $(this).attr("id").split("-")[1];

    $.ajax({
      type: "POST",
      url: `/api/changeStatusCompany/${id_company}`,
      success: function (resp) {
        message(resp);
      },
    });
  });

  /* Mensaje de exito */
  const message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardCreateLicense").hide(800);
      $("#formAddLicense").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblCompaniesLicense").DataTable().clear();
    $("#tblCompaniesLicense").DataTable().ajax.reload();
  }
});
