$(document).ready(function () {
    printPDF = async () => {
        try {
            const elementos = invoice.getElementsByClassName('btnPrintPDF');
            
            while (elementos.length > 0) {
                elementos[0].remove();
            }

            const canvas = await html2canvas(invoice, { scale: 2 });
 
            const imgData = canvas.toDataURL('image/jpeg');
            const bytes = atob(imgData.split(',')[1]).split('').map(char => char.charCodeAt(0));
            const uint8Array = new Uint8Array(bytes);
 
            const pdfDoc = await PDFLib.PDFDocument.create();
            const page = pdfDoc.addPage([canvas.width, canvas.height]);
            const img = await pdfDoc.embedJpg(uint8Array);
            const { width, height } = img.scale(1);
            page.drawImage(img, {
                x: 0,
                y: 0,
                width,
                height,
            });
 
            const pdfBytes = await pdfDoc.save();
            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Orden_Produccion.pdf';
            a.click();
            URL.revokeObjectURL(url);

            let card = document.getElementsByClassName('cardHeader')[0];

            card.insertAdjacentHTML('afterend', `<div class="col-sm-5 col-xl-6 d-flex justify-content-end btnPrintPDF">
                                                        <a href="javascript:;" <i id="btnPrintPDF" class="bi bi-filetype-pdf" data-toggle='tooltip' style="font-size: 30px; color:red;"></i></a>
                                                     </div>`); 
        } catch (error) {
            console.log(error);
        }
    };
});