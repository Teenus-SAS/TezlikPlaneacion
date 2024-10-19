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

  // Función para agrupar por una clave específica
  groupBy = (array, key) => {
    const groups = array.reduce((result, currentValue) => {
      const groupKey = currentValue[key];

      // Si no existe el grupo, lo creamos como array vacío
      if (!result[groupKey]) {
        result[groupKey] = [];
      }

      // Añadimos el objeto actual al grupo correspondiente
      result[groupKey].push(currentValue);

      return result;
    }, {}); // El valor inicial es un objeto vacío

    // Convertimos el objeto en un array de arrays
    return Object.values(groups);
  };
});
