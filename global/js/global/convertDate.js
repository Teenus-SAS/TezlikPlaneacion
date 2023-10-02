$(document).ready(function () {
    convetFormatDateTime = (date) => { 
        var fecha = new Date(date);

        var año = fecha.getFullYear();
        var mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
        var día = ('0' + fecha.getDate()).slice(-2);
        var hora = ('0' + fecha.getHours()).slice(-2);
        var minutos = ('0' + fecha.getMinutes()).slice(-2); 

        var fechaFormateada = año + '-' + mes + '-' + día + 'T' + hora + ':' + minutos;

        return fechaFormateada;
    };

    convetFormatDate = (date) => { 
        var fecha = new Date(date);

        var año = fecha.getFullYear();
        var mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
        var día = ('0' + fecha.getDate()).slice(-2); 

        // Formatear la fecha en el nuevo formato
        var fechaFormateada = año + '-' + mes + '-' + día;

        return fechaFormateada;
    };
});