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
  sessionStorage.removeItem('opProgramming');
  sessionStorage.removeItem('minDate');

  loadTblProgramming = async () => {
    let data = await searchData('/api/programming');

    if(data.length > 0)
      sessionStorage.setItem('opProgramming', 1);
      
    tblProgramming = $('#tblProgramming').dataTable({
      destroy:true,
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
          title: 'Cant.Pedido',
          data: 'quantity_order',
          className: 'uniqueClassName',
        },
        {
          title: 'Cant.Fabricar',
          data: 'quantity_programming',
          className: 'uniqueClassName',
        },
        {
          title: 'Cant.Pendiente',
          data: 'accumulated_quantity',
          className: 'uniqueClassName',
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
        // {
        //   title: 'F.Inicio',
        //   data: 'process',
        //   className: 'uniqueClassName',
        // },
        // {
        //   title: 'F.Final',
        //   data: 'process',
        //   className: 'uniqueClassName',
        // },
        {
          title: 'Acciones',
          data: 'id_programming',
          className: 'uniqueClassName',
          render: function (data) {
            // <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
            return `
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
    });
  }

  loadTblProgramming();
});
