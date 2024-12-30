document.getElementById('saveAndUpload').addEventListener('click', async function () {
    $("#fileInput").hide();

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    const margin = 10;

    const contentDivs = document.querySelectorAll('.mainBody');

    // Apply temporary styles
    contentDivs.forEach(div => Object.assign(div.style, { fontSize: '1.65rem', border: 'none' }));

    for (const div of contentDivs) {
        const canvas = await html2canvas(div, {
            scale: 2,
            useCORS: true,
            backgroundColor: null,
        });

        const imgData = canvas.toDataURL('image/png');
        const imgWidth = pdfWidth - margin * 2;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let y = margin;
        let remainingHeight = imgHeight;

        // Add content across multiple pages if needed
        while (remainingHeight > 0) {
            const renderHeight = Math.min(remainingHeight, pdfHeight - margin * 2);
            pdf.addImage(imgData, 'PNG', margin, y, imgWidth, renderHeight);

            remainingHeight -= renderHeight;

            if (remainingHeight > 0) {
                pdf.addPage();
                y = margin;
            }
        }
    }

    pdf.save("document.pdf");

    // Reset styles
    contentDivs.forEach(div => Object.assign(div.style, { fontSize: '', border: '1px solid #002947' }));

    $("#addAgremmetForm").submit();
});
