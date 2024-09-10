$(document).ready(function () {
  $(".cardImport").hide();

  $("#btnImportProduct").on("click", function () {
    $(".cardAddNewProduct").hide(800);
    $(".cardAddMaterials").hide(800);
    $(".cardImport").toggle(800);
    $(".cardAddNewProduct").hide();
  });

  $("#btnImportProductsMaterials").click(function (e) {
    e.preventDefault();

    const fileInput = document.getElementById("fileProductsMaterials");
    const selectedFile = fileInput.files[0];

    if (!fileProductsMaterials) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    $(".cardBottons").hide();

    let form = document.getElementById("formProductMaterial");
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
        const expectedHeaders = ['referencia_producto', 'producto', 'referencia_material', 'material', 'tipo_material', 'magnitud', 'unidad', 'cantidad', 'tipo'];
        const actualHeaders = Object.keys(data[0]);

        if (flag_products_measure == '0') {
          expectedHeaders.splice(4, 1);
        }

        const missingHeaders = expectedHeaders.filter(
          (header) => !actualHeaders.includes(header)
        );

        if (missingHeaders.length > 0) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileProductsMaterials").val("");
          toastr.error(
            "Archivo no corresponde con el formato. Verifique nuevamente"
          );
          return false;
        }

        let dataToImport = data.map((item) => {
          !item.referencia_producto ? item.referencia_producto = '' : item.referencia_producto;
          !item.producto ? item.producto = '' : item.producto;
          !item.referencia_material ? item.referencia_material = '' : item.referencia_material;
          !item.material ? item.material = '' : item.material;
          !item.magnitud ? item.magnitud = '' : item.magnitud;
          !item.unidad ? item.unidad = '' : item.unidad;
          !item.cantidad ? item.cantidad = 0 : item.cantidad;
          !item.tipo ? item.tipo = '' : item.tipo;

          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            materialType: item.tipo_material,
            magnitude: item.magnitud,
            unit: item.unidad,
            quantity: item.cantidad,
            type: item.tipo,
          };
        });

        checkDataPM(dataToImport);
      })
      .catch(() => {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProductsMaterials").val("");

        toastr.error("Ocurrio un error. Intente Nuevamente");
      });
  });

  /* Mensaje de advertencia */
  const checkDataPM = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/productsMaterialsDataValidation",
      data: { importProducts: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileProductsMaterials").val("");

          $("#formImportProductMaterial").trigger("reset");
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileProductsMaterials").val("");

          // Generar el HTML para cada mensaje
          let concatenatedMessages = resp.debugg
            .map(
              (item) =>
                `<li>
              <span class="badge text-danger" style="font-size: 16px;">${item.message}</span>
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
            message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
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
                saveProduct(data);
              } else {
                $(".cardLoading").remove();
                $(".cardBottons").show(400);
                $("#fileProductsMaterials").val("");
              }
            },
          });
        }
      },
    });
  };

  const saveProduct = (data) => {
    // console.log(data);
    $.ajax({
      type: "POST",
      url: "/api/addProductsMaterials",
      data: { importProducts: data },
      success: function (r) {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProductsMaterials").val("");

        messageMaterial(r);
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsProductsMaterials").click(function (e) {
    e.preventDefault();
    let link = document.createElement('a');
    link.target = '_blank';

    link.href = 'assets/formatsXlsx/Productos_Materias.xlsx';
    document.body.appendChild(link);
    link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(link.href); // liberar memoria
      })
      .catch(console.error);
  });
});
