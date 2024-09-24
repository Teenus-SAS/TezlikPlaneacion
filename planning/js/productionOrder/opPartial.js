$(document).ready(function () {
    loadTblPartialsDelivery = (id_programming) => {
        tblPartialsDelivery = $('#tblPartialsDelivery').dataTable({
            destroy: true,
            pageLength: 50,
            ajax: {
                url: `/api/productionOrderPartial/${id_programming}`,
                dataSrc: '',
            },
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
                    title: "Fechas",
                    data: null,
                    className: "uniqueClassName dt-head-center",
                    width: "200px",
                    render: function (data, type, full, meta) {
                        const start_date = full.start_date;
                        const end_date = full.end_date;

                        return `Inicio: ${moment(start_date).format(
                            "DD/MM/YYYY HH:mm A"
                        )}<br>Fin: ${moment(end_date).format("DD/MM/YYYY HH:mm A")}`;
                    },
                },
                {
                    title: 'Operador',
                    data: null,
                    className: 'uniqueClassName dt-head-center',
                    render: function (data) {
                        return `${data.firstname} ${data.lastname}`;
                    }
                },
                {
                    title: 'Desperdicio',
                    data: 'waste',
                    className: 'uniqueClassName dt-head-center',
                    render: $.fn.dataTable.render.number('.', ',', 0, ''),
                },
                {
                    title: 'Cantidad Entregada',
                    data: 'partial_quantity',
                    className: 'uniqueClassName dt-head-center',
                    render: $.fn.dataTable.render.number('.', ',', 0, ''),
                },
                {
                    title: "Acciones",
                    data: "id_part_deliv",
                    className: "uniqueClassName dt-head-center",
                    render: function (data) {
                        return `
                            <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateOPPartial" data-toggle='tooltip' title='Actualizar Produccion' style="font-size: 30px;"></i></a>
                            <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Produccion' style="font-size: 30px;color:red" onclick="deleteOPPartialFunction()"></i></a>
                            `;
                    },
                },
            ],
        });
    };

    /* Crear OP Parcial */
    $("#btnDeliverPartialOP").click(function (e) {
        e.preventDefault();

        const idPartDeliv = sessionStorage.getItem("id_part_deliv") || null;
        const apiUrl = !idPartDeliv
            ? "/api/addOPPartial"
            : "/api/updateOPPartial";

        checkDataOPPartial(apiUrl, idPartDeliv);
    });

    /* Actualizar OP Parcial */
    $(document).on("click", ".updateOPPartial", function (e) {
        $("#btnDeliverPartialOP").text("Actualizar");

        // Obtener el ID del elemento
        const idPartDeliv = $(this).attr("id").split("-")[1];

        sessionStorage.setItem("id_part_deliv", idPartDeliv);

        // Obtener data
        const row = $(this).closest("tr")[0];
        const data = tblPartialsDelivery.fnGetData(row);

        // Asignar valores a los campos del formulario y animar
        $('#startDateTime').val(data.start_date);
        $('#endDateTime').val(data.end_date);
        $('#waste').val(data.waste);
        $('#quantityProduction').val(data.partial_quantity);
    }); 

    // Entregas Parciales
    const checkDataOPPartial = async(url, idPartDeliv) => {
        let startDateTime = $('#startDateTime').val();
        let endDateTime = $('#endDateTime').val();
        // let operator = parseInt($('#operator').val());
        let waste = parseInt($('#waste').val());
        let quantityProduction = parseInt($('#quantityProduction').val());

        if (!startDateTime || startDateTime == '' || !endDateTime || endDateTime == '' || isNaN(quantityProduction) || quantityProduction <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        };

        let id_programming = sessionStorage.getItem('id_programming');

        let dataOP = new FormData(formAddOPPArtial);
        dataOP.append('idProgramming', id_programming);
        
        if(idPartDeliv) 
            dataOP.append('idPartDeliv', idPartDeliv);

        let resp = await sendDataPOST(url, dataOP);

        messageOPPartial(resp);
    };

    /* Eliminar productos */
    deleteOPPartialFunction = () => {
        const row = $(this.activeElement).closest("tr")[0];
        const data = tblPartialsDelivery.fnGetData(row);

        const { id_part_deliv } = data;

        bootbox.confirm({
            title: "Eliminar",
            message:
                "Está seguro de eliminar esta programacion? Esta acción no se puede reversar.",
            buttons: {
                confirm: {
                    label: "Si",
                    className: "btn-success",
                },
                cancel: {
                    label: "No",
                    className: "btn-danger",
                },
            },
            callback: function (result) {
                if (result) {
                    $.get(
                        `/api/deleteOPPartial/${id_part_deliv}`,
                        function (data, textStatus, jqXHR) {
                            messageOPPartial(data);
                        }
                    );
                }
            },
        });
    };

    /* Mensaje de exito */

    const messageOPPartial = (data) => {
        const { success, error, info, message } = data;
        if (success) {
            $("#formAddOPPArtial").trigger("reset");
            toastr.success(message);
            loadAllDataPO();
            return false;
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);
    };
});