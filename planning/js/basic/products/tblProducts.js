$(document).ready(function () {
  products = [];
  materials = [];

  $(".selectNavigation").click(function (e) {
    e.preventDefault();
    let option = this.id;

    switch (option) {
      case 'sProducts':
        $(".cardProducts").show();
        $(".cardMaterials").hide();
        $(".cardRawMaterials").hide(800);
        $(".cardImportMaterials").hide(800);
        loadTblProduct(products);
        inventoryIndicator(products);
        break;
      case 'sMaterials':
        $(".cardMaterials").show();
        $(".cardProducts").hide();
        $(".cardCreateProduct").hide(800);
        $(".cardImportProducts").hide(800);
        loadTblMaterials(materials);
        inventoryIndicator(materials);
        break;
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
        searchData('/api/materials')
      ]);

      products = dataProducts;
      materials = dataMaterials;

      const card = document.querySelector('.selectNavigation');

      if (card.classList.contains('active')) {
        loadTblProduct(products);
        inventoryIndicator(products);
      } else {
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
          render: (data) => formatNumber(data, 'es-CO'),
        },
        {
          title: 'Stock Min',
          data: 'minimum_stock',
          className: 'uniqueClassName dt-head-center',
          render: (data) => formatNumber(data, 'es-CO'),
        },
        {
          title: "Dias Inv",
          data: "days",
          className: "uniqueClassName dt-head-center",
          render: (data) => formatNumber(data, 'es-CO'),
        },
        {
          title: 'Img',
          data: 'img',
          className: 'uniqueClassName dt-head-center',
          render: (data) => data ? `<img src="${data}" alt="" style="width:80px;border-radius:100px">` : '',
        },
        {
          title: 'Acciones',
          data: 'id_product',
          className: 'uniqueClassName dt-head-center',
          render: (data) => `
          <a href="javascript:;">
            <i id="upd-${data}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i>
          </a>
          <a href="javascript:;">
            <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProductsFunction()"></i>
          </a>
        `,
        },
      ],
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
