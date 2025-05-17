import * as XLSX from 'xlsx';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import { Title } from 'chart.js';
import { dateToLocal } from './useFormato';
import { toZonedTime } from 'date-fns-tz';
import { format } from 'date-fns';

const currentDate = toZonedTime(new Date(), 'America/Mexico_City');

export function exportToExcel(fileName, data, headers, values) {
    const tableData = data.map(item => {
        const rowData = {};
        values.forEach((value, index) => {
            if (typeof value === 'function') {
                rowData[headers[index]] = value(item);
            } else {
                rowData[headers[index]] = value.split('.').reduce((o, i) => o[i], item);
            }
        });
        return rowData;
    });

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(tableData);
    XLSX.utils.book_append_sheet(wb, ws, 'Data');
    XLSX.writeFile(wb, (fileName + '_' + format(currentDate, 'yyyyMMdd')) + '.xlsx');
};

export function exportToPDF(fileName, title, data, headers, values) {
    const doc = new jsPDF('landscape');

    const tableData = data.map((item, index) => {
        const rowData = values.map(value => {
            if (typeof value === 'function') {
                return value(item);
            } else {
                return value.split('.').reduce((o, i) => o[i], item);
            }


        });
        return [index + 1, ...rowData];
    });

    const updatedHeaders = ['N°', ...headers];
    const currentDate = dateToLocal(new Date(), true)
    const logo = new Image();
    logo.src = "/img/conagua.png"; // Logo
    doc.addImage(logo, 230, 10, 50, 20);
    doc.setFontSize(14);
    doc.setTextColor(0, 0, 0);
    doc.text(title, 15, 17);
    doc.text(currentDate, 15, 22);
    doc.setTextColor(0, 0, 0);

    doc.autoTable({
        head: [updatedHeaders],
        body: tableData,
        startY: 35,
        theme: 'grid',
        styles: { fontSize: 10, cellPadding: 2 },
        bodyStyles: {
            valign: "top",
            halign: "center",
            cellPadding: 1.5,
            fontSize: 5,
        },
        headStyles: {
            cellPadding: 1.5,
            fontSize: 6,
            fillColor: [190, 150, 91],
            halign: "center",
            textColor: "10",
        },
    });

    doc.save((fileName + '_' + format(currentDate, 'yyyyMMdd')) + '.pdf');
}



