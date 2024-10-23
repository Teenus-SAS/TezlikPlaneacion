$(document).ready(function () {
  // $(document).on("click", ".move", function (e) {
  //   let row = $(this).parent().parent().parent().parent()[0];
  //   let data = tblRoutes.fnGetData(row);
  //   let allData = tblRoutes.fnGetData();
  //   let type = getLastText(this.className);
  //   let index = parseInt(getFirstText(this.className));

  //   $(this).hide(200);

  //   let form = document.getElementById(`actionRoute-${index}`);
  //   form.insertAdjacentHTML(
  //     "beforeend",
  //     `<div class="spinner-border spinner-border-sm text-secondary" role="status">
  //               <span class="sr-only">Loading...</span>
  //               </div>`
  //   );

  //   let dataRoute = [];

  //   // obtener el nuevo valor de route
  //   if (type == "up") {
  //     dataRoute.push({
  //       idCiclesMachine: data.id_cicles_machine,
  //       route: data.route - 1,
  //     });
  //     dataRoute.push({
  //       idCiclesMachine: allData[index - 1].id_cicles_machine,
  //       route: allData[index - 1].route + 1,
  //     });
  //   } else {
  //     dataRoute.push({
  //       idCiclesMachine: data.id_cicles_machine,
  //       route: data.route + 1,
  //     });
  //     dataRoute.push({
  //       idCiclesMachine: allData[index + 1].id_cicles_machine,
  //       route: allData[index + 1].route - 1,
  //     });
  //   }

  //   $.ajax({
  //     type: "POST",
  //     url: "/api/saveRoute",
  //     data: { data: dataRoute },
  //     success: function (data) {
  //       messageRoutes(data);
  //     },
  //   });
  // });

  $(document).on("click", ".move", function (e) {
    let $this = $(this);
    let row = $this.closest("tr")[0];  // Simplificado para encontrar el ancestro más cercano
    let data = tblRoutes.fnGetData(row);
    let allData = tblRoutes.fnGetData();
    let type = getLastText($this.attr("class"));
    let index = parseInt(getFirstText($this.attr("class")));

    $this.hide(200);

    let form = document.getElementById(`actionRoute-${index}`);
    form.insertAdjacentHTML(
      "beforeend",
      `<div class="spinner-border spinner-border-sm text-secondary" role="status">
          <span class="sr-only">Loading...</span>
        </div>`
    );

    // Obtener el nuevo valor de la ruta
    let dataRoute = [
      {
        idCiclesMachine: data.id_cicles_machine,
        route: data.route + (type === "up" ? -1 : 1),
      },
      {
        idCiclesMachine: allData[index + (type === "up" ? -1 : 1)].id_cicles_machine,
        route: allData[index + (type === "up" ? -1 : 1)].route + (type === "up" ? 1 : -1),
      },
    ];

    // Llamada AJAX optimizada
    $.post("/api/saveRoute", { data: dataRoute }, function (response) {
      messageRoutes(response);
    });
  });

  const messageRoutes = (data) => {
    const { success, error, info, message } = data;
    $(".cardLoading").remove();

    if (success) {
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblRoutes").DataTable().clear();
    $("#tblRoutes").DataTable().ajax.reload();
  }
});
