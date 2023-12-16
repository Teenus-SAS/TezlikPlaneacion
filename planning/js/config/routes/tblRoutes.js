$(document).ready(function () {
    // Mostrar Tabla planeacion maquinas
    loadTblRoutes = (idProduct) => {
        tblRoutes = $('#tblRoutes').dataTable({
            destroy: true,
            pageLength: 50,
            ajax: {
                url: `/api/routesCiclesMachine/${idProduct}`,
                dataSrc: '',
            },
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
            },
            columns: [
                {
                    title: 'No.',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    title: 'Máquina',
                    data: 'machine',
                    className: 'uniqueClassName',
                },
                {
                    title: '',
                    data: 'id_cicles_machine',
                    className: 'text-center',
                    // render: function (data, type, full, meta) { 
                    //     const moveUpBtn = meta.row > 0 ? `<a href="javascript:;"><i class="bi bi-arrow-up-circle-fill move mt-1 ml-1" id="up" data-index="${meta.row}" style="color: black;"></i></a>` : '';
                    //     const moveDownBtn = meta.row < meta.settings.fnRecordsDisplay() - 1  || meta.row === 0 ?
                    //         `<a href="javascript:;"><i class="bi bi-arrow-down-circle-fill move mt-1 ml-1" id="down" data-index="${meta.row}" style="color: black;"></i></a>` : '';
                        

                    //     return `<div class="btn-group" id="actionRoute" role="group" style="color: black;">
                    //         <h3>${String.fromCharCode(65 + meta.row)}</h3>
                    //         ${moveUpBtn}
                    //         ${moveDownBtn}
                    //     </div>`;
                    // },
                },
            ],
            drawCallback: function (settings) {
                const recordsTotal = tblRoutes.fnSettings().fnRecordsTotal();
                const recordsDisplay = tblRoutes.fnSettings().fnRecordsDisplay();

                $('#tblRoutes tbody tr').each(function (index) {
                    const moveUpBtn = index > 0 ? `<a href="javascript:;" data-index="${index}"><i class="${index} bi bi-arrow-up-circle-fill move mt-1 ml-1 up" style="color: black;"></i></a>` : '';

                    const moveDownBtn = index < recordsDisplay - 1 && index < recordsTotal - 1
                        ? `<a href="javascript:;" data-index="${index}"><i class="${index} bi bi-arrow-down-circle-fill move mt-1 ml-1 down" style="color: black;"></i></a>`
                        : '';
        
                    $(this).find('td:last-child').html(`<div class="btn-group" id="actionRoute-${index}" role="group" style="color: black;">
                                <h3>${String.fromCharCode(65 + index)}</h3>
                                ${moveUpBtn}
                                ${moveDownBtn}
                            </div>`);
                });
            },

        });
    };
});
