import { format } from "date-fns";
import { toZonedTime } from "date-fns-tz";

const timeZone = 'America/Mexico_City';

// Formato numerico
export function useNFmt(num, dec=2)
{
    return Number(num).toFixed(dec).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}


// Formato de Fecha
export function useDFmt(fecha)
{
    if(fecha === null)
        return '';
    return moment(fecha).format('DD/MM/YYYY');
}

export function useDBF(fecha)
{
    if(fecha === null)
        return '';
    console.log(fecha);
    return moment(fecha).format('YYYY-MM-DD');
}

export function monthName(monthNumber)
{
    return moment(monthNumber, 'MM').format('MMMM');
}

export function usePeriodName(p)
{
    if(p.id_periodicidad === 1)
    {
        return 'QUINCENA: ' + p.consec + ' DE ' + p.id_ejercicio;
    }
    else
    {
        return moment(p.mes, 'MM').format('MMMM').toUpperCase() + ' DE ' + p.id_ejercicio;
    }
}

// FORMAT DATE IN LL
export function useFormatDate(date) {
    if(date === '9999-12-31')
        return 'Actual';
    moment.defaultFormat = 'es-mx';
    return moment(date).format('ll');
}


// FORMAT DATE IN LL
export function useFormatDateTime(date) {
    if(date === '9999-12-31')
        return 'Actual';
    moment.defaultFormat = 'es-mx';
    return moment(date).format('lll');
}

// FORMATEAR NÚMERO
export const useFormatNumber = (importe_autorizado) => {
    const numberFormat = new Intl.NumberFormat('es-MX');
    return numberFormat.format(importe_autorizado);
}

// Limitar texto
export const limitText = (text, size) => {
    if (text.length > size) {
        return text.slice(0, size) + '...';
    }
    return text;
};

export function dateToLocal(date, isComplete) {
    if (!date) return "";
    const dateUtc = new Date(date);
    const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    };
    const formattedDate = dateUtc.toLocaleDateString("es-MX", options);
    const [year, month, day, hour, minute] = formattedDate.split(/[/,\s-]/);
    if (isComplete) {
        return `${day}-${month}-${year} ${hour}:${minute}`;
    }
    return `${day}-${month}-${year}`;
}

export function currentDate(formatString) {
    const date = toZonedTime(new Date(), timeZone)
    return format(date, formatString);
}
