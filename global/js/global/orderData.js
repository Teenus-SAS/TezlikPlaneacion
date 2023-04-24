$(document).ready(function () {
  const collator = new Intl.Collator('en');

  sortReference = (x, y) => {
    return collator.compare(x.reference, y.reference);
  };

  sortNameProduct = (x, y) => {
    return collator.compare(x.product, y.product);
  };
});
