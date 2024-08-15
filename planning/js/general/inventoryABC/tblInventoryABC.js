$(document).ready(function () {
  loadAllData = async () => {
    let data = await searchData('/api/inventoryABC');

    if (data.length == 0) {
      let dataInventory = new FormData();

      dataInventory.append('a', 0);
      dataInventory.append('b', 0);
      dataInventory.append('c', 0);
      await sendDataPOST('/api/addInventoryABC', dataInventory);
    }

    sessionStorage.setItem('dataInventoryABC', JSON.stringify(data));

    if (data.length == 0) {
      $('#btnNewInventoryABC').show();
    } else {
      $('#a').val(data[0].a);
      $('#b').val(data[0].b);
      $('#c').val(data[0].c);
    }
  };

  loadAllData();
});
