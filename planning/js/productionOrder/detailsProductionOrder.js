$(document).ready(function () {
    loadAllDataPO = () => {
        let dataOP = JSON.parse(sessionStorage.getItem('dataOP'));
        let dataFTMaterials = JSON.parse(sessionStorage.getItem('dataFTMaterials'));
        let allStore = JSON.parse(sessionStorage.getItem('dataAllStore'));
        let id_programming = sessionStorage.getItem('id_programming');

        let data = dataOP.find(item => item.id_programming == id_programming);
        let dataFT = dataFTMaterials.filter(item => item.id_product == data.id_product);

        let flag_op = data.flag_op;

        if (flag_op == 1) {
            $('#formAddOPPArtial').hide();
            $('#formAddOPMP').hide();
            $('.cardCloseOP').hide();
            $('#thActions').hide();
        }

        if (data.flag_cancel == 1) $('.cardExcOP').hide();
        else $('.cardExcOP').show();

        $('#imgClient').empty();

        if(data.img)
            $('#imgClient').html(`<img src="${data.img}" width="150px">`);
        // Orden Produccion
        $('#txtNumOP').html(data.num_production);
        $('#txtNumOrder').html(data.num_order);

        let date_order = moment(data.date_order).format("DD/MM/YYYY");
        let min_date = moment(data.min_date_order).format("DD/MM/YYYY") == "Invalid date" ? "0000-00-00" : moment(data.min_date_order).format("DD/MM/YYYY");
        let max_date = moment(data.max_date_order).format("DD/MM/YYYY") == "Invalid date" ? "0000-00-00" : moment(data.max_date_order).format("DD/MM/YYYY");
        
        $('#txtEDate').html(
            `<p><b class="font-weight-bold text-dark">Fecha de Emisión:</b>  ${date_order}</p>`
        ); 
        
        $('#txtMinDate').val(min_date);
        $('#txtMaxDate').val(max_date);
        $('#txtQuantityP').val(data.quantity_programming); 
        $('#nameClient').val(data.client);

        // Info Producto
        $('#txtReferenceP').val(data.reference);
        $('#txtNameP').val(data.product); 
        $('#width').val(data.width);
        $('#high').val(data.high);
        $('#length').val(data.length);
        $('#usefulLength').val(data.useful_length);
        $('#totalWidth').val(data.total_width);
        $('#window').val(data.window);

        // Datatable Materiales 
        $('#tblPOMaterialsBody').empty();
        let body = document.getElementById('tblPOMaterialsBody');
        
        for (let i = 0; i < dataFT.length; i++) {
            let quantity_ftm = formatQuantity(dataFT[i].quantity_ftm, dataFT[i].abbreviation); 
            let quantity_total = parseFloat(dataFT[i].quantity_ftm) * parseFloat(data.quantity_programming);
            quantity_total = formatQuantity(quantity_total, dataFT[i].abbreviation);

            let store = allStore.filter(item => item.id_programming == id_programming && item.id_material == dataFT[i].id_material);

            let recieve = 0;
            let pending = 0;
 
            store.forEach(item => {
                recieve += parseFloat(item.delivery_store);
                item.delivery_pending == 0 ? pending = 0 : pending += parseFloat(item.delivery_pending);
            });

            pending < 0 ? pending = 0 : pending;

            body.insertAdjacentHTML('beforeend',
                `<tr>
                    <td>${dataFT[i].reference_material}</td>
                    <td>${dataFT[i].material}</td>
                    <td>${quantity_ftm.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
                    <td>${quantity_total.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
                    <td>${dataFT[i].abbreviation}</td>
                    <td>${recieve.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
                    <td>${pending.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td> 
                    ${flag_op == 0 ?
                        `<td>
                            ${recieve > 0 ?
                            `<button class="btn btn-info acceptMaterial" id="accept-${dataFT[i].id_material}">Aceptar MP</button>` : ''}    
                        </td>` : ''}
                </tr>`);
        }
        
        // Procesos
        $('#tblPOProcessBody').empty();
        body = document.getElementById('tblPOProcessBody');

        min_date = moment(data.min_date_programming).format("DD/MM/YYYY HH:mm A");
        max_date = moment(data.max_date_programming).format("DD/MM/YYYY HH:mm A");
        

        body.insertAdjacentHTML('beforeend',
            `<tr>
                <td>1</td>
                <td>${data.process}</td>
                <td>${data.machine}</td>
                <td>${min_date}</td>
                <td>${max_date}</td>
            </tr>`
        );

        if (data.flag_cancel == 0) {
            flag_op == 1 ? visible = false : visible = true;
            loadTblPartialsDelivery(id_programming, visible);
            loadTblOPMaterial(id_programming, visible);
        }
    };

    $(document).on('click', '.acceptMaterial', function () {
        // Obtener el ID del elemento
        let date = new Date().toISOString().split("T")[0];
        
        const idMaterial = $(this).attr("id").split("-")[1];

        bootbox.confirm({
            title: "Ingrese Fecha De Ingreso!",
            message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="dateOPMP" max="${date}"></input>
                        <label for="date">Fecha</span></label>
                      </div>`,
            buttons: {
                confirm: {
                    label: "Guardar",
                    className: "btn-success",
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-danger",
                },
            },
            callback: function (result) {
                if (result) {
                    let date = $("#dateOPMP").val();

                    if (!date) {
                        toastr.error("Ingrese los campos");
                        return false;
                    }

                    let store = allStore.filter(item => item.id_programming == id_programming && item.id_material == idMaterial);

                    let recieve = 0; 
 
                    store.forEach(item => {
                        recieve += parseFloat(item.delivery_store); 
                    });

                    // let form = new FormData();
                    // form.append("idOPM", data.id_prod_order_material);
                    // form.append("idMaterial", idMaterial);
                    // form.append("quantity", recieve);
                    // form.append("date", date);

                    // $.ajax({
                    //     type: "POST",
                    //     url: "/api/saveReceiveOPMPDate",
                    //     data: form,
                    //     contentType: false,
                    //     cache: false,
                    //     processData: false,
                    //     success: function (resp) {
                    //         messagePOD(resp);
                    //     },
                    // });
                }
            },
        });
    });

    const formatQuantity = (quantity, abbreviation) => {
        quantity = parseFloat(quantity);
        
        if (Math.abs(quantity) < 0.01)
            return quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        
        if (Math.abs(quantity) > 1)
            return quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            
        if (abbreviation === "UND")
            quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
            });
        else
            quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 4,
                maximumFractionDigits: 4
            });
        
        return quantity;
    };  

    // Cerrar OP
    $('#btnCloseOP').click(function (e) {
        e.preventDefault();
        let id_programming = sessionStorage.getItem('id_programming');
        
        bootbox.confirm({
            title: "Orden de Producción",
            message: `¿Está seguro de cerrar esta orden de produccion? Esta acción no se puede reversar`,
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
                if (result == true) {
                    $.get(`/api/changeFlagOP/${id_programming}/1`,
                        function (resp, textStatus, jqXHR) {
                            messagePOD(resp);
                        },
                    );
                }
            },
        });
    }); 

    loadAllDataPO();

    messagePOD = async (data) => {
        const { success, error, info, message } = data;
        if (success) {
            loadAllDataPO();
            toastr.success(message);
        } else if (error) toastr.error(message);
        else if (info) toastr.info(message);
    };
});