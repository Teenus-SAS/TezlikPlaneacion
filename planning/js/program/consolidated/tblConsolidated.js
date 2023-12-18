$(document).ready(function () {
  fetch(`/api/consolidated`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);

      loadTblConsolidated(data);
    });

  // Cargar tabla consolidados
  loadTblConsolidated = (data) => {
    $('#tblConsolidated').empty();

    if (data.length == 0) {
      $('#tblConsolidated').append(`
      <tbody>
        <tr>
          <th class="text-center" colspan="9">Ningún dato disponible en esta tabla =(</th>
        </tr>
      </tbody>
      `);
    } else {
      titlesOrderType = loadTitlesOrderTypes(data);

      $('#tblConsolidated').append(
        ` <thead>
            <tr class="text-center">
              <th>No.</th>
              <th>Item</th>
              <th>Referencia</th>
              <th>Kardex</th> 
              ${titlesOrderType}
              <th>Total Pedidos</th>
              <th>Promedio Mes</th>
              <th>Dias Inventario</th>
              <th>Stock Minimo x Semanas</th>
              <th>A Producir Ajustado</th>
            </tr>
          </thead>`
      );
      for (i = 0; i < data.length; i++) {
        dataOrderType = loadOrderTypes(data[i]);

        $('#tblConsolidated').append(
          `
        <tbody>
          <tr class="text-center">
            <th>${i + 1}</th>
            <th>${data[i]['num_order']}</th>
            <th>${data[i]['reference']}</th>
            <th>${data[i]['quantity']}</th>
            ${dataOrderType}
            <th>${data[i]['total_orders']}</th>
            <th>${data[i]['average_month']}</th>
            <th>${data[i]['inventory_days']}</th>
            <th>${data[i]['week_minimum_stock']}</th>
            <th>${data[i]['produce_ajusted']}</th>
          </tr>
        </tbody>`
        );
      }
    }
  };

  // Obtener titulos tipos de pedidos
  loadTitlesOrderTypes = (data) => {
    dataOrderType = [];
    j = 0;
    while (data[0][`name_order_type-${j}`]) {
      dataOrderType.push(`<th>${data[0][`name_order_type-${j}`]}</th>`);
      j++;
    }

    return dataOrderType;
  };

  // Obtener datos tipos de pedido
  loadOrderTypes = (data) => {
    dataOrderType = [];
    j = 0;
    while (data[`order_type-${j}`] >= 0) {
      dataOrderType.push(`<th>${data[`order_type-${j}`]}</th>`);
      j++;
    }

    return dataOrderType;
  };

  /* Cargar tabla consolidados 
  loadTblConsolidated = (data) => {
    tblConsolidated = $('#tblConsolidated').dataTable({
      pageLength: 50,
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
          title: 'Item',
          data: 'num_order',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Kardex',
          data: 'quantity',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
         {
          title: ,
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: ,
          data: '',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: ,
          data: '',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        }, 
        {
          title: 'Total Pedidos',
          data: 'total_orders',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Promedio Mes',
          data: 'average_month',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Dias Inventario',
          data: 'inventory_days',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Stock Minimo x Semanas',
          data: 'week_minimum_stock',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        // {
        //   title: 'A Producir Con Stock Minimo',
        //   data: 'produce_minimum_stock',
        //   className: 'uniqueClassName dt-head-center',
        //   render: $.fn.dataTable.render.number('.', ',', 0, ''),
        // },
        {
          title: 'A Producir Ajustado',
          data: 'produce_ajusted',
          className: 'uniqueClassName dt-head-center',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
      ],
    });
  }; */
});
