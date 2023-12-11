$(document).ready(function () {
  /* Seleccion producto */

  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadtableMaterials(id); 
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadtableMaterials(id); 
    // loadTableProcess(id)
  });

  /* Cargue tabla de Productos Materiales */

  const loadtableMaterials = (idProduct) => { 
    tblConfigMaterials = $('#tblConfigMaterials').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsMaterials/${idProduct}`,
        dataSrc: '',
      },
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
          title: 'Materia Prima',
          data: 'material',
          className: 'classCenter',
        },
        {
          title: 'Unidad',
          data: 'abbreviation',
          className: 'classCenter',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 4, ''),
        },
        {
          title: 'Acciones',
          data: 'id_product_material',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
    });
  };
  /* Cargue tabla de Productos en proceso 

  const loadTableProcess = (idProduct) => {
    $('.cardTableProductsInProcess').show(800);

    tblProductsInProcess = $('#tblProductsInProcess').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsInProcessByCompany/${idProduct}`,
        dataSrc: '',
      },
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
          title: 'Producto',
          data: 'product',
          className: 'classCenter',
        },
        {
          title: 'Acciones',
          data: 'id_product_category',
          className: 'uniqueClassName',
          render: function (data) {
            return `
              <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProduct()"></i></a>`;
          },
        },
      ],
    });
  }; */
});
