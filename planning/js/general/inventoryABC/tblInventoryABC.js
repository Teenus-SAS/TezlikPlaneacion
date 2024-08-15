$(document).ready(function () {
  loadAllData = async () => {
    let data = await searchData('/api/inventoryABC');

    if (data.length == 0) {
      let dataInventory = new FormData();

      dataInventory.append('a', 0);
      dataInventory.append('b', 0);
      dataInventory.append('c', 0);
      await sendDataPOST('/api/addInventoryABC', dataInventory);
    }

    sessionStorage.setItem('dataInventoryABC', JSON.stringify(data));

    if (data.length == 0) {
      $('#btnNewInventoryABC').show();
    } else {
      $('#a').val(data[0].a);
      $('#b').val(data[0].b);
      $('#c').val(data[0].c);
    }
    // loadTblInventoryABC(data);
  };

  // visible = inventory_abc == '1' ? true : false;

  /* Cargue tabla de Categorias 
  const loadTblInventoryABC = (data) => {
    tblInventoryABC = $('#tblInventoryABC').dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'A',
          data: 'a',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `${data} %`;
          }
        },
        {
          title: 'B',
          data: 'b',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `${data} %`;
          }
        },
        {
          title: 'C',
          data: 'c',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `${data} %`;
          }
        },
        {
          title: 'Acciones',
          data: 'id_inventory',
          // visible: visible,
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateInventory" data-toggle='tooltip' title='Actualizar Inventario' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
  }; */

  loadAllData();
});
