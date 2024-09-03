$(document).ready(function () {
  /* Abrir modal crear empresa */

  $("#btnNewCompany").click(function (e) {
    e.preventDefault();
    $("#createCompany").modal("show");
    $("#license").show();
    logo.src = "";
    sessionStorage.removeItem("id_company");
    $("#btnCreateCompany").text("Crear");
    $("#formCreateCompany").trigger("reset");
  });

  /* Cargar foto de perfil */
  $("#formFile").change(function (e) {
    e.preventDefault();
    logo.src = URL.createObjectURL(event.target.files[0]);
  });

  /* Cerrar Modal*/
  $("#btnCloseCompany").click(function (e) {
    e.preventDefault();
    $("#createCompany").modal("hide");
  });

  /* Crear Empresa */
  $("#btnCreateCompany").click(function (e) {
    e.preventDefault();

    idCompany = sessionStorage.getItem("id_company");
    if (!idCompany || idCompany == "") {
      company = $("#company").val();
      companyNIT = $("#companyNIT").val();
      companyCity = $("#companyCity").val();
      companyState = $("#companyState").val();
      companyCountry = $("#companyCountry").val();
      companyAddress = $("#companyAddress").val();
      companyTel = $("#companyTel").val();

      if (
        company === "" ||
        companyNIT === "" ||
        companyCity === "" ||
        companyState === "" ||
        companyCountry === "" ||
        companyAddress === "" ||
        companyTel === ""
      ) {
        toastr.error("Ingrese todos los campos");
        return false;
      }

      dataCompany = new FormData(document.getElementById("formCreateCompany"));
      // dataCompany.append('companyStatus', companyStatus);
      let logo = $("#formFile")[0].files[0];
      dataCompany.append("logo", logo);

      $.ajax({
        type: "POST",
        url: "/api/addNewCompany",
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
  $(document).on("click", ".updateCompany", function (e) {
    e.preventDefault();
    $("#createCompany").modal("show");
    $("#license").hide();
    $("#btnCreateCompany").text("Actualizar");

    // Obtener el ID del elemento
    let idCompany = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_company", idCompany);

    //obtener data
    const row = $(this).closest("tr")[0];
    let data = tblCompanies.fnGetData(row);

    $("#company").val(data.company);
    $("#companyNIT").val(data.nit);
    if (data.logo) logo.src = data.logo;

    $("#companyCity").val(data.city);
    $("#companyState").val(data.state);
    $("#companyCountry").val(data.country);
    $("#companyAddress").val(data.address);
    $("#companyTel").val(data.telephone);
    $("html, body").animate({ scrollTop: 0 }, 1000);
  });

  updateCompany = () => {
    idCompany = sessionStorage.getItem("id_company");
    logo = $("#formFile")[0].files[0];

    dataCompany = new FormData(formCreateCompany);
    dataCompany.append("idCompany", idCompany);
    dataCompany.append("logo", logo);

    $.ajax({
      type: "POST",
      url: "/api/updateDataCompany",
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
    const { success, error, info, message } = data;
    if (success) {
      $("#createCompany").modal("hide");
      $("#formCreateCompany").trigger("reset");
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblCompanies").DataTable().clear();
    $("#tblCompanies").DataTable().ajax.reload();
  }
});
