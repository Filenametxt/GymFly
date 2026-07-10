<?php
// 1. Carica l'autoloader di Composer
require 'vendor/autoload.php';

// 2. Importa il namespace
use Dompdf\Dompdf;

// 3. Istanzia Dompdf
$dompdf = new Dompdf();

// 4. Carica il tuo HTML
$html = '<h1>Ciao Mondo!</h1><p>Questo è un PDF generato con Dompdf.</p>';
$dompdf->loadHtml($html);

// 5. (Opzionale) Imposta orientamento e formato pagina
$dompdf->setPaper('A4', 'portrait');

// 6. Renderizza l'HTML in PDF
$dompdf->render();

// 7. Output del PDF nel browser    
$dompdf->stream("documento.pdf");
?>