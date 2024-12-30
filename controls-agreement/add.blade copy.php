
document.getElementById('download_report').addEventListener('click', async function() { 
    const loaderModal = document.getElementById('loaderModal');
    console.log('Showing loader modal...');
    loaderModal.style.display = 'block'; // Show modal

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    const margin = 10; // Margin to ensure borders are not clipped
    const contentDivs = document.querySelectorAll('.mainBody');

    console.log('PDF page dimensions:', pdfWidth, pdfHeight);

    // Apply temporary styles
    contentDivs.forEach(div => {
        div.style.fontSize = '1.65rem';
        div.style.border = 'none';
    });

    for (const div of contentDivs) {
        const canvas = await html2canvas(div, {
            scale: 2, // Increase scale for better quality
            useCORS: true, // Handle cross-origin images
            backgroundColor: null, // Transparent background
        });

        console.log('Canvas dimensions:', canvas.width, canvas.height);

        const imgData = canvas.toDataURL('image/png');
        const imgWidth = pdfWidth - margin * 2;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        console.log('Image dimensions:', imgWidth, imgHeight);

        let yPosition = margin; // Start position on the page
        let remainingHeight = imgHeight;

        // Handle content overflow across pages
        while (remainingHeight > 0) {
            const renderHeight = Math.min(remainingHeight, pdfHeight - margin * 2);
            console.log('Adding image at Y position:', yPosition, 'with height:', renderHeight);

            pdf.addImage(imgData, 'PNG', margin, yPosition, imgWidth, renderHeight);

            remainingHeight -= renderHeight;

            if (remainingHeight > 0) {
                console.log('Adding new page for remaining content...');
                pdf.addPage(); // Add a new page for remaining content
                yPosition = margin; // Reset position for the new page
            }
        }
    }

    // Reset styles after capturing
    contentDivs.forEach(div => {
        div.style.fontSize = '';
        div.style.border = '1px solid #002947';
    });

    // Save the PDF
    pdf.save("report.pdf");

    console.log('Hiding loader modal...');
    loaderModal.style.display = 'none'; // Hide modal
});





1.****************************aaaaaa
document.getElementById('download_report').addEventListener('click', async function() { 
    const loaderModal = document.getElementById('loaderModal');
    loaderModal.style.display = 'block'; // Show modal

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    const margin = 10; // Margin to ensure borders are not clipped
    const contentDivs = document.querySelectorAll('.mainBody');

    // Apply temporary styles to each element in the NodeList
    contentDivs.forEach(div => {
        div.style.fontSize = '1.65rem';
        div.style.border = 'none';
    });

    let pageNumber = 1; // Initialize page number

    // Loop through each contentDiv and generate a canvas for each one
    for (const div of contentDivs) {
        const canvas = await html2canvas(div, {
            scale: 2, // Increase scale for better image quality
            useCORS: true, // Handle cross-origin images
            backgroundColor: null, // Ensure transparency
            logging: true, // Enable logging for debugging
        });

        const imgData = canvas.toDataURL('image/png');
        const imgWidth = pdfWidth - margin * 2;
        const imgHeight = (canvas.height * imgWidth / canvas.width);

        let yPos = margin;

        // If image is taller than the page, split into multiple pages
        if (imgHeight > pdfHeight - margin * 2) {
            let remainingHeight = imgHeight;

            while (remainingHeight > 0) {
                const renderHeight = Math.min(remainingHeight, pdfHeight - margin * 2);
                pdf.addImage(imgData, 'PNG', margin, yPos, imgWidth, renderHeight);
                remainingHeight -= renderHeight;

                if (remainingHeight > 0) {
                    pdf.addPage(); // Add a new page if content is left
                    yPos = margin;
                }
            }
        } else {
            // If image fits in one page, add it
            pdf.addImage(imgData, 'PNG', margin, yPos, imgWidth, imgHeight);
        }

        if (pageNumber > 1) {
            pdf.addPage(); // Add a new page for next div if there are multiple divs
        }
        pageNumber++;
    }

    // Reset styles after capturing
    contentDivs.forEach(div => {
        div.style.fontSize = '';
        div.style.border = '1px solid #002947';
    });

    // Save the PDF
    pdf.save("report.pdf");

    loaderModal.style.display = 'none'; // Hide modal
});

1.###########*###qqq


document.getElementById('download_report').addEventListener('click', async function() { 
 const loaderModal = document.getElementById('loaderModal');
 loaderModal.style.display = 'block'; // Show modal

const {
jsPDF
} = window.jspdf;
const pdf = new jsPDF('p', 'mm', 'a4');
const pdfWidth = 210; // A4 width in mm
const pdfHeight = 420; // A4 height in mm
const margin = 10; // Margin to ensure borders are not clipped
const contentDivs = document.querySelectorAll('.mainBody');

let pageNumber = 1;

// Apply styles to each element in the NodeList
contentDivs.forEach(div => {
div.style.fontSize = '1.65rem';
div.style.border = 'none';
});

const canvas = await html2canvas(contentDivs[0], { // Use the first element for canvas
scale: 1, // High resolution
useCORS: true, // Handle cross-origin images
backgroundColor: null, // Ensure transparency
logging: true, // Enable logging for debugging
});

// Reset styles after capturing
contentDivs.forEach(div => {
div.style.fontSize = '';
div.style.border = '1px solid #002947';
});

const imgData = canvas.toDataURL('image/png');
const imgWidth = pdfWidth - margin * 2;
const imgHeight = (canvas.height * imgWidth / canvas.width);

pdf.addImage(imgData, 'PNG', margin, margin, imgWidth, imgHeight);
pdf.save("pdf");
loaderModal.style.display = 'none'; // Hide modal
});
