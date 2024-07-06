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
    convetFormatDateTime1 = (date) => {
        var fecha = new Date(date);

        var año = fecha.getFullYear();
        var mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
        var día = ('0' + fecha.getDate()).slice(-2);
        var hora = ('0' + fecha.getHours()).slice(-2);
        var minutos = ('0' + fecha.getMinutes()).slice(-2);

        var fechaFormateada = año + '-' + mes + '-' + día + ' ' + hora + ':' + minutos;

        return fechaFormateada;
    };

    convetFormatDate = (date) => {
        var fecha = new Date(date + 'T00:00:00');

        var año = fecha.getFullYear();
        var mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
        var día = ('0' + fecha.getDate()).slice(-2);

        // Formatear la fecha en el nuevo formato
        var fechaFormateada = año + '-' + mes + '-' + día;

        return fechaFormateada;
    };

    formatDate = (date) => {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    getLastDayOfMonth = (year, month) => {
        return new Date(year, month, 0).getDate();
    };
});