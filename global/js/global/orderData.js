$(document).ready(function () {
  const collator = new Intl.Collator('en');
  // sortReference = (x, y) => {
  //   return collator.compare(x.reference, y.reference);
  // };

  // sortNameProduct = (x, y) => {
  //   return collator.compare(x.product, y.product);
  // };
  sortByKey = (key) => {
    return (x, y) => {
      return collator.compare(x[key], y[key]);
    };
  };

  sortFunction = (data, key) => data.sort(sortByKey(key));

  assignOpToGroups = (arr, key) => {
    // Paso 1: Agrupar el array por key
    const grouped = arr.reduce((acc, obj) => {
      const keyValue = obj[key];
      if (!acc[keyValue]) {
        acc[keyValue] = [];
      }
      acc[keyValue].push(obj);
      return acc;
    }, {});

    // Paso 2: Asignar un valor 'op' único a cada grupo
    let opCounter = 1;
    for (const groupKey in grouped) {
      const group = grouped[groupKey];
      const opValue = `OP${opCounter++}`;
      group.forEach(obj => {
        obj.op = opValue;
      });
    }

    // Paso 3: Combinar los resultados en un nuevo array
    return Object.values(grouped).flat();
  };
});
