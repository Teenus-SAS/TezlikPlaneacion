$(document).ready(function () {
  /* Cargar data materia prima */
  loadDataMaterial = async () => {
    sessionStorage.removeItem('dataMaterials');
    let data = await searchData('/api/materials');

    let dataMaterials = JSON.stringify(data);
    sessionStorage.setItem('dataMaterials', dataMaterials);

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

      let data = sessionStorage.getItem('dataMaterials');
      if (data) {
        dataMaterials = JSON.parse(data);
        sessionStorage.removeItem('dataMaterials');
      }

      for (i = 0; i < dataMaterials.length; i++) {
        if (id == dataMaterials[i].id_material) {
          loadUnitsByMagnitude(dataMaterials[i], 2);
        }
      }
    });

    $('#material').change(function (e) {
      e.preventDefault();
      let id = this.value;

      $('#refMaterial option').prop('selected', function () {
        return $(this).val() == id;
      });

      let data = sessionStorage.getItem('dataMaterials');
      if (data) {
        dataMaterials = JSON.parse(data);
        sessionStorage.removeItem('dataMaterials');
      }

      for (i = 0; i < dataMaterials.length; i++) {
        if (id == dataMaterials[i].id_material) {
          loadUnitsByMagnitude(dataMaterials[i], 2);
        }
      }
    });
  }
});
