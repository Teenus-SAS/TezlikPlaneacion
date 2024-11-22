$(document).ready(function () {
  let selectedFile;

  $(".cardImportPMeasure").hide();

  $("#btnImportNewPMeasure").click(function (e) {
    e.preventDefault();
    $(".cardCreatePMeasure").hide(800);
    $(".cardImportPMeasure").toggle(800);
  });

  $("#fileProducts").change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $("#btnImportProducts").click(function (e) {
    e.preventDefault();
    file = $("#fileProducts").val();

    if (!file) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    $(".cardBottons").hide();

    let form = document.getElementById("formProducts");
    form.insertAdjacentHTML(
      "beforeend",
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-grow text-dark" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        let expectedHeaders = [
          "referencia_producto",
          "producto",
          "tipo_producto",
          "ancho",
          "alto",
          "largo",
          "largo_util",
          "ancho_total",
          "ventanilla",
          "tinta",
          "origen",
          "compuesto",
        ];

        if (flag_products_measure == "0") {
          expectedHeaders = [
            "referencia_producto",
            "producto",
            "origen",
            "compuesto",
          ];
        }

        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(
          (header) => !actualHeaders.includes(header)
        );

        if (missingHeaders.length > 0) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileProducts").val("");
          toastr.error(
            "Archivo no corresponde con el formato. Verifique nuevamente"
          );
          return false;
        }

        let productsToImport = data.map((item) => {
          let width = 0;
          let high = 0;
          let length = 0;
          let usefulLength = 0;
          let totalWidth = 0;
          let window = 0;
          let inks = 0;
          let origin = '';
          
          !item.referencia_producto ? item.referencia_producto = '' : item.referencia_producto;
          !item.producto ? item.producto = '' : item.producto;
          !item.tipo_producto ? item.tipo_producto = '' : item.tipo_producto;
          !item.ancho ? item.ancho = 0 : item.ancho;
          !item.alto ? item.alto = 0 : item.alto;
          !item.largo ? item.largo = 0 : item.largo;
          !item.largo_util ? item.largo_util =0 : item.largo_util;
          !item.ancho_total ? item.ancho_total = 0 : item.ancho_total;
          !item.ventanilla ? item.ventanilla = 0 : item.ventanilla;
          !item.tinta ? item.tinta = 0 : item.tinta;
          !item.origen ? item.origen = 0 : item.origen;
          !item.compuesto ? item.compuesto = '' : item.compuesto;

          item.ancho ? width = item.ancho : width;
          item.alto ? high = item.alto : high;
          item.largo ? length = item.largo : length;
          item.largo_util ? usefulLength = item.largo_util : usefulLength;
          item.ancho_total ? totalWidth = item.ancho_total : totalWidth;
          item.ventanilla ? window = item.ventanilla : window;
          item.tinta ? inks = item.tinta : inks;
          // item.origen ? origin = '' : origin;
          
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            productType: item.tipo_producto,
            width: width,
            high: high,
            length: length,
            usefulLength: usefulLength,
            totalWidth: totalWidth,
            window: window,
            inks: inks,
            origin: item.origen,
            composite: item.compuesto,
          };
        });
        checkProduct(productsToImport);
      })
      .catch(() => {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProducts").val("");
        toastr.error("Ocurrio un error. Intente Nuevamente");
      });
  });

  /* Mensaje de advertencia */
  const checkProduct = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/productsMeasuresDataValidation",
      data: { importProducts: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileProducts").val("");
          toastr.error(resp.message);
          $("#formImportProduct").trigger("reset");
          return false;
        }

        if (resp.debugg.length > 0) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileProducts").val("");

          // Generar el HTML para cada mensaje
          let concatenatedMessages = resp.debugg
            .map(
              (item) =>
                `<li>
              <span class="badge badge-danger" style="font-size: 16px;">${item.message}</span>
            </li>`
            )
            .join("");

          // Mostramos el mensaje con Bootbox
          bootbox.alert({
            title: "Estado Importación Data",
            message: `
            <div class="container">
              <div class="col-12">
                <ul>
                  ${concatenatedMessages}
                </ul>
              </div> 
            </div>`,
            size: "large",
            backdrop: true,
          });
          return false;
        }

        if (
          typeof arr === "object" &&
          !Array.isArray(arr) &&
          arr !== null &&
          resp.debugg.length == 0
        ) {
          bootbox.confirm({
            title: "¿Desea continuar con la importación?",
            message: `Se encontraron los siguientes registros:<br><br>Datos a insertar: ${arr.insert}<br>Datos a actualizar: ${arr.update}`,
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
                saveProductTable(data);
              } else {
                $(".cardLoading").remove();
                $(".cardBottons").show(400);
                $("#fileProducts").val("");
              }
            },
          });
        }
      },
    });
  };

  /* Guardar Importacion */
  const saveProductTable = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/addProductMeasure",
      //data: data,
      data: { importProducts: data },
      success: function (r) {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProducts").val("");
        messageProducts(r);
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsProducts").click(async function (e) {
    e.preventDefault();

    if (flag_products_measure == "1")
      url = "assets/formatsXlsx/Medidas_Productos.xlsx";
    else url = "assets/formatsXlsx/Productos_No_Medidas.xlsx";

    let newFileName = "Productos.xlsx";

    fetch(url)
      .then((response) => response.blob())
      .then((blob) => {
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = newFileName;

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(link.href); // liberar memoria
      })
      .catch(console.error);
  });
});
