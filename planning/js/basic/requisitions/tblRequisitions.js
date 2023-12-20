$(document).ready(function () {
  $('.selectNavigation').click(function (e) {
    e.preventDefault();
    
    if (this.id == 'pending')
      loadTblRequisitions(pending, true);
    else if (this.id == 'done')
      loadTblRequisitions(done, false);
  });

  loadAllData = async (op, min_date, max_date) => {
    try {
      const [dataRequisitions, dateRequisitions] = await Promise.all([
        searchData('/api/requisitions'),
        op == 3 ? searchData(`/api/requisitions/${min_date}/${max_date}`) : null
      ]);

      let card = document.getElementsByClassName('selectNavigation');

      if (card[0].className.includes('active')) {
        pending = 1;
        op = 1;
      }
      else {
        pending = 1;
        op = 2;
      }

      pending = dataRequisitions.filter(item => item.application_date == "0000-00-00" &&
        item.delivery_date == "0000-00-00" &&
        item.purchase_order == "").map(item => ({ ...item, status: 'Pendiente' }));
      
      let done1 = dataRequisitions.filter(item => item.application_date != "0000-00-00" &&
        item.delivery_date != "0000-00-00" &&
        item.purchase_order != "" && item.admission_date).map(item => ({ ...item, status: 'Recibido' }));
      
      let date = formatDate(new Date());

      let process = dataRequisitions.filter(item => item.application_date != "0000-00-00" &&
        item.delivery_date != "0000-00-00" &&
        item.purchase_order != "" && !item.admission_date && item.delivery_date >= date).map(item => ({ ...item, status: 'Proceso' }));
      
      let process1 = dataRequisitions.filter(item => item.application_date != "0000-00-00" &&
        item.delivery_date != "0000-00-00" &&
        item.purchase_order != "" && !item.admission_date);
      
      let delayed = process1.filter(item => item.delivery_date < date).map(item => ({ ...item, status: 'Retrasada' }));

      done = [...delayed, ...process, ...done1];

      $('#lblPending').html(` Pendientes: ${pending.length}`);
      $('#lblProcess').html(` Proceso: ${process.length}`);
      $('#lblDelayed').html(` Retrasadas: ${delayed.length}`);
      $('#lblReceived').html(` Recibido: ${done1.length}`);
      
      let visible = true;
      if (op === 1)
        dataToLoad = pending;
      else if (op === 2) {
        dataToLoad = deliveredStore
        visible = false;
      } else {
        if (pending == 1)
          dataToLoad = dateRequisitions.filter(item => item.application_date == "0000-00-00" &&
            item.delivery_date == "0000-00-00" &&
            item.purchase_order == "");
        else
          dataToLoad = dateRequisitions.filter(item => item.application_date != "0000-00-00" &&
            item.delivery_date != "0000-00-00" &&
            item.purchase_order != "");
      }

      if (dataToLoad) {
        loadTblRequisitions(dataToLoad, visible);
      }
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  /* Cargue tabla de Productos Materiales */
  loadTblRequisitions = (data, visible) => {
    if ($.fn.dataTable.isDataTable("#tblRequisitions")) {
      $("#tblRequisitions").DataTable().clear();
      $("#tblRequisitions").DataTable().rows.add(data).draw();
      return;
    }

    tblRequisitions = $("#tblRequisitions").dataTable({
      // destroy: true,
      pageLength: 50,
      order: [[0, 'asc']],
      data: data,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Descripción",
          data: "material",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Medida",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Proveedor",
          data: "provider",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Fecha Solicitud",
          data: "application_date",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Fecha Entrega",
          data: "delivery_date",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cantidad",
          data: "quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 2, ""),
        },
        {
          title: "Orden de Compra",
          data: "purchase_order",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "",
          data: 'status',
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            // if (
            //   (data.application_date == "0000-00-00" &&
            //   data.delivery_date == "0000-00-00" &&
            //   data.purchase_order == "" ) || !data.admission_date
            // )
            //   date = "";
            // else date = `Recibido<br>${data.admission_date}`;

            // return date;
            if (data == 'Pendiente')
              badge = 'badge-info';
            else if (data == 'Proceso')
              badge = 'badge-warning';
            else if (data == 'Retrasada')
              badge = 'badge-danger';
            else if (data == 'Recibido')
              badge = 'badge-success';
            
            return `<span class="badge ${badge}">${data}</span>`
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            if (data.status != 'Recibido')
              action = `<a href="javascript:;" <i id="${data.id_requisition}" class="bx bx-edit-alt updateRequisition" data-toggle='tooltip' title='Actualizar Requisicion' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data.id_requisition}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Requisicion' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
            else
              action = data.admission_date; 
            return action;
          },
        },
      ],
    });
  };

  loadAllData(1, null, null);
});
