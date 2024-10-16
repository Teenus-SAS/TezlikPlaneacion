$(document).ready(function () {
  
  loadUserAccess = async () => {
    let data = await searchData('/api/planningUserAccess');

    let access = {
      planProducts: data.create_product, 
      planMachines: data.create_machine,
      planPayroll: data.payroll,
      planProductsMaterials: data.products_material,
      planningMachines: data.programs_machine,
      planStock: data.stock,
      planCalendar: data.calendar,
      planClients: data.client,
      planSellers: data.seller, 
      planSales: data.sale, 
      planUsers: data.user, 
      planInventories: data.inventory,
      planRequisitions: data.requisition,
      planOrders: data.plan_order,
      planPrograms: data.program, 
      planExplosionMaterials: data.explosion_of_material,
      planProductionOrder: data.production_order,
      planOffices: data.office,
      planStore: data.store,
    }

    $.each(access, (index, value) => {
      if (value == 0) {
        $(`.${index}`).hide();
      } else
        $(`.${index}`).show();
    });

    if (
      access.planProducts == 0 && 
      access.planMachines == 0 
    ) {
      $('#navPlanBasics').hide();
    } else
      $('#navPlanBasics').show();

    if (
      access.planProductsMaterials == 0 &&
      access.planningMachines == 0 &&
      access.planStock == 0 &&
      access.planCalendar == 0
    ) {
      $('#navPlanSetting').hide();
    } else
      $('#navPlanSetting').show();

    if (
      access.planClients == 0 &&
      access.planSellers == 0 &&
      access.planSales == 0
    ) {
      $('#navPlanGeneral').hide();
    } else
      $('#navPlanGeneral').show();

    if (
      access.planUsers == 0 
    ) {
      $('#navPlanAdmin').hide();
    } else
      $('#navPlanAdmin').show();
  }
});
