$(document).ready(function () {
    const loadAllDataPO = () => {
        let dataOP = JSON.parse(sessionStorage.getItem('dataOP'));
        let dataFTMaterials = JSON.parse(sessionStorage.getItem('dataFTMaterials'));
        let id_programming = sessionStorage.getItem('id_programming');

        let data = dataOP.find(item => item.id_programming == id_programming);
        let dataFT = dataFTMaterials.filter(item => item.id_product == data.id_product);

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

            body.insertAdjacentHTML('beforeend',
                `<tr>
                    <td>${dataFT[i].reference_material}</td>
                    <td>${dataFT[i].material}</td>
                    <td>${quantity_ftm}</td>
                    <td>${quantity_total}</td>
                    <td>${dataFT[i].abbreviation}</td>
                    <td></td>
                    <td></td> 
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
                <td>${min_date}</td>
                <td>${max_date}</td>
            </tr>`
        );
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
    }

    loadAllDataPO();
});