$(document).ready(function () {
    $.ajax({
        url: '/api/materialsType',
        success: function (r) {
            let $select = $(`#materialType`);
            $select.empty();

            $select.append(
                `<option value='0' disabled selected>Seleccionar</option>`
            );
            $.each(r, function (i, value) {
                $select.append(
                    `<option value ='${value.id_material_type}'> ${value.material_type} </option>`
                );
            });
        }
    });
});