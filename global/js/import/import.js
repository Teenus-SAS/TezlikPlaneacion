$(document).ready(function () {
  // importFile = (selectedFile) =>
  //   new Promise((resolve, reject) => {
  //     let fileReader = new FileReader();
  //     fileReader.readAsBinaryString(selectedFile);

  //     fileReader.onload = (event) => {
  //       let data = event.target.result;
  //       let workbook = XLSX.read(data, { type: 'binary' });
  //       workbook.SheetNames.forEach((sheet) => {
  //         rowObject = XLSX.utils.sheet_to_row_object_array(
  //           workbook.Sheets[sheet]
  //         );
  //       });
  //       resolve(rowObject);
  //     };
  //   });
  importFile = (selectedFile) =>
  new Promise((resolve, reject) => {
    let fileReader = new FileReader();
    fileReader.readAsBinaryString(selectedFile);

    fileReader.onload = (event) => {
      let data = event.target.result;
      let workbook = XLSX.read(data, { type: 'binary' });
      let rowObject = [];
      
      workbook.SheetNames.forEach((sheet) => {
        // Obtenemos los datos de la hoja en forma de objetos
        let sheetData = XLSX.utils.sheet_to_json(workbook.Sheets[sheet], { raw: false });
        
        // Recorremos cada fila del objeto
        sheetData.forEach(row => {
          for (let key in row) {
            let cellValue = row[key];
            
            // Detectar si el valor de la celda es una hora almacenada como número en Excel
            if (typeof cellValue === 'number' && cellValue >= 0 && cellValue < 1) {
              // Excel almacena horas como fracciones del día, multiplicamos por 24 para obtener la hora
              let hours = Math.floor(cellValue * 24);
              let minutes = Math.floor((cellValue * 24 * 60) % 60);
              
              // Creamos un objeto Date para facilitar el formateo
              let time = new Date(0, 0, 0, hours, minutes);
              
              // Convertimos la hora a formato de 12 horas con AM/PM
              let formattedTime = time.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });

              // Reemplazamos el valor de la celda con el formato de hora
              row[key] = formattedTime;
            }
          }
        });

        rowObject = sheetData; // Asignar los datos procesados al rowObject
      });

      resolve(rowObject);
    };

    fileReader.onerror = (error) => {
      reject(error);
    };
  });
});
