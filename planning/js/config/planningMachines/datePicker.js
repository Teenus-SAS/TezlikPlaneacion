$(document).ready(function () {
  /* Horas */
  $('#hourStartPicker').datetimepicker({ format: 'HH:mm A' });
  $('#hourEndPicker').datetimepicker({ format: 'HH:mm A' });

  /* Meses */
  let date = new Date();
  const festivos = [
    [10], // Enero (Sabado 1)
    [], // Febrero
    [21], // Marzo
    [14, 15], // Abril
    [1, 30], // Mayo
    [20, 27], // Junio
    [4, 20], // Julio
    [7, 15], // Agosto
    [], // Septiembre
    [17], // Octubre
    [7, 14], // Noviembre
    [8, 25], // Diciembre
  ];

  // General
  $('.month').on('blur', function (e) {
    id = this.id;
    day = this.value;
    month = parseInt(id.slice(6));

    if (day == 0) {
      $(`#${id}`).val('');
      return false;
    }

    businessDays = sessionStorage.getItem('businessDays');
    businessDays = JSON.parse(businessDays);

    if (day > businessDays[month]) {
      message = 'El valor es mayor al ultimo dia';
      showError(message, id);
      return false;
    }
  });

  getBusinessDays = (days, month) => {
    businessDays = days;

    for (j = 1; j < days; j++) {
      businessDate = new Date(date.getFullYear(), month, j);
      nameDay = businessDate.toLocaleDateString('es-CO', { weekday: 'long' });

      if (nameDay == 'sábado' || nameDay == 'domingo')
        businessDays = businessDays - 1;

      for (let d in festivos[month]) {
        if (j == festivos[month][d]) {
          businessDays = businessDays - 1;
        }
      }
    }

    return businessDays;
  };

  showError = (message, id) => {
    toastr.error(message);
    $(`#${id}`).css('border-color', 'red');
    $(`#${id}`).val('');
  };

  $(document).on('blur','.hours', function () {
    let hourStart = $('#hourStart').val();
    let hoursDay = parseFloat($('#hoursDay').val());

    if (!hourStart || hourStart == '') {
      toastr.error('Ingrese hora inicial');
      return false;
    }
    
    if (!hoursDay || hoursDay == '') {
      return false; 
    }

    var date = new Date("2000-01-01 " + hourStart);

    var horas = date.getHours();
    var minutos = date.getMinutes();
 
    var hourEnd = parseFloat(horas + '.' + (minutos < 10 ? '0' : '') + minutos) + hoursDay;

    hourEnd = moment(hourEnd.toFixed(2), ['HH:mm']).format('h:mm A');

    $('#hourEnd').val(hourEnd);
  });
});
