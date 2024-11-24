$(document).ready(function () {
  sessionStorage.removeItem("id_programming");
  let copy;

  $("#searchMachine").change(function (e) {
    e.preventDefault();

    let data = [];
    let op;
    copy = undefined;
    let allTblData = flattenData(generalMultiArray);

    if (this.value == "0") {
      data = allTblData;
      op = 1;
    } else {
      data = allTblData.filter((item) => item.id_machine == this.value);

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

  $("#simulationType").change(function (e) {
    e.preventDefault();
    let allTblData = flattenData(generalMultiArray);

    let dataSim = allTblData.filter((item) => item.sim == this.value);
    loadTblProgramming(dataSim, 1);
  });

  loadTblProgramming = async (data, op) => {
    sessionStorage.setItem(
      "dataProgramming",
      JSON.stringify(generalMultiArray)
    );

    if (data.length > 0) {
      $(".cardSaveBottons").show(800);
      $(".cardSimulation").show(800);
      $(".cardAddOP").show(800);
      $("#machines1").show(800);
    } else {
      $(".cardAddOP").hide(800);
      $(".cardSaveBottons").hide(800); 
      $("#machines1").hide();
    }

    if (allProgramming.length == 0) {
      $(".cardAddOP").hide(800);
    }

    if ($.fn.dataTable.isDataTable("#tblProgramming")) {
      $("#tblProgramming").DataTable().destroy();
      $("#tblProgramming").empty();
      $("#tblProgramming").append(`<tbody id="tblProgrammingBody"></tbody>`);
    }
    // Encabezados de la tabla
    var headers = [
      "No.",
      "Pedido",
      "Referencia",
      "Producto",
      "Proceso",
      "Maquina",
      "Cantidades",
      "Cliente",
      `${flag_type_program == 0 ? "Fecha y Hora" : "Fecha Inicial"}`,
      "Acciones",
    ];

    // Obtén la tabla
    var table = document.getElementById("tblProgramming");

    // Crea la fila de encabezados
    var headerRow = table.createTHead().insertRow();
    headers.forEach(function (header) {
      var th = document.createElement("th");
      th.textContent = header;
      headerRow.appendChild(th);
    });

    $("#tblProgrammingBody").empty();

    var body = document.getElementById("tblProgrammingBody");

    // Itera sobre los datos y crea filas para cada conjunto de datos
    data.forEach((arr, index) => {
      const i = index;
      const dataRow = body.insertRow();
      dataRow.setAttribute("data-index", index);
      headers.forEach((header, columnIndex) => {
        const cell = dataRow.insertCell();
        switch (header) {
          case "No.":
            cell.textContent = i + 1;
            break;
          case "Pedido":
            cell.textContent = arr.num_order;
            break;
          case "Referencia":
            cell.textContent = arr.reference;
            break;
          case "Producto":
            cell.textContent = arr.product;
            break;
          case "Proceso":
            cell.textContent = arr.process;
            break;
          case "Maquina":
            cell.textContent = arr.machine;
            break;
          case "Cantidades":
            const {
              quantity_order,
              quantity_programming,
              accumulated_quantity,
            } = arr;
            if (accumulated_quantity > 0)
              cell.innerHTML = `Pedido: ${quantity_order}<br>Fabricar: ${quantity_programming}<br><span class="badge badge-danger">Pendiente: ${accumulated_quantity}</span>`;
            else
              cell.innerHTML = `Pedido: ${quantity_order}<br>Fabricar: ${quantity_programming}<br>Pendiente: ${accumulated_quantity}`;
            break;
          case "Cliente":
            cell.textContent = arr.client;
            break;
          case "Fecha y Hora":
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
            final_date.setMinutes(
              minDate.getMinutes() + Math.floor(minProgramming)
            );
            // Checkear si la hora de la fecha final calculada es mayor a la hora de finalizacion de la maquina
            let hour_check = parseFloat(
              `${final_date.getHours()}.${final_date.getMinutes()}`
            );
            
            let planningMachine = allPlanningMachines.find(
              (item) => item.id_machine == arr.id_machine
            );

            if (hour_check > planningMachine.hour_end || hour_check < 6) {
              let hours = Math.floor(planningMachine.hour_start);
              let minutes = parseInt(
                planningMachine.hour_start.toFixed(2).toString().split(".")[1]
              );

              isNaN(minutes) ? (minutes = 0) : minutes;

              final_date.setMinutes(Math.floor(minutes));
              final_date.setHours(Math.floor(hours));
              final_date.setDate(final_date.getDate() + 1);
            }
            

            if (op == 2 && i > 0) {
              minDate.setMinutes(minDate.getMinutes() + Math.floor(min_date1));
            }
            cell.innerHTML = `Inicio: ${moment(minDate).format(
              "DD/MM/YYYY hh:mm A"
            )}<br>Fin: ${moment(final_date).format("DD/MM/YYYY hh:mm A")}`;
            break;
          case "Fecha Inicial":
            cell.innerHTML = `Inicio: ${arr.min_date}`;
            break;
          case "Acciones":
            cell.innerHTML = `<a href="javascript:;" <i id="${arr.id_programming
              }" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${arr.id_programming
              }" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction(${arr.bd_status == 1 ? arr.id_programming : i
              }, ${arr.bd_status})"></i></a>`;
            break;
          default:
            cell.textContent = "";
            break;
        }
      });
    });

    $("#tblProgramming").DataTable({
      headerCallback: function (thead, data, start, end, display) {
        $(thead).find("th").css({
          "background-color": "#386297" /* Color de fondo */,
          color: "white" /* Color del texto */,
          "text-align": "center" /* Centrar el texto */,
          "font-weight": "bold" /* Texto en negrita */,
          padding: "10px" /* Espaciado interno */,
          border: "1px solid #ddd" /* Borde del encabezado */,
        });
      },
    });

    if (op == 2) {
      dragula([document.getElementById("tblProgrammingBody")]).on(
        "drop",
        function (el, container, source, sibling) {
          // Obtener el indice de la fila anterior
          var previousIndex = el.dataset.index;
          // Obtener el índice de fila actual
          var currentIndex = el.closest("tr").rowIndex;

          // If the row was dropped within the same container,
          // move it to the specified position
          if (container === source) {
            var targetIndex = sibling
              ? sibling.rowIndex - 1
              : container.children.length - 1;

            container.insertBefore(el, container.children[targetIndex]);

            // Crear copia para organizar el array de acuerdo a la key

            !copy ? (copy = [...data]) : copy;

            copy[previousIndex]["key"] = currentIndex - 1;
            copy[currentIndex - 1]["key"] = previousIndex;

            copy.sort((a, b) => a.key - b.key);

            loadTblProgramming(copy, 2);
          } else {
            // If the row was dropped into a different container,
            // move it to the first position
            container.insertBefore(el, container.firstChild);
          }
        }
      );
    }
  };
});
