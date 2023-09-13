/* Función mover filas

let shadow;
function dragit(event) {
  shadow = event.target;
}
function dragover(e) {
  let children = Array.from(e.target.parentNode.parentNode.children);
  if (children.indexOf(e.target.parentNode) > children.indexOf(shadow))
    e.target.parentNode.after(shadow);
  else e.target.parentNode.before(shadow);
}*/
$(document).ready(function () {
  sessionStorage.removeItem('id_programming'); 
  
  $('#searchMachine').change(function (e) { 
    e.preventDefault();
    
    loadTblProgramming(this.value);
  });
  
  loadTblProgramming = async (machine) => { 
    let data;

    if (machine == 0) {
      data = await searchData('/api/programming');
    }
    else
      data = await searchData(`/api/programmingByMachine/${machine}`);
      
    tblProgramming = $('#tblProgramming').dataTable({
      destroy: true,
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
          title: 'Pedido',
          data: 'num_order',
          className: 'uniqueClassName',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName',
        },
        {
          title: 'Maquina',
          data: 'machine',
          className: 'uniqueClassName',
        },
        {
          title: 'Cant.Pedido',
          data: 'quantity_order',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 2, ''),
        },
        {
          title: 'Cant.Fabricar',
          data: 'quantity_programming',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 2, ''),
        },
        {
          title: 'Cant.Pendiente',
          data: 'accumulated_quantity',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 2, ''),
        },
        {
          title: 'Cliente',
          data: 'client',
          className: 'uniqueClassName',
        },
        // {
        //   title: 'Lote Economico',
        //   data: 'process',
        //   className: 'uniqueClassName',
        // },
        {
          title: 'Fecha Inicio',
          data: null,
          className: 'uniqueClassName',
          render: function (data) { 
            let min_date = getFirstText(data.min_date);
            let hourStart = moment(data.min_hour, ['HH:mm']).format('h:mm A');

            return `<p>${min_date}</p><p>${hourStart}</p>`;
          },
        }, 
        {
          title: 'Fecha Final',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            let max_date = getFirstText(data.min_date);

            let hourStart = moment(data.max_hour, ['HH:mm']).format('h:mm A');
            return `<p>${max_date}</p><p>${hourStart}</p>`;
          },
        }, 
        {
          title: 'Acciones',
          data: 'id_programming',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
    });
  };

  loadTblProgramming(0);
});
