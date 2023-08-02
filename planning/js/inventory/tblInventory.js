$(document).ready(function () {
  sessionStorage.removeItem('products');
  // Obtener Inventarios
  loadInventory = async () => {
    await fetch(`/api/inventory`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        // Guardar productos
        dataProducts = JSON.stringify(data.products); 
        sessionStorage.setItem('dataProducts', dataProducts);

        let $select = $('#category');
        $select.empty();
        $select.append(`<option disabled selected>Seleccionar</option>`);
        $select.append(`<option value="1">Productos</option>`);
        $select.append(`<option value="2">Materiales</option>`);
        $select.append(`<option value="3">Todos</option>`);
 
        products = data.products;
        materials = data.rawMaterials;
      });
  };

  loadInventory();

  // Seleccionar Categoria
  $('#category').change(function (e) {
    e.preventDefault();

    // Ocultar card formulario Analisis Inventario ABC
    $('.cardAddMonths').hide(800);
    $('.cardBtnAddMonths').hide(800);

    value = this.value;
    if (value != 0) {
      // Productos
      if (value == 1) {
        $('.cardBtnAddMonths').show(800);
        data = getInventory(products);
        data['visible'] = true;
      }
      // Materias Prima
      else if (value == 2) {
        data = getInventory(materials);
        data['visible'] = false;
      }
      // Todos
      else if (value == 3) {
        dataProducts = getInventory(products);
        dataMaterials = getInventory(materials);

        data = dataProducts.concat(
          dataMaterials
        );
        data['visible'] = false;
      }
      loadTable(data);
    }
  });

  getInventory = (data) => {
    let dataInventory = [];
    for (i = 0; i < data.length; i++) {
      data[i].classification
        ? (classification = data[i].classification)
        : (classification = '');

      dataInventory.push({
        reference: data[i].reference,
        description: data[i].descript,
        unit: data[i].unit,
        quantity: data[i].quantity,
        // price: data[i].price,
        classification,
      });
    }

    return dataInventory;
  };

  /* Cargar Tabla Inventarios */
  loadTable = (data) => {
    if ($.fn.dataTable.isDataTable('#tblInventories')) {
      $('#tblInventories').DataTable().destroy();
      $('#tblInventories').empty();
    }

    tblInventories = $('#tblInventories').dataTable({
      pageLength: 50,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Descripción',
          data: 'description',
          className: 'uniqueClassName',
        },
        {
          title: 'Existencia',
          data: 'quantity',
          className: 'uniqueClassName',
        },
        {
          title: 'Unidad',
          data: 'unit',
          className: 'classCenter',
        },
        // {
        //   title: 'Precio',
        //   data: 'price',
        //   className: 'uniqueClassName',
        // },
        {
          title: 'Clasificación',
          data: null,
          className: 'uniqueClassName',
          visible: data['visible'],
          render: function (data) {
            return `<p>${data.classification}</p>`;
          },
        },
      ],
    });
  };
});
