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
});
