$(document).ready(function () {
    $(document).on('click', '.move', function (e) {
        let row = $(this).parent().parent().parent().parent()[0];
        let data = tblRoutes.fnGetData(row);
        let allData = tblRoutes.fnGetData();
        let type = getLastText(this.className);
        let index = parseInt(getFirstText(this.className));

        $(this).hide(200);

        let form = document.getElementById(`actionRoute-${index}`);
        form.insertAdjacentHTML(
            'beforeend',
            `<div class="spinner-border spinner-border-sm text-secondary" role="status">
                <span class="sr-only">Loading...</span>
                </div>`
                );
                
        let dataRoute = []; 
        
        // obtener el nuevo valor de route
        if (type == 'up') {
            dataRoute.push({ 'idCiclesMachine': data.id_cicles_machine, 'route': data.route - 1 });
            dataRoute.push({ 'idCiclesMachine': allData[index - 1].id_cicles_machine, 'route': allData[index - 1].route + 1 });
        }
        else {
            dataRoute.push({ 'idCiclesMachine': data.id_cicles_machine, 'route': data.route + 1 });
            dataRoute.push({ 'idCiclesMachine': allData[index + 1].id_cicles_machine, 'route': allData[index + 1].route - 1 });
        }

        $.ajax({
            type: "POST",
            url: "/api/saveRoute",
            data: {data: dataRoute},
            success: function (data) {
                messageRoutes(data); 
            }
        }); 

    });

    const messageRoutes = (data) => {
        $('.cardLoading').remove(); 

        if (data.success == true) {
            updateTable();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla */
    function updateTable() {
        $('#tblRoutes').DataTable().clear();
        $('#tblRoutes').DataTable().ajax.reload();
    }

});