$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    if (this.id == "products") {
      $(".cardProducts").show();
      $(".cardMaterials").hide();
      $(".cardRawMaterials").hide(800);
      $(".cardImportMaterials").hide(800);
      loadTblProduct(products);
      inventoryIndicator(products);
    } else if (this.id == "materials") {
      $(".cardMaterials").show();
      $(".cardProducts").hide();
      $(".cardCreateProduct").hide(800);
      $(".cardImportProducts").hide(800);
      loadTblMaterials(materials);
      inventoryIndicator(materials);
    }

    let tables = document.getElementsByClassName("dataTable");

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = "100%";
      attr = tables[i].firstElementChild;
      attr.style.width = "100%";
    }
  });

  loadAllData = async () => {
    try {
      const [dataProducts, dataMaterials] = await Promise.all([
        searchData('/api/products'),
        searchData(`/api/materials`)
      ]);

      products = dataProducts;
      materials = dataMaterials;

      let card = document.getElementsByClassName('selectNavigation');

      if (card[0].className.includes('active')) {
        loadTblProduct(products);
        inventoryIndicator(products);
      }
      else {
        loadTblMaterials(materials);
        inventoryIndicator(materials);
      }

      
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  const inventoryIndicator = (data) => {
    let totalQuantity = 0;
    let average = 0;
    let concentration = 0;
    let maxQuantity = 0;
    
    totalQuantity = data.reduce((acc, obj) => acc + parseFloat(obj.quantity), 0);
    maxQuantity = Math.max(...data.map(obj => parseFloat(obj.quantity)));
    average = totalQuantity / data.length;
    concentration = maxQuantity / totalQuantity;

    $('#totalQuantity').html(totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 }));
    $('#lblTotal').html(` Inv Total: ${totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblAverage').html(` Promedio: ${average.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblConcentration').html(` Concentracion: ${concentration.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
  }

  /* Cargue tabla de Proyectos */
  loadTblProduct = (data) => {
    if ($.fn.dataTable.isDataTable("#tblProducts")) {
      $("#tblProducts").DataTable().clear();
      $("#tblProducts").DataTable().rows.add(data).draw();
      return;
    }

    tblProducts = $('#tblProducts').dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName  dt-head-center',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Existencia',
          data: 'quantity',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            let quantity = parseFloat(data);
            if (Math.abs(quantity) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              quantity = quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              quantity = quantity.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return quantity;
          },
        },
        {
          title: 'Medida',
          data: 'abbreviation',
          className: 'uniqueClassName dt-head-center',
        },
        /* {
          title: 'Reservado',
          data: 'reserved',
          className: 'uniqueClassName', 
        },  */
        {
          title: 'Stock Min',
          data: 'minimum_stock',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            let minimum_stock = parseFloat(data);
            if (Math.abs(minimum_stock) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              minimum_stock = minimum_stock.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              minimum_stock = minimum_stock.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return minimum_stock;
          },
        },
        {
          title: "Dias Inv",
          data: "days",
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let days = parseFloat(data);
            if (Math.abs(days) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              days = days.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              days = days.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return days;
          },
        },
      
        {
          title: 'Img',
          data: 'img',
          className: 'uniqueClassName dt-head-center',
          render: (data, type, row) => {
            data ? img = `<img src="${data}" alt="" style="width:80px;border-radius:100px">` : (img = '');
            return img;
          },
        },
        {
          title: 'Acciones',
          data: 'id_product',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProductsFunction()"></i></a>`;
          },
        },
      ],
    });
  }

  loadAllData();
});
