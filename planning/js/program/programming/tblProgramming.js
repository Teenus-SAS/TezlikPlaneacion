$(document).ready(function () {
  sessionStorage.removeItem("id_programming");

  $("#searchMachine").change(function (e) {
    e.preventDefault();

    let data = [];

    if (this.value == '0') {
      data = allTblData;
      op = 1;
    }
    else {
      data = allTblData.filter(item => item.id_machine == this.value);
      op = 2;
    }

    loadTblProgramming(data, 2);
  });

  loadTblProgramming = async (data, op) => {

    if (allTblData.length > 0) {
      $('.cardSaveBottons').show(800);
    }

    if ($.fn.dataTable.isDataTable("#tblProgramming")) {
      $("#tblProgramming").DataTable().destroy();
      $("#tblProgramming").empty();
      $('#tblProgramming').append(`<tbody id="tblProgrammingBody"></tbody>`);
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
    data.forEach((arr, index) => {
      const i = index;
      const dataRow = body.insertRow();
      headers.forEach((header, columnIndex) => {
        const cell = dataRow.insertCell();
        switch (header) {
          case 'No.':
            cell.textContent = i + 1;
            break;
          case 'Pedido':
            cell.textContent = arr.num_order;
            break;
          case 'Referencia':
            cell.textContent = arr.reference;
            break;
          case 'Producto':
            cell.textContent = arr.product;
            break;
          case 'Maquina':
            cell.textContent = arr.machine;
            break;
          case 'Cantidades':
            const { quantity_order, quantity_programming, accumulated_quantity } = arr;
            if (accumulated_quantity > 0)
              cell.innerHTML = `Pedido: ${quantity_order}<br>Fabricar: ${quantity_programming}<br><span class="badge badge-danger">Pendiente: ${accumulated_quantity}</span>`;
            else
              cell.innerHTML = `Pedido: ${quantity_order}<br>Fabricar: ${quantity_programming}<br>Pendiente: ${accumulated_quantity}`;
            break;
          case 'Cliente':
            cell.textContent = arr.client;
            break;
          case 'Fecha y Hora':
            const { min_date, min_programming } = arr;
            const final_date = new Date(min_date);
            const minDate = new Date(min_date);
            final_date.setMinutes(minDate.getMinutes() + Math.floor(min_programming));
            cell.innerHTML = `Inicio: ${moment(min_date).format("DD/MM/YYYY HH:mm A")}<br>Fin: ${moment(final_date).format("DD/MM/YYYY HH:mm A")}`;
            break;
          case 'Orden Produccion':
            cell.innerHTML = `<button class="btn btn-warning changeStatus" id="${arr.id_programming}" name="${arr.id_programming}">Crear OP</button>`;
            break;
          case 'Acciones':
            cell.innerHTML = `
                <a href="javascript:;" <i id="${arr.id_programming}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${arr.id_programming}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction(${arr.id_programming}, ${arr.bd_status})"></i></a>`;
            break;
          default:
            cell.textContent = '';
            break;
        }
      });
    });

    $('#tblProgramming').DataTable(); 
    
    if (op == 2) {
      dragula([document.getElementById('tblProgrammingBody')]).on('drop', function (el, container, source, sibling) {
        // Get the row index of the dropped element
        var rowIndex = el.closest('tr').rowIndex;

        // If the row was dropped within the same container,
        // move it to the specified position
        if (container === source) {
          var targetIndex = sibling ? sibling.rowIndex : container.children.length - 1;
          container.insertBefore(el, container.children[targetIndex]);
        } else {
          // If the row was dropped into a different container,
          // move it to the first position
          container.insertBefore(el, container.firstChild);
        }
      });
    }

    // dragula([document.getElementById('tblProgrammingBody')]).on('drop', function (el, container, source, sibling) {
    //   // Get the row index of the dropped element
    //   var rowIndex = $(el).closest('tr').index();

    //   // If the row was dropped within the same container,
    //   // move it to the specified position
    //   if (container === source) {
    //     var targetIndex = $(el).closest('tbody').find('tr').index(sibling);
    //     $(el).closest('tr').insertAfter($(container).find('tr')[targetIndex]);
    //   } else {
    //     // If the row was dropped into a different container,
    //     // move it to the first position
    //     $(el).closest('tr').appendTo($(container));
    //   }
    // });

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
