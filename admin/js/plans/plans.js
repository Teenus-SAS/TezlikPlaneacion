$(document).ready(function () {
  $('#NewPlanAccess').click(function (e) {
    e.preventDefault();

    $('#formCreatePlan').trigger('reset');
    $('#createPlansAccess').modal('show');
  });

  // Ocultar Modal Nuevo usuario
  $('#btnClosePlan').click(function (e) {
    e.preventDefault();

    $('#formCreatePlan').trigger('reset');
    $('#createPlansAccess').modal('hide');
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

    const row = $(this).closest("tr")[0];
    let data = tblPlans.fnGetData(row);

    $(`#plan option[value=${data.id_plan}]`).prop('selected', true);
    $('#cantProducts').val(data.cant_products);

    // Datos usuario

    let acces = {
      inventories: data.plan_inventory,
      orders: data.plan_order,
      programs: data.plan_program,
      loads: data.plan_load,
      explosionMaterials: data.plan_explosion_of_material,
      productionOrder: data.plan_production_order,
      offices: data.plan_office,
      store: data.plan_store
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
  });

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataPlan) => {
    let i = 1;

    let access = {
      inventories: 0,
      orders: 0,
      programming: 0,
      loads: 0,
      explosionOfMaterials: 0,
      productionOrder: 0,
      offices: 0,
      store: 0,
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
    if (success) {
      $('#createPlansAccess').modal('hide');
      $('#formCreatePlan').trigger('reset');
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPlans').DataTable().clear();
    $('#tblPlans').DataTable().ajax.reload();
  }
});
