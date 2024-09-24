$(document).ready(function () {
    loadTblOPMaterial = (id_programming) => {
        tblOPMaterial = $('#tblOPMaterial').dataTable({
            destroy: true,
            pageLength: 50,
            ajax: {
                url: `/api/productionOrderMaterial/${id_programming}`,
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
                    title: 'Referencia',
                    data: 'reference',
                    className: 'uniqueClassName dt-head-center', 
                },
                {
                    title: 'Material',
                    data: 'material',
                    className: 'uniqueClassName dt-head-center', 
                },
                {
                    title: 'Cantidad',
                    data: 'quantity',
                    className: 'uniqueClassName dt-head-center',
                    render: $.fn.dataTable.render.number('.', ',', 0, ''),
                },
                {
                    title: "Acciones",
                    data: "id_prod_order_material",
                    className: "uniqueClassName dt-head-center",
                    render: function (data) {
                        return `
                            <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateOPMaterial" data-toggle='tooltip' title='Actualizar Material' style="font-size: 30px;"></i></a>
                            <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Material' style="font-size: 30px;color:red" onclick="deleteOPMaterialFunction()"></i></a>
                            `;
                    },
                },
            ],
        });
    };

    /* Crear OP Parcial */
    $("#btnAddOPMP").click(function (e) {
        e.preventDefault();

        const idOPM = sessionStorage.getItem("id_prod_order_material") || null;
        const apiUrl = !idOPM
            ? "/api/addOPMaterial"
            : "/api/updateOPMaterial";

        checkDataOPMaterial(apiUrl, idOPM);
    });

    /* Actualizar OP Parcial */
    $(document).on("click", ".updateOPMaterial", function (e) {
        $("#btnAddOPMP").text("Actualizar");

        // Obtener el ID del elemento
        const idOPM = $(this).attr("id").split("-")[1];

        sessionStorage.setItem("id_prod_order_material", idOPM);

        // Obtener data
        const row = $(this).closest("tr")[0];
        const data = tblOPMaterial.fnGetData(row);

        // Asignar valores a los campos del formulario y animar
        $(`#refMaterial option[value=${data.id_material}]`).prop("selected", true);
        $(`#material option[value=${data.id_material}]`).prop("selected", true);        
        $('#quantityMP').val(data.quantity);
    }); 

    // Entregas Parciales
    const checkDataOPMaterial = async(url, idOPM) => {
        let id_material = parseInt($('#refMaterial').val()); 
        let quantity = parseInt($('#quantityMP').val());

        let data = id_material * quantity;

        if (isNaN(data) || data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        };

        let id_programming = sessionStorage.getItem('id_programming');

        let dataOP = new FormData(formAddOPMP);
        dataOP.append('idProgramming', id_programming);
        
        if(idOPM) 
            dataOP.append('idOPM', idOPM);

        let resp = await sendDataPOST(url, dataOP);

        messageOPMaterial(resp);
    };

    /* Eliminar productos */
    deleteOPMaterialFunction = () => {
        const row = $(this.activeElement).closest("tr")[0];
        const data = tblOPMaterial.fnGetData(row);

        const { id_prod_order_material } = data;

        bootbox.confirm({
            title: "Eliminar",
            message:
                "Está seguro de eliminar esta materia prima? Esta acción no se puede reversar.",
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
                        `/api/deleteOPMaterial/${id_prod_order_material}`,
                        function (data, textStatus, jqXHR) {
                            messageOPMaterial(data);
                        }
                    );
                }
            },
        });
    };

    /* Mensaje de exito */

    const messageOPMaterial = (data) => {
        const { success, error, info, message } = data;
        if (success) {
            $("#formAddOPMP").trigger("reset");
            toastr.success(message);
            loadAllDataPO();
            return false;
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);
    };
});