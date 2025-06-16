<?php 

class MYPDF extends TCPDF
{
  public function Header(){}
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
    }
}
$data = json_decode($json);
$candaDet = count($data->servicios);
$pageLayout = array(80, 130+($candaDet*10));
$pdf = new MYPDF('P', 'mm', $pageLayout, true, 'UTF-8', false);
//$pdf->SetAutoPageBreak(true, 10); 
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Chuñitos');
$pdf->SetTitle('nota venta');
$pdf->SetSubject('nota venta');
$pdf->SetKeywords('TCPDF, CodeIgniter, PDF, Voucher');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetHeaderMargin(5);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
  require_once(dirname(__FILE__) . '/lang/eng.php');
  $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
  $pdf->SetMargins(3, 3, 3);
  $pdf->SetAutoPageBreak(TRUE, 10);
  $pdf->AddPage();
  $logoWidth = 25;
  $pageWidth = $pdf->getPageWidth(); 
  $logoX = ($pageWidth - $logoWidth) / 2;
  $logoY = 3; 
  $pdf->Image($data->logo, $logoX, $logoY, $logoWidth, '', 'PNG');
  $pdf->Ln($logoWidth);
  $pdf->SetFont('helvetica', 'B', 14);
  $pdf->Cell(0, 5, "$data->empresa", 0, 1, 'C');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->MultiCell(70, 5, "$data->direccion", 0, 'C', false);
  $pdf->Cell(0, 5, "$data->celular", 0, 1, 'C');
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(37, 5, "NIT:", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(37, 5, "$data->nit", 0, 1, 'L');
  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Cell(0, 2, "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -", 0, 1, 'C');
  $pdf->SetFont('helvetica', 'B', 14);
  $pdf->Cell(0, 7, "NOTA DE VENTA N°: $data->numero", 0, 1, 'C');
  
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(37, 5, "FECHA:", 0, 0, 'R');
  $pdf->SetFont('helvetica', '',9);
  $pdf->Cell(37, 5, "$data->fecha $data->hora", 0, 1, 'L');
  
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(37, 5, "Operario:", 0, 0, 'R');
  $pdf->SetFont('helvetica', '',10);
  $pdf->Cell(37, 5, "$data->usuario", 0, 1, 'L');
  
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(37, 5, "Cliente:", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(37, 5, "$data->cliente", 0, 1, 'L');
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(37, 5, "ci:", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(37, 5, "$data->ci_cli", 0, 1, 'L');
  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(37, 5, "telefono:", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(37, 5, "$data->celular_cli", 0, 1, 'L');
  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Cell(0, 2, "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -", 0, 1, 'C');
  $pdf->SetFont('helvetica', 'B', 8);
  $y = $pdf->GetY();
  $pdf->SetXY(3, $y);
  $pdf->Cell(3, 5, "N°", 0, 0, 'C');
  $pdf->Cell(19, 5, "Mascota", 0, 0, 'C');
  $pdf->Cell(19, 5, "Servicio", 0, 0, 'C');
  $pdf->Cell(10, 5, "Precio", 0, 0, 'C');
  $pdf->Cell(10, 5, "Desc.", 0, 0, 'C');
  $pdf->Cell(10, 5, "Total", 0, 1, 'C');
  $pdf->SetFont('helvetica', '', 8);
  $x = $pdf->GetX();
  $y = $pdf->GetY();
  $pdf->SetXY(3, $y);
  $tam = 5;
  foreach($data->servicios as $key => $servicio){
    $yInicial = $pdf->GetY();
    $x = $pdf->GetX();
    $pdf->Cell(2, $tam, $key+1, 0, 0, 'C');
    $pdf->MultiCell(19, $tam, "$servicio->mascota", 0, 'C', false);
    $yFinal = $pdf->GetY(); 
    $pdf->SetXY($x + 21, $yInicial);
    $pdf->MultiCell(19, $tam, "$servicio->servicio", 0, 'C', false);
    $yFinal2 = $pdf->GetY(); 
    $yFinal = $yFinal2>$yFinal?$yFinal2:$yFinal;
    $pdf->SetXY($x + 40, $yInicial);
    $pdf->Cell(10, $tam, number_format($servicio->precio_servicio,2), 0, 0, 'C');
    $pdf->Cell(10, $tam, number_format($servicio->descuento,2), 0, 0, 'C');
    $pdf->Cell(10, $tam, number_format($servicio->total_pagar,2), 0, 1, 'C');
    if((($yFinal-$yInicial)/$tam)-1>=0.4){
        $x = $pdf->GetX();
        $pdf->SetXY($x, $yFinal);
      }
  }
  $pdf->SetFont('helvetica', 'B', 11);
  $pdf->Cell(0, 2, "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -", 0, 1, 'C');

  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(55, 5, "SUBTOTAL: Bs", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(20, 5, number_format($data->sub_total,2), 0, 1, 'L');

  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(55, 5, "DESCUENTO: Bs", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(20, 5, number_format($data->total_descuento,2), 0, 1, 'L');

  $pdf->SetFont('helvetica', 'B', 10);
  $pdf->Cell(55, 5, "TOTAL A PAGAR: Bs", 0, 0, 'R');
  $pdf->SetFont('helvetica', '', 10);
  $pdf->Cell(20, 5, "".number_format($data->total_pagar,2), 0, 1, 'L');
  $pdf->Output('movimiento_caja.pdf', 'I');

?>