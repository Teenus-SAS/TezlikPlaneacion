$(document).ready(function () {
    $('#btnExportStore').click(function (e) {
        e.preventDefault();
 
        let table = document.getElementById('tblStore');
        let rows = table.querySelectorAll('tr');
        let datos = [];

        rows.forEach(function (row) {
            let cells = row.querySelectorAll('td');

            cells.length == 0 ? cells = row.querySelectorAll('th') : cells;

            let arr = [];

            for (let i = 1; i < cells.length - 1; i++) {

                // if (i != 3)
                    arr.push(cells[i].textContent);
            }
            datos.push(arr);
        });
 
        let wb = XLSX.utils.book_new();

        let ws = XLSX.utils.aoa_to_sheet(datos);
        XLSX.utils.book_append_sheet(wb, ws, 'Almacen');

        XLSX.writeFile(wb, 'Almacen.xlsx');
    });
});