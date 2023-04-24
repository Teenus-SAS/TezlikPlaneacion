$(document).ready(function () {
  $('.number').on({
    focus: function (event) {
      $(event.target).select();
    },
    keyup: function (event) {
      $(event.target).val(function (index, value) {
        if (!/[0-9]/.test(value)) {
          event.preventDefault();
          return '';
        }

        number = value;

        while (number.includes('.')) {
          number = number.replace('.', '');
        }

        number = decimalNumber(number);

        return number;
      });
    },
  });

  $('.numberCalc').on({
    focus: function (event) {
      $(event.target).select();
    },
    keyup: function (event) {
      $(event.target).val(function (index, value) {
        if (!/[0-9]/.test(value)) {
          event.preventDefault();
          return '';
        }

        number = value
          .replace(/\./g, '')
          .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.');

        return number;
      });
    },
  });

  $('.money').on({
    focus: function (event) {
      $(event.target).select();
    },
    keyup: function (event) {
      $(event.target).val(function (index, value) {
        return (
          value
            .replace(/\D/g, '')
            //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.')
        );
      });
    },
  });

  /* Devolver numero formato 0.000.000,0 */
  function formatNumber(floatValue = 0, decimals = 0, multiplier = 1) {
    let floatMultiplied = floatValue * multiplier;
    let stringFloat = floatMultiplied + '';
    let arraySplitFloat = stringFloat.split('.');
    let decimalsValue = '0';
    if (arraySplitFloat.length > 1) {
      decimalsValue = arraySplitFloat[1].slice(0, decimals);
    }
    let integerValue = arraySplitFloat[0];
    let arrayFullStringValue = [integerValue, decimalsValue];
    let FullStringValue = arrayFullStringValue.join('.');
    let floatFullValue = parseFloat(FullStringValue) + '';
    let formatFloatFullValue = new Intl.NumberFormat(undefined, {
      minimumFractionDigits: decimals,
    }).format(floatFullValue);
    return formatFloatFullValue;
  }

  /* Quitar miles y decimales del numero */
  strReplaceNumber = (num) => {
    while (num.includes('.')) {
      num = num.replace('.', '');
    }
    num = num.replace(',', '.');
    return num;
  };

  /* Validar si es entero o decimal */
  validateNumber = (number) => {
    if (number.isInteger) number = number.toLocaleString('es-CO');
    else
      number = number.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

    return number;
  };

  /* Devolver cantidad de decimales */
  decimalNumber = (number) => {
    let decimals = 0;
    dnumber = number;

    while (dnumber.includes(',')) {
      dnumber = dnumber.slice(1, dnumber.length);
      if (dnumber == '') {
        decimals = '';
        break;
      }
      decimals = dnumber.length;
    }

    if (decimals == '' && typeof decimals === 'string') return number;

    number = number.replace(',', '.');
    number = formatNumber(parseFloat(number), decimals);
    return number;
  };
});
