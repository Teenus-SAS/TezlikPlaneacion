$(document).ready(function () {
    /* Ocultar panel crear planos */

    $(".cardAddProductPlans").hide();

    /* Cargar imagen de planos 
    $("#formFile").change(function (e) {
        // e.preventDefault();

        $("#preview").html(
            `<img id="img" src="${URL.createObjectURL(
                event.target.files[0]
            )}" style="width:50%;padding-bottom:15px"/>`
        );
    }); */

    /* Abrir panel crear planos */
    $('#btnNewProductPlans').click(async function (e) {
        e.preventDefault();
 
        $(".cardAddProductPlans").toggle(800); 
        $("#btnSaveProductsPlans").html("Crear");

        sessionStorage.removeItem("id_product_plan");

        $("#formAddProductPlan").trigger("reset");
        $("#mechanicalPlaneFile").remove();
        $("#assemblyPlaneFile").remove();
    });

    /* Crear planos */
    $('#btnSaveProductsPlans').click(function (e) {
        e.preventDefault();
        let idProductPlan = sessionStorage.getItem("id_product_plan");

        if (idProductPlan == "" || idProductPlan == null) {
            checkDataProductsPlan("/api/addProductPlan", idProductPlan);
        } else {
            checkDataProductsPlan("/api/updateProductPlan", idProductPlan);
        }
    });

    /* Actualizar Planos */

    $(document).on("click", ".updateProductPlan", function (e) { 
        $(".cardAddProductPlans").show(800);
        $("#btnSaveProductsPlans").html("Actualizar");

        // Obtener el ID del elemento
        let id = $(this).attr('id');
        // Obtener la parte después del guion '-'
        let idProductPlan = id.split('-')[1];

        sessionStorage.setItem("id_product_plan", idProductPlan);

        // let row = $(this).parent().parent().parent()[0];
        // let data = tblPlans.fnGetData(row);

        // if (data.img)
        //     $("#preview").html(
        //         `<img id="img" src="${data.img}" style="width:50%;padding-bottom:15px"/>`
        //     );

        $("html, body").animate({ scrollTop: 0 }, 1000);
    });

    /* Revisar datos */
    const checkDataProductsPlan = async (url, idProductPlan) => { 
        let mechanicalPlaneFile = $("#mechanicalPlaneFile")[0].files[0];
        let assemblyPlaneFile = $("#assemblyPlaneFile")[0].files[0]; 

        if (!mechanicalPlaneFile || !assemblyPlaneFile) {
            toastr.error("Seleccione un archivo correspondiente");
            return false;
        }

        let dataProduct = new FormData(formAddProductPlan);
        dataProduct.append("mechanicalPlaneFile", mechanicalPlaneFile); 
        dataProduct.append("assemblyPlaneFile", assemblyPlaneFile); 
        
        if (idProductPlan) {
            dataProduct.append("idProductPlan", idProductPlan);            
        }

        let resp = await sendDataPOST(url, dataProduct);

        messageProductsPlan(resp);
    };

    /* Eliminar Planos */
    deleteProductsPlanFunction = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblPlans.fnGetData(row);

        let idProductPlan = data.id_product_plan;

        bootbox.confirm({
            title: "Eliminar",
            message:
                "Está seguro de eliminar este plano? Esta acción no se puede reversar.",
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
                        `/api/deleteProductPlan/${idProductPlan}`,
                        function (data, textStatus, jqXHR) {
                            messageProductsPlan(data);
                        }
                    );
                }
            },
        });
    };

    /* Mensaje de exito */

    messageProductsPlan = (data) => {
        if (data.success == true) {
            $(".cardAddProductPlans").hide(800);
            $("#formAddProductPlan").trigger("reset");
            
            const idProduct = $('#selectNameProduct').val()

            if (idProduct)
                loadTblProductPlans(idProduct);

            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };
});