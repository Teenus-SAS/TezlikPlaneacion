$(document).ready(function () {
    // var loadTblProductType;

    loadAllDataPType = async () => {
        let data = await searchData('/api/productsType');

        loadSelectProductType(data);
        loadTblProductType(data);
    };

    const loadSelectProductType = (data) => {
        let $select = $(`#idProductType`);
        $select.empty();

        $select.append(
            `<option value='0' disabled selected>Seleccionar</option>`
        );
        $.each(data, function (i, value) {
            $select.append(
                `<option value ='${value.id_product_type}'> ${value.product_type} </option>`
            );
        });
    };

    loadAllDataPType();
});