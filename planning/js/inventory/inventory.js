$(document).ready(function () {
  // Ocultar card formulario Analisis Inventario ABC
  $(".cardAddMonths").hide();

  // Ocultar botón analisar Inventario ABC
  $(".cardBtnAddMonths").hide();

  $("#btnInvetoryABC").click(function (e) {
    e.preventDefault();
    $(".cardImportInventory").hide(800);
    $(".cardInventoryABC").hide(800);
    $(".cardAddMonths").toggle(800);

    let display = $(".cardAddMonths").css("display");

    if (display == "none") {
      // $('.cardTblInventoryABC').show(800);
      // $('.cardTblInventoryABC').hide(800);
      $(".cardInventoryABC").hide(800);
    }

    // $('#formAddMonths').trigger('reset');
  });

  $("#btnAddMonths").click(function (e) {
    e.preventDefault();

    cantMonths = $("#cantMonths").val();

    if (!cantMonths || cantMonths == "") {
      toastr.error("Ingrese cantidad a calcular");
      return false;
    }

    $.get(
      `/api/classification/${cantMonths}`,
      function (data, textStatus, jqXHR) {
        messageInventory(data);
      }
    );
  });

  /* Mensaje de exito */
  messageInventory = async (data) => {
    const { success, error, info, message } = data;
    if (success) {
      $(".cardImportInventory").hide(800);
      $(".cardAddMonths").hide(800);

      await loadInventory();

      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
