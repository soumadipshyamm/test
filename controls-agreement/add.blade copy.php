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
