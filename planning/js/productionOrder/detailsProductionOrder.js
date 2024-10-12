$(document).ready(function () {
    let dataPTOP, allStore, allMaterialsAccept;
    let id_programming = sessionStorage.getItem('id_programming');

    loadAllDataPO = async () => {
        const [dataOP, dataFTMaterials, dataStore, materialsCM] = await Promise.all([
            searchData("/api/productionOrder"),
            searchData("/api/allProductsMaterials"),
            searchData("/api/allStore"),
            searchData("/api/materialsComponents"),
        ]);
        // let dataOP = JSON.parse(sessionStorage.getItem('dataOP'));
        // let dataFTMaterials = JSON.parse(sessionStorage.getItem('dataFTMaterials'));
        // let allStore = JSON.parse(sessionStorage.getItem('dataAllStore'));
        allStore = dataStore;
        
        let data = dataOP.find(item => item.id_programming == id_programming);
        dataPTOP = data;
        let dataFT = dataFTMaterials.filter(item => item.id_product == data.id_product);
        allMaterialsAccept = materialsCM.filter(item => item.id_programming == id_programming);

        let flag_op = data.flag_op;

        if (flag_op == 1) {
            $('#formAddOPPArtial').hide();
            $('#formAddOPMP').hide();
            $('.cardCloseOP').hide();
            $('#thActions').hide();
        }

        if (data.origin == 1) {
            $('.cardMeasure').hide();
        }

        if (data.flag_cancel == 1) $('.cardExcOP').hide();
        else $('.cardExcOP').show();

        $('#imgClient').empty();

        if (data.img)
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

            let cost_ftm = dataFT[i].quantity_ftm * dataFT[i].cost;
            let cost_total = quantity_total * dataFT[i].cost;

            quantity_total = formatQuantity(quantity_total, dataFT[i].abbreviation);

            let store = allStore.filter(item => item.id_programming == id_programming && item.id_material == dataFT[i].id_material);

            let recieve = 0;
            let pending = 0;
 
            store.forEach(item => {
                recieve += parseFloat(item.delivery_store);
                item.delivery_pending == 0 ? pending = 0 : pending += parseFloat(item.delivery_pending);
            });

            let materialsAccept = allMaterialsAccept.filter(item => item.id_material == dataFT[i].id_material);

            let accept = 0;
            materialsAccept.forEach(item => {
                accept += parseFloat(item.quantity);
            });

            pending < 0 ? pending = 0 : pending;

            let action = '';

            if (recieve > 0) {
                if (recieve - accept > 0) {
                    action = `<button class="btn btn-info acceptMaterial" id="accept-${dataFT[i].id_material}">Aceptar MP</button>`;
                } else if (recieve - accept <= 0) {
                    action = `<a href="javascript:;">
                            <i class="mdi mdi-playlist-check seeAcceptMP programming-${id_programming} material-${dataFT[i].id_material}" data-toggle="tooltip" title="Ver Usuarios" style="font-size: 30px;color:black"></i>
                          </a>`;
                }
            }

            body.insertAdjacentHTML('beforeend',
                `<tr>
                    <td>${dataFT[i].reference_material}</td>
                    <td>${dataFT[i].material}</td>
                    <td>${quantity_ftm}</td>
                    <td>${cost_ftm.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
                    <td>${quantity_total}</td>
                    <td>${cost_total.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
                    <td>${dataFT[i].abbreviation}</td>
                    <td>${recieve.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td>
                    <td>${pending.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}</td> 
                    ${flag_op == 0 ? `<td>${action}</td>` : ''}
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
        // let date = new Date().toISOString().split("T")[0];
        
        const idMaterial = $(this).attr("id").split("-")[1];

        bootbox.confirm({
            title: "Aceptar Materia Prima!",
            message: "¿Desea aceptar la cantidad recibida de este material?.",
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
                    // let date = $("#dateOPMP").val();

                    // if (!date) {
                    //     toastr.error("Ingrese los campos");
                    //     return false;
                    // }

                    let store = allStore.filter(item => item.id_programming == id_programming && item.id_material == idMaterial);

                    let recieve = 0;
 
                    store.forEach(item => {
                        recieve += parseFloat(item.delivery_store);
                    });

                    let materialsAccept = allMaterialsAccept.filter(item => item.id_material == idMaterial);

                    let accept = 0;
                    materialsAccept.forEach(item => {
                        accept += parseFloat(item.quantity);
                    });

                    let form = new FormData();
                    form.append("idProgramming", id_programming);
                    form.append("idMaterial", idMaterial);
                    form.append("quantity", recieve - accept);
                    // form.append("date", date);

                    $.ajax({
                        type: "POST",
                        url: "/api/acceptMaterialReceive",
                        data: form,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (resp) {
                            messagePOD(resp);
                        },
                    });
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
        let dataOPP = tblPartialsDelivery.DataTable().rows().data().toArray();
        let dataOPM = tblOPMaterial.DataTable().rows().data().toArray();

        if (dataOPP.length == 0 || dataOPM.length == 0) {
            toastr.error('Ejecucion produccion o devolucion de materiales sin datos');
            return false;
        }

        let materialsAccept = allMaterialsAccept.filter(item => item.id_material == idMaterial);

        let accept = 0;
        materialsAccept.forEach(item => {
            accept += parseFloat(item.quantity);
        });

        if (accept == 0) {
            toastr.error('Materiales y Componentes no ejecutados');
            return false;
        }

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

    // Materiales aceptados
    $(document).on('click', '.seeAcceptMP', async function (e) {
        e.preventDefault();
        // Obtiene el elemento que fue clickeado
        const element = $(this)[0];

        // Obtiene todas las clases del elemento
        const classList = Array.from(element.classList);

        // Busca las clases que contienen 'programming-' y 'material-'
        const id_programming = classList.find(cls => cls.startsWith('programming-')).split('-')[1];
        const id_material = classList.find(cls => cls.startsWith('material-')).split('-')[1];

        let users = await searchData(`/api/materialsComponents/${id_programming}/${id_material}`);
        let rows = '';

        for (let i = 0; i < users.length; i++) {
            rows +=
                `<tr>
                    <td>${i + 1}</td>
                    <td>${users[i].firstname}</td>
                    <td>${users[i].lastname}</td>
                    <td>${users[i].email}</td>
                    <td>
                        ${parseFloat(users[i].quantity).toLocaleString("es-CO", {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                })}
                    </td>
                </tr>`;
        }

        // Mostramos el mensaje con Bootbox
        bootbox.alert({
            title: 'Usuarios',
            message: `
            <div class="container">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="fixed-table-loading table table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Cantidad Aceptada</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${rows}
                    </tbody>
                  </table>
                </div>
              </div> 
            </div>`,
            size: 'large',
            backdrop: true
        });
        return false;
    });

    // Descargar PDF
    $(document).on('click', '.downloadPlaneProduct', function () {
        let key = this.id;
        let pdfUrl = dataPTOP[key];

        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = 'plano.pdf'; // Nombre del archivo para descargar
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});