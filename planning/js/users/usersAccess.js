$(document).ready(function () {
  /* ACCESOS DE USUARIO 
  $.ajax({
    type: 'POST',
    url: '/api/planningUserAccess',
    success: function (resp) {
      let acces = {
        inventories: resp.inventory,
        orders: resp.plan_order,
        programs: resp.program,
        loads: resp.plan_load,
        explosionMaterials: resp.explosion_of_material,
        offices: resp.office,
        invMolds: resp.create_mold,
        planProducts: resp.create_product,
        planMaterials: resp.create_material,
        planMachines: resp.create_machine,
        planProcess: resp.create_process,
        planProductsMaterials: resp.products_material,
        planProductProcess: resp.products_process,
        planningMachines: resp.programs_machine,
        planCiclesMachine: resp.cicles_machine,
        categories: resp.inv_category,
        sales: resp.sale,
        planUsers: resp.user,
        clients: resp.client,
        typeOrder: resp.orders_type,
      };

      $.each(acces, (index, value) => {
        if (value === 0) {
          $(`.${index}`).remove();
        }
      });
      if (
        acces.invMolds == 0 &&
        acces.planProducts == 0 &&
        acces.planMaterials == 0 &&
        acces.planMachines == 0 &&
        acces.planProcess == 0
      ) {
        $('#navPlanBasics').remove();
      }

      if (
        acces.planProductsMaterials == 0 &&
        acces.planProductProcess == 0 &&
        acces.planningMachines == 0 &&
        acces.planCiclesMachine == 0
      ) {
        $('#navPlanSetting').remove();
      }

      if (acces.categories == 0 && acces.sales == 0) {
        $('#navPlanGeneral').remove();
      }

      if (acces.planUsers == 0 && acces.clients == 0 && acces.typeOrder == 0) {
        $('#navPlanAdmin').remove();
      }
    },
  }); */
});
