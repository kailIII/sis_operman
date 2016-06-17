<?php
require_once dirname(__FILE__).'/pxpReport/Report.php';

 class CustomReport extends TCPDF {
    
    private $dataSource;
    
    public function setDataSource(DataSource $dataSource) {
        $this->dataSource = $dataSource;
    }
    
    public function getDataSource() {
        return $this->dataSource;
    }
    
    public function Header() {
        $height = 20;
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetXY($x, $y);
        $this->Cell(40, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Image(dirname(__FILE__).'/logo.png', 25, 12, 18);
        
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetFontSize(14);
        $this->SetFont('','B');        
        $this->Cell(170, $height/2, 'REGISTRO', 1, 2, 'C', false, '', 0, false, 'T', 'C');        
        $this->Cell(170,$height/2, 'Control Tarjetas TPM',1,0,'C',false,'',0,false,'T','C');
        
        $this->setXY($x+170,$y);
        $this->SetFont('','');
        $this->Cell(40, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        
        
        $this->SetFontSize(7);
        
        $width1 = 17;
        $width2 = 23;
        $this->SetXY($x+170, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Código:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, $this->dataSource->getParameter('codigo'), "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+170, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Revisión:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, '1', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+170, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Fecha Emision:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, '26/05/2012', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+170, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Página:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4,  '                  '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
    }
    
    public function Footer() {
        $this->SetFontSize(5.5);
		$this->setY(-10);
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		//set style for cell border
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
		$this->Ln(2);
		$cur_y = $this->GetY();
		//$this->Cell($ancho, 0, 'Generado por XPHS', 'T', 0, 'L');
		$this->Cell($ancho, 0, 'Usuario: '.$_SESSION['_LOGIN'], '', 1, 'L');
		$pagenumtxt = 'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
		//$this->Cell($ancho, 0, '', '', 0, 'C');
		$fecha_rep = date("d-m-Y H:i:s");
		$this->Cell($ancho, 0, "Fecha impresión: ".$fecha_rep, '', 0, 'L');
		$this->Ln($line_width);
    }
}

Class RTpm_Tarjeta extends Report {

    function write($fileName) {
        $pdf = new CustomReport('L', PDF_UNIT, "LETTER", true, 'UTF-8', false);
        $pdf->setDataSource($this->getDataSource());
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        //set some language-dependent strings
        
        // add a page
        $pdf->AddPage();
        
        $height = 5;
        $width1 = 15;
        $width2 = 20;
        $width3 = 40;
        $width4 = 100;
        
        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2, $height, 'Localizacion:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($width4, $height, $this->getDataSource()->getParameter('localizacion'), 'B', 0, 'L', false, '', 0, false, 'T', 'C');        
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width1, $height, 'Estacion:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($width3, $height, ($this->getDataSource()->getParameter('tipo')=='Estacion'?'X':''), 'B', 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width1, $height, 'Planta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($width3, $height, ($this->getDataSource()->getParameter('tipo')=='Planta'?'X':''), 'B', 0, 'C', false, '', 0, false, 'T', 'C');
        
        $dataset = $this->getDataSource()->getDataset();
        $pdf->Ln();
        $pdf->Ln();
                
        // end title
        
        $colCount = 0;
        $pdf->SetFontSize(6.5);
        foreach($dataset as $row) {
            
            //Tabla
            $widthColVariable = 30;
            $widthColValor = 50;
            $widthSeparator = 15;
            if($colCount == 0) {
                $pdf->Cell(10, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
                $this->writePair($pdf, $row['nombre'], $widthColVariable, $row['valor'], $widthColValor, 0, $height);
                $pdf->Cell($widthSeparator, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
                $colCount++;
            } else {
                $this->writePair($pdf, $row['nombre'], $widthColVariable, $row['valor'], $widthColValor, 0, $height);
                $pdf->Ln(); 
                $colCount = 0;          
            }
        }
        
        $this->writeDetalles($this->getDataSource()->getParameter('detalleDataSource'), $pdf);
        
        $pdf->Output($fileName, 'F');
    }

    function writePair(TCPDF $pdf, $value1, $widthVal1, $value2, $widthVal2, $pairSeparatorWidth, $height) {
        $pdf->SetFont('', '');
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($widthVal1, $height, $value1, 0, 0, 'L', false, '', 0, false, 'T', 'C');
        if($pairSeparatorWidth > 0) {
            $pdf->Cell($pairSeparatorWidth, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        }
        
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($widthVal2, $height, $value2, 1, 0, 'C', false, '', 0, false, 'T', 'C');
    }        
    
    function writeDetalles (DataSource $dataSource, TCPDF $pdf) {
        $widthMarginLeft = 1;
        $width = 5;
        
        $pdf->Ln();
        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        $height = 10;
        $pdf->SetFillColor(51,51,153, true);
        $pdf->setTextColor(255,255,255);
        
        $pdf->Cell($widthMarginLeft, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width*3, $height, 'Tarjeta Nº', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width*4, $height, 'Detecto', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width*7, $height, 'Equipo', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width*5, $height, 'Descripcion', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width*2, $height, 'Acceso Dificil', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width*2, $height, 'Condicion Insegura', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width*2, $height, 'Contaminacion', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $x = $pdf->getX();
        $y = $pdf->getY();
        $pdf->Cell($width*5, $height/2, 'Fallas', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setXY($x,$y+$height/2);
        $pdf->Cell($width, $height/2, 'ME', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width, $height/2, 'EL', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width, $height/2, 'IST', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width, $height/2, 'NE', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width, $height/2, 'HI', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setXY($x+$width*5,$y);        
        $pdf->Cell($width*3, $height, 'Oportunidad de mejora', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width*4, $height, 'Fecha de Colocacion', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width*4, $height, 'Fecha de Retiro', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width*4, $height, 'Efectuado por', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width*5, $height, 'Observaciones', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Ln();
        $pdf->setTextColor(0,0,0);
        $pdf->SetFontSize(6.5);
        foreach($dataSource->getDataset() as $row) {
            $pdf->Cell($widthMarginLeft, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*3, $height, $row['id_tpm_tarjeta_det'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*4, $height, $row['desc_func_detec'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Cell($width*7, $height, $row['desc_uni_cons'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Cell($width*5, $height, $row['descripcion'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Cell($width*2, $height, $row['acceso_dificil'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*2, $height, $row['cond_inseg'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*2, $height, $row['contaminacion'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width, $height, $row['falla_me'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width, $height, $row['falla_el'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width, $height, $row['falla_ist'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width, $height, $row['falla_ne'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width, $height, $row['falla_hi'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*3, $height, $row['oportunidad_mejora'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*4, $height, $row['fecha_coloc'], 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*4, $height, $row['fecha_retiro'], 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width*4, $height, $row['desc_func_efec'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Cell($width*5, $height, $row['observaciones'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Ln();
        }
    }    
}
?>
