$(document).ready(function () {
  sessionStorage.removeItem("id_programming");

  $("#searchMachine").change(function (e) {
    e.preventDefault();

    loadAllDataProgramming(2, this.value);
  });

  loadTblProgramming = async (data) => {

    if ($.fn.dataTable.isDataTable("#tblProgramming")) {
      $("#tblProgramming").DataTable().destroy();
      $("#tblProgramming").empty();
    }
    // Encabezados de la tabla
    var headers = ['No.', 'Pedido', 'Referencia', 'Producto', 'Maquina', 'Cantidades', 'Cliente', 'Fecha y Hora', 'Orden Produccion', 'Acciones'];
    
    // Obtén la tabla
    var table = document.getElementById('tblProgramming');

    // Crea la fila de encabezados
    var headerRow = table.createTHead().insertRow();
    headers.forEach(function (header) {
      var th = document.createElement('th');
      th.textContent = header;
      headerRow.appendChild(th);
    });
    
    $('#tblProgrammingBody').empty();

    var body = document.getElementById('tblProgrammingBody');

    // Itera sobre los datos y crea filas para cada conjunto de datos
    data.forEach(function (data, index) {
      // Crea una fila con datos
      var dataRow = body.insertRow();
      // Itera sobre los datos y agrega celdas a la fila
      headers.forEach(function (header, columnIndex) {
        var cell = dataRow.insertCell();
        switch (header) { 
          case 'No.':
            cell.textContent = index + 1;
            break;
          case 'Pedido':
            cell.textContent = data.num_order;
            break;
          case 'Referencia':
            cell.textContent = data.reference;
            break;
          case 'Producto':
            cell.textContent = data.product;
            break;
          case 'Maquina':
            cell.textContent = data.machine;
            break;
          case 'Cantidades':
            const quantityOrder = data.quantity_order;
            const quantityProgramming = data.quantity_programming;
            const accumulatedQuantity = data.accumulated_quantity;

            if (accumulatedQuantity > 0)
              cell.innerHTML = `Pedido: ${quantityOrder}<br>Fabricar: ${quantityProgramming}<br><span class="badge badge-danger">Pendiente: ${accumulatedQuantity}</span>`;
            else
              cell.innerHTML = `Pedido: ${quantityOrder}<br>Fabricar: ${quantityProgramming}<br>Pendiente: ${accumulatedQuantity}`;
            
            break;
          case 'Cliente':
            cell.textContent = data.client;
            break;
          case 'Fecha y Hora':
            const minDate = data.min_date;
            // const maxDate = data.max_date;
            const min_programming = data.min_programming;

            let final_date = new Date(minDate);
            let min_date = new Date(minDate);
            final_date.setMinutes(min_date.getMinutes() + Math.floor(min_programming));
            cell.innerHTML = `Inicio: ${moment(minDate).format("DD/MM/YYYY HH:mm A")}<br>Fin: ${moment(final_date).format("DD/MM/YYYY HH:mm A")}`;

            // cell.innerHTML = `Inicio: ${moment(minDate).format("DD/MM/YYYY HH:mm A")}<br>Fin: ${moment(maxDate).format("DD/MM/YYYY HH:mm A")}`;
            break;
          case 'Orden Produccion':
            cell.innerHTML = `<button class="btn btn-warning changeStatus" id="${data.id_programming}" name="${data.id_programming}">Crear OP</button>`;
            break;
          case 'Acciones':
            cell.innerHTML = `
            <a href="javascript:;" <i id="${data.id_programming}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
            <a href="javascript:;" <i id="${index + 1}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction(${data.id_programming})"></i></a>`;
            break;
          default:
            cell.textContent = ''; // Manejar cualquier otro encabezado no especificado
            break;
        }
      });
    });

    $('#tblProgramming').DataTable();

    dragula([document.getElementById('tblProgrammingBody')]).on('drop', function (el, container, source, sibling) {
      // Get the row index of the dropped element
      var rowIndex = $(el).closest('tr').index();

      // If the row was dropped within the same container,
      // move it to the specified position
      if (container === source) {
        var targetIndex = $(el).closest('tbody').find('tr').index(sibling);
        $(el).closest('tr').insertAfter($(container).find('tr')[targetIndex]);
      } else {
        // If the row was dropped into a different container,
        // move it to the first position
        $(el).closest('tr').appendTo($(container));
      }
    });

    // var drake = dragula({
    //   moves: function (el, source, handle, sibling) {
    //     // Permite arrastrar solo las filas utilizando la clase 'drag-handle'
    //     return handle.classList.contains('drag-handle');
    //   },
    //   accepts: function (el, target, source, sibling) {
    //     // Permite soltar solo en el área de la fila de la tabla
    //     return target.tagName === 'tr';
    //   },
    // });

    // // Itera sobre las filas y añade cada una al conjunto arrastrable
    // var rows = table.getElementsByTagName('tr');
    // for (var i = 0; i < rows.length; i++) {
    //   drake.containers.push(rows[i]);
    // }

    // // Configura Dragula para permitir el arrastre de columnas
    // dragula([headerRow], {
    //   moves: function (el, source, handle, sibling) {
    //     return el.tagName === 'th';
    //   },
    //   accepts: function (el, target, source, sibling) {
    //     return target.tagName === 'tr';
    //   },
    //   direction: 'horizontal',
    // });
  };
});
