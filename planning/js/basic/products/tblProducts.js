$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();
    const option = this.id;

    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
    let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

    const cards = {
      'sProducts': {
        show: [".cardProducts"],
        hide: [".cardMaterials", ".cardRawMaterials", ".cardImportMaterials"],
        load: loadTblProduct,
        data: dataProducts
      },
      'sMaterials': {
        show: [".cardMaterials"],
        hide: [".cardProducts", ".cardCreateProduct", ".cardImportProducts"],
        load: loadTblMaterials,
        data: dataMaterials
      }
    };

    if (cards[option]) {
      const { show, hide, load, data } = cards[option];

      show.forEach(selector => $(selector).show());
      hide.forEach(selector => $(selector).hide());

      load(data);
      inventoryIndicator(data);
    }

    Array.from(document.getElementsByClassName("dataTable")).forEach(table => {
      table.style.width = "100%";
      table.firstElementChild.style.width = "100%";
    });
  });

  loadAllData = async () => {
    try {
      const [dataProducts, dataMaterials] = await Promise.all([
        searchData('/api/products'),
        searchData('/api/materials')
      ]); 

      let products = dataProducts;

      sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));
      sessionStorage.setItem('dataMaterials', JSON.stringify(dataMaterials));

      if (flag_products_measure == '1') {
        products = products.filter(item => item.id_product_inventory != 0); 
      }

      const card = document.querySelector('.selectNavigation');

      if (card.classList.contains('active')) {
        loadTblProduct(products);
        inventoryIndicator(products);
      } else {
        loadTblMaterials(dataMaterials);
        inventoryIndicator(dataMaterials);
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

    // $('#totalQuantity').html(totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 }));
    $('#lblTotal').html(` Inv Total: ${totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblAverage').html(` Promedio: ${average.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblConcentration').html(` Concentracion: ${concentration.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
  }

  /* Cargue tabla de Proyectos */
  const loadTblProduct = (data) => {
    const tblProductsElement = $("#tblProducts");

    if ($.fn.dataTable.isDataTable("#tblProducts")) {
      const dataTable = tblProductsElement.DataTable();
      dataTable.clear();
      dataTable.rows.add(data).draw();
      return;
    }

    tblProducts = tblProductsElement.dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
      fnInfoCallback: (oSettings, iStart, iEnd, iMax, iTotal, sPre) => {
        if (oSettings.json && oSettings.json.error) {
          console.error(oSettings.json.error);
        }
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: (data, type, full, meta) => meta.row + 1,
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
          title: 'Medida',
          data: 'abbreviation',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Existencia',
          data: 'quantity',
          className: 'uniqueClassName dt-head-center',
          render: (data) => parseFloat(data).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
        },
        {
          title: 'Stock Min',
          data: 'minimum_stock',
          className: 'uniqueClassName dt-head-center',
          render: (data) => parseFloat(data).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
        },
        {
          title: "Dias Inv",
          data: "days",
          className: "uniqueClassName dt-head-center",
          render: (data) => parseFloat(data).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
        },
        {
          title: 'Img',
          data: 'img',
          className: 'uniqueClassName dt-head-center',
          render: (data) => data ? `<img src="${data}" alt="" style="width:80px;border-radius:100px">` : '',
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: (data) => `
          
          <a href="javascript:;">
            <i id="upd-${data.id_product}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i>
          </a>
          <a href="javascript:;">
            <i id="${data.id_product}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProductsFunction()"></i>
          </a>
        `,
        }, /* <a href="javascript:;">
            <i id="${data.id_product}" class="${data.composite == 0 ? 'bi bi-plus-square-fill' : 'bi bi-dash-square-fill'} composite" data-toggle='tooltip' title='${data.composite == 0 ? 'Agregar' : 'Eliminar'} a producto compuesto' style="font-size:25px; color: #3e382c;"></i>
          </a>*/
      ],
      footerCallback: function (row, data, start, end, display) {
        let quantity = 0; 
        let minimum_stock = 0; 
        let days = 0; 

        for (i = 0; i < display.length; i++) {
          quantity += parseFloat(data[display[i]].quantity); 
          minimum_stock += parseFloat(data[display[i]].minimum_stock); 
          days += parseFloat(data[display[i]].days); 
        }

        $(this.api().column(4).footer()).html(
          quantity.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        ); 
        $(this.api().column(5).footer()).html(
          minimum_stock.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        ); 
        $(this.api().column(6).footer()).html(
          days.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        ); 
      },
    });
  };

  const formatNumber = (num, locale) => {
    const value = parseFloat(num);
    if (Math.abs(value) < 0.01) {
      return value.toLocaleString(locale, { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    }
    return value.toLocaleString(locale, { maximumFractionDigits: 2 });
  };

  loadAllData();
});
