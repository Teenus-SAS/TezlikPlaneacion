$(document).ready(function () {
  loadClients(1);

  loadAllData = async () => {
    try {
      const dataOrders = await searchData('/api/orders');

      orders = dataOrders;

      ordersIndicators(orders);
      loadTblOrders(orders);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  const ordersIndicators = (data) => {
    let totalQuantity = 0;
    let completed = 0;
    let late = 0;
    let today = formatDate(new Date());

    if (data.length > 0) {
      let arrCompleted = data.filter(item => item.status == 'Despacho' && item.min_date < item.office_date);
      let arrLate = data.filter(item => item.min_date < today);

      totalQuantity = data.length;
      completed = (arrCompleted.length / totalQuantity) * 100;
      late = (arrLate.length / totalQuantity) * 100;
    }

    $('#lblTotal').html(` Total: ${totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblCompleted').html(` Completados: ${completed.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
    $('#lblLate').html(` Atrasados: ${late.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
  }

  /* Cargar pedidos */
  loadTblOrders = (data) => {
    tblOrder = $('#tblOrder').dataTable({
      destroy: true,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
          title: 'Fecha',
          data: 'date_order',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Pedido',
          data: 'num_order',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Cliente',
          data: 'client',
          className: 'uniqueuniqueClassName dt-head-center',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Cantidad',
          data: 'original_quantity',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'F.Maxima',
          data: 'max_date',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Estado',
          data: 'status',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            if (data == 'Entregado')
              badge = 'badge-success';
            else if (data == 'Sin Ficha Tecnica' || data == 'Sin Materia Prima')
              badge = 'badge-danger';
            else if (data == 'Despacho')
              badge = 'badge-info';
            else
              badge = 'badge-light';
            
            return `<span class="badge ${badge}">${data}</span>`
          }
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            !data.delivery_date && data.status == 'Programar' ? action = `<a href="javascript:;" <i class="bx bx-edit-alt updateOrder" id="${data.id_order}" data-toggle='tooltip' title='Actualizar Pedido' style="font-size: 30px;"></i></a><a href="javascript:;" <i class="mdi mdi-delete-forever" id="${data.id_order}" data-toggle='tooltip' title='Eliminar Pedido' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`
              : action = '';

            return action;
          },
        },
      ],
      // rowCallback: function (row, data, index) {
      //   if (data.status == 'Sin Ficha Tecnica' || data.status == 'Sin Materia Prima') $(row).css('color', 'red');
      // },
    });
  }

  loadAllData();
});
