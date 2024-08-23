$(document).ready(function () {
    $('.cardAddNewProduct').hide();

    $('#btnAddNewProduct').click(async function (e) {
        e.preventDefault();

        $('.cardImportProductsMaterials').hide(800);
        $('.cardAddMaterials').hide(800);
        $('.cardAddNewProduct').toggle(800);
        $('#btnAddProduct').html('Asignar');
        $('#units2').empty();
        
        sessionStorage.removeItem('id_composite_product');
        
        $('.inputs').css('border-color', '');
        $('#formAddNewProduct').trigger('reset');
    });

    $('.compositeProduct').change(function (e) {
        e.preventDefault();

        let data = JSON.parse(sessionStorage.getItem('dataUnits'));

        let filterData = data.filter(item => item.unit == 'UNIDAD');
        
        let $select = $(`#unit2`);
        $select.empty();
        $select.append(`<option disabled>Seleccionar</option>`);
        $select.append(`<option value ='${filterData[0].id_unit}' selected>UNIDAD</option>`);
    });

    $('#btnAddProduct').click(function (e) {
        e.preventDefault();

        let idCompositeProduct = sessionStorage.getItem('id_composite_product');

        if (idCompositeProduct == '' || idCompositeProduct == null) {
            checkDataProducts(
                '/api/addCompositeProduct',
                idCompositeProduct
            );
        } else {
            checkDataProducts(
                '/api/updateCompositeProduct',
                idCompositeProduct
            );
        }
    });

    /* Actualizar productos materials */

    $(document).on('click', '.updateComposite', function (e) {
        $('.cardImport').hide(800);
        $('.cardAddNewProduct').show(800);
        $('.inputs').css('border-color', '');
        $('#btnAddProduct').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblConfigMaterials.fnGetData(row);

        sessionStorage.setItem('id_composite_product', data.id_composite_product);
        $(`#refCompositeProduct option[value=${data.id_child_product}]`).prop('selected', true);
        $(`#compositeProduct option[value=${data.id_child_product}]`).prop('selected', true);

        $('#quantityCP').val(data.quantity);  

        data = JSON.parse(sessionStorage.getItem('dataUnits'));

        let filterData = data.filter(item => item.unit == 'UNIDAD');
        
        let $select = $(`#unit2`);
        $select.empty();
        $select.append(`<option disabled>Seleccionar</option>`);
        $select.append(`<option value ='${filterData[0].id_unit}' selected>UNIDAD</option>`);

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });

    function validateForm() {
        let emptyInputs = [];
        let selectNameProduct = parseInt($('#selectNameProduct').val());
        let quantityCP = parseFloat($('#quantityCP').val());

        // Verificar cada campo y agregar los vacíos a la lista
        if (!selectNameProduct) {
            emptyInputs.push('#selectNameProduct');
        }
        if (!quantityCP) {
            emptyInputs.push('#quantityCP');
        }

        // Marcar los campos vacíos con borde rojo
        emptyInputs.forEach(function (selector) {
            $(selector).css('border-color', 'red');
        });

        // Mostrar mensaje de error si hay campos vacíos
        if (emptyInputs.length > 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        return true;
    };

    /* Revision data Productos materiales */
    checkDataProducts = async (url, idCompositeProduct) => {
        if (!validateForm()) return false;

        let ref = parseInt($('#compositeProduct').val());
        let quan = parseFloat($('#quantityCP').val());
        let idProduct = parseInt($('#selectNameProduct').val());
      
        if (ref == idProduct) {
            $('#compositeProduct').css('border-color', 'red');
            toastr.error('Seleccione un producto compuesto diferente');
            return false;
        }

        quant = 1 * quan;

        if (quan <= 0 || isNaN(quan)) {
            $('#quantityCP').css('border-color', 'red');
            toastr.error('La cantidad debe ser mayor a cero (0)');
            return false;
        }

        let dataProduct = new FormData(formAddNewProduct);
        dataProduct.append('idProduct', idProduct);

        if (idCompositeProduct != '' || idCompositeProduct != null)
            dataProduct.append('idCompositeProduct', idCompositeProduct);

        let resp = await sendDataPOST(url, dataProduct);

        messageMaterial(resp);
    };
});