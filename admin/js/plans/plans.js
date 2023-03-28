$(document).ready(function () {
  // Ocultar Modal Nuevo usuario
  $('#btnClosePlan').click(function (e) {
    e.preventDefault();
    $('#switchCost').prop('disabled', false);
    $('#switchPlanning').prop('disabled', false);
    $('#formCreatePlan').trigger('reset');

    $('#createPlansAccess').modal('hide');
  });

  /* Accesos de usuario*/
  $('.switch').change(function (e) {
    e.preventDefault();
    if (
      $('#switchCost').is(':checked') &&
      $('#switchPlanning').is(':checked')
    ) {
      $('.cardAccessCost').show(800);
      $('.separator').show(800);
      $('.cardAccessPlanning').show(800);
    } else if ($('#switchCost').is(':checked')) {
      $('.cardAccessCost').show(800);
    } else if ($('#switchPlanning').is(':checked')) {
      $('.cardAccessPlanning').show(800);
    }

    if (!$('#switchCost').is(':checked')) {
      $('.separator').hide();
      $('.cardAccessCost').hide(800);
    }

    if (!$('#switchPlanning').is(':checked')) {
      $('.separator').hide();
      $('.cardAccessPlanning').hide(800);
    }
  });

  $('#btnCreatePlanAccess').click(function (e) {
    e.preventDefault();
    idPlan = sessionStorage.getItem('id_plan');

    dataPlan = {};
    dataPlan['idPlan'] = idPlan;
    dataPlan['cantProducts'] = $('#cantProducts').val();

    dataPlan = setCheckBoxes(dataPlan);

    $.post(
      '/api/updatePlansAccess',
      dataPlan,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  });

  /* Actualizar plan */

  $(document).on('click', '.updatePlanAccess', function (e) {
    let idPlan = this.id;
    sessionStorage.setItem('id_plan', idPlan);

    rol = $('#rol').val();

    if (rol == 1) {
      $('#switchCost').prop('checked', true);
      $('#switchPlanning').prop('disabled', true);
      $('.cardAccessCost').show(800);
      $('.separator').hide();
      $('.cardAccessPlanning').hide();
      $('.inputCantProducts').show();
    }
    if (rol == 2) {
      $('#switchPlanning').prop('checked', true);
      $('#switchCost').prop('disabled', true);
      $('.cardAccessPlanning').show(800);
      $('.separator').hide();
      $('.cardAccessCost').hide();
      $('.inputCantProducts').hide();
    }

    setData(idPlan);
  });

  setData = async (idPlan) => {
    dataPlan = await loaddataAccess();

    for (let i = 0; i < dataPlan.length; i++) {
      if (dataPlan[i]['id_plan'] == idPlan) {
        data = dataPlan[i];
        break;
      }
    }

    $(`#plan option[value=${data.id_plan}]`).prop('selected', true);
    $('#cantProducts').val(data.cant_products);

    // Datos usuario

    let acces = {
      //costos
      prices: data.cost_price,
      analysisMaterials: data.cost_analysis_material,
      economyScale: data.cost_economy_scale,
      multiproduct: data.cost_multiproduct,
      quotes: data.cost_quote,
      support: data.cost_support,

      //Planeacion
      inventories: data.plan_inventory,
      orders: data.plan_order,
      programs: data.plan_program,
      loads: data.plan_load,
      explosionMaterials: data.plan_explosion_of_material,
      offices: data.plan_office,
    };

    let i = 1;

    $.each(acces, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });

    $('#createPlansAccess').modal('show');
    $('#btnCreatePlanAccess').html('Actualizar Accesos');
  };

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataPlan) => {
    let i = 1;

    let access = {
      prices: 0,
      analysisRawMaterials: 0,
      economyScale: 0,
      multiproduct: 0,
      quotes: 0,
      support: 0,
      inventories: 0,
      orders: 0,
      programming: 0,
      loads: 0,
      explosionOfMaterials: 0,
      offices: 0,
    };

    $.each(access, (index, value) => {
      if ($(`#checkbox-${i}`).is(':checked')) dataPlan[`${index}`] = 1;
      else dataPlan[`${index}`] = 0;
      i++;
    });

    return dataPlan;
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('#createPlansAccess').modal('hide');
      $('#formCreatePlan').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#rol').trigger('change');
  }
});
