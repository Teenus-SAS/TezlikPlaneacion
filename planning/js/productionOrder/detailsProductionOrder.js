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
            $('#btnCloseOP').hide();
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
        let body = document.getElementById('tblPOMaterialsBody');
        
        for (let i = 0; i < dataFT.length; i++) {
            let quantity_ftm = formatQuantity(dataFT[i].quantity_ftm, dataFT[i].abbreviation); 
            let quantity_total = parseFloat(dataFT[i].quantity_ftm) * parseFloat(data.quantity_order);
            quantity_total = formatQuantity(quantity_total, dataFT[i].abbreviation);

            let store = allStore.filter(item => item.id_programming == id_programming && item.id_material == dataFT[i].id_material);

            let recieve = 0;
            let pending = 0;
 
            store.forEach(item => {
                recieve += item.delivery_store;
                pending -= item.delivery_pending;
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
                </tr>`);
        }
        
        // Procesos
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
            message: `¿ Está seguro de cerrar esta orden de produccion. ?`,
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
                            const { success, error, info, message } = resp;
                            if (success) {
                                loadAllDataPO();
                                toastr.success(message); 
                            } else if (error) toastr.error(message);
                            else if (info) toastr.info(message);
                        },
                    );
                }
            },
        });
    });

    loadAllDataPO();
});