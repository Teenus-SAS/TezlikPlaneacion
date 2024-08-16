$(document).ready(function () {
  /* Cargar data materia prima */
  loadDataMaterial = async () => {
    sessionStorage.removeItem('dataMaterials');
    let data = await searchData('/api/materials');

    let dataMaterials = JSON.stringify(data);
    sessionStorage.setItem('dataMaterials', dataMaterials);

    setSelectsMaterials(data);    
  };
  
  setSelectsMaterials = (data) => {
    let ref = sortFunction(data, 'reference');
    let $select = $(`#refMaterial`);
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(ref, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.reference} </option>`
      );
    });
      
    let name = sortFunction(data, 'material');
    $select = $(`#material`);
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(name, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.material} </option>`
      );
    });
  };

  loadDataMaterial();

  /* Funciones cuando se selecciona una materia prima */

  if (viewRawMaterial == 1) {
    $('#refMaterial').change(function (e) {
      e.preventDefault();
      let id = this.value;

      $('#material option').prop('selected', function () {
        return $(this).val() == id;
      });

      let data = JSON.parse(sessionStorage.getItem('dataMaterials')); 

      let arr = data.find(item => item.id_material == id);

      if (flag_products_measure == '1') {
        let $select = $(`#units`);
        $select.empty();

        $select.append(`<option disabled>Seleccionar</option>`); 
        $select.append(
          `<option value ='${arr.id_unit}' selected> ${arr.unit} </option>`
        );
      } else
        loadUnitsByMagnitude(arr, 2); 
    });

    $('#material').change(function (e) {
      e.preventDefault();
      let id = this.value;

      $('#refMaterial option').prop('selected', function () {
        return $(this).val() == id;
      });

      let data = JSON.parse(sessionStorage.getItem('dataMaterials'));

      let arr = data.find(item => item.id_material == id);

      if (flag_products_measure == '1') {
        let $select = $(`#units`);
        $select.empty();

        $select.append(`<option disabled>Seleccionar</option>`);
        $select.append(
          `<option value ='${arr.id_unit}' selected> ${arr.unit} </option>`
        );
      } else
        loadUnitsByMagnitude(arr, 2);
    });
  } else {
     $('#refMaterial').change(function (e) {
      e.preventDefault();
      let id = this.value;

      $('#material option').prop('selected', function () {
        return $(this).val() == id;
      }); 
       
      let data = JSON.parse(sessionStorage.getItem('dataMaterials'));
      let arr = data.find(item => item.id_material == id);
      $('#abbreviation').val(arr.abbreviation);
    });

    $('#material').change(function (e) {
      e.preventDefault();
      let id = this.value;

      $('#refMaterial option').prop('selected', function () {
        return $(this).val() == id;
      }); 

      let data = JSON.parse(sessionStorage.getItem('dataMaterials'));
      let arr = data.find(item => item.id_material == id);
      $('#abbreviation').val(arr.abbreviation);
    });
  }
});
