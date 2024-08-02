$(document).ready(function () {
  sessionStorage.removeItem("id_programming");
  let copy;

  $("#searchMachine").change(function (e) {
    e.preventDefault();

    let data = [];
    let op;
    copy = undefined;

    if (this.value == '0') {
      data = allTblData;
      op = 1;
    }
    else { 
      data = allTblData.filter(item => item.id_machine == this.value);

      if (data.length > 0) {
        MMindate = data[0].min_date;
        op = 2;
      } else {
        // data = allTblData;
        op = 1;
      }
    }

    loadTblProgramming(data, op);
  });

  loadTblProgramming = async (data, op) => { 
    sessionStorage.setItem('dataProgramming', JSON.stringify(allTblData));
     
    if (allTblData.length > 0) {
      $('.cardSaveBottons').show(800);
      $('#machines1').show(800);
    } else {
      $('#machines1').hide();
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
      dataRow.setAttribute('data-index', index);
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
            // const min_date = arr.min_date;
            // if ($('#searchMachine').val() != '0' && $('#searchMachine').val()) {
            if (op == 2) {
              if (i == 0) {
                minProgramming = 0;
              } else {
                min_date1 = minProgramming;
              }
              minProgramming += arr.min_programming;
              min_date = MMindate;
            } else {
              min_date = arr.min_date;
              minProgramming = arr.min_programming;
            }
            
            let minDate = new Date(min_date);
            let final_date = new Date(min_date);
            final_date.setMinutes(minDate.getMinutes() + Math.floor(minProgramming));
            
            // if ($('#searchMachine').val() != '0' && $('#searchMachine').val() && i > 0) {
            if (op == 2 && i > 0) {
              minDate.setMinutes(minDate.getMinutes() + Math.floor(min_date1));
            }
            cell.innerHTML = `Inicio: ${moment(minDate).format("DD/MM/YYYY hh:mm A")}<br>Fin: ${moment(final_date).format("DD/MM/YYYY hh:mm A")}`;
            break;
          case 'Orden Produccion':
            arr.bd_status == 1 ? cell.innerHTML = `<button class="btn btn-warning changeStatus" id="${arr.id_programming}" name="${arr.id_programming}">Crear OP</button>` :
              '';
            break;
          case 'Acciones':
            cell.innerHTML = $('#searchMachine').val() != '0' && $('#searchMachine').val() ?
              `<a href="javascript:;" <i id="${arr.id_programming}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${arr.id_programming}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction(${arr.bd_status == 1 ? arr.id_programming : i}, ${arr.bd_status})"></i></a>`
              : '';
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
        // Obtener el indice de la fila anterior
        var previousIndex = el.dataset.index;
        // Obtener el índice de fila actual
        var currentIndex = el.closest('tr').rowIndex;

        // If the row was dropped within the same container,
        // move it to the specified position
        if (container === source) {
          var targetIndex = sibling ? sibling.rowIndex - 1 : container.children.length - 1;
          
          container.insertBefore(el, container.children[targetIndex]);

          // Crear copia para organizar el array de acuerdo a la key

          !copy ? copy = [...allTblData] : copy; 
          
          copy[previousIndex]['key'] = currentIndex - 1;
          copy[currentIndex - 1]['key'] = previousIndex;

          copy.sort((a, b) => a.key - b.key);

          loadTblProgramming(copy, 2);
        } else {
          // If the row was dropped into a different container,
          // move it to the first position
          container.insertBefore(el, container.firstChild);
        }
      });
    } 
  };
});
