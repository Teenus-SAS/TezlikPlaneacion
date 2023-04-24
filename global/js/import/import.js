$(document).ready(function () {
  importFile = (selectedFile) =>
    new Promise((resolve, reject) => {
      let fileReader = new FileReader();
      fileReader.readAsBinaryString(selectedFile);

      fileReader.onload = (event) => {
        let data = event.target.result;
        let workbook = XLSX.read(data, { type: 'binary' });
        workbook.SheetNames.forEach((sheet) => {
          rowObject = XLSX.utils.sheet_to_row_object_array(
            workbook.Sheets[sheet]
          );
        });
        resolve(rowObject);
      };
    });
});
