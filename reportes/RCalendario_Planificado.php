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
        $this->SetFontSize(12);
        $this->SetFont('','B');        
        $this->Cell(245, $height/2, 'PLAN', 1, 2, 'C', false, '', 1, false, 'T', 'C');        
        $this->setXY($x,$y+$height/2);
		$this->Cell(245, $height/2, 'Programa de Mantenimiento',1,0,'C',false,'',1,false,'T','C');
        //$this->Cell(245, $height/2, 'ESTACION '.strtoupper($this->getDataSource()->getParameter('localizacion')),1,0,'C',false,'',1,false,'T','C');
                
        $this->setXY($x+245,$y);
        $this->SetFont('','');
        $this->Cell(40, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFontSize(7);
        
        $width1 = 17;
        $width2 = 23;
        $this->SetXY($x+245, $y);
        $this->setCellPaddings(2);
        $this->SetFont('','B');
        $this->Cell($width1, $height/4, 'Código:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->Cell($width2, $height/4, 'GMAN-PL-SM-001', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+245, $y);
        $this->setCellPaddings(2);
        $this->SetFont('','B');
        $this->Cell($width1, $height/4, 'Revisión:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->Cell($width2, $height/4, '1', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+245, $y);
        $this->setCellPaddings(2);
        $this->SetFont('','B');
        $this->Cell($width1, $height/4, 'Fecha de Emisión:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->Cell($width2, $height/4, '26/05/2012', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+245, $y);
        $this->setCellPaddings(2);
        $this->SetFont('','B');
        $this->Cell($width1, $height/4, 'Pagina:', "B", 0, '', false, '', 0, false, 'T', 'C');
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

Class RCalendarioPlanificado extends Report {
	
				private $ano=NULL;

    function write($fileName) {
        $pdf = new CustomReport('L', PDF_UNIT, "LEGAL", true, 'UTF-8', false);
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
        $width3 = 30;
        $width4 = 75;
        
        $dataset = $this->getDataSource()->getDataset();
								
								if(count($dataset)!==0){
			        //$ano=NULL;
			        foreach ($dataset[0] as $key => $value) {
			            if(preg_match("/^c+.+[1-4]$/",$key))                
			                $recuperado = explode('_', $key);
			            $this->ano=$recuperado[2];
			        }
			        
			        $pdf->SetFontSize(7.5);
			        $pdf->SetFont('', 'B');                
			        $pdf->setTextColor(0,0,0);
			        $pdf->Cell($height, $height, '', 0, 0, 'R', false, '', 0, false, 'T', 'C');
			        $pdf->Cell($width1, $height, 'Estación:', 0, 0, 'R', false, '', 0, false, 'T', 'C');
			        $pdf->SetFont('', 'B');
			        $pdf->setTextColor(51,51,153);
			        $pdf->Cell($width2, $height, $this->getDataSource()->getParameter('localizacion'), 'B', 0, 'L', false, '', 0, false, 'T', 'C');
			        $pdf->Cell($width3+$width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			        $pdf->SetFont('', 'B');
			        $pdf->SetFillColor(51,51,153, true);
			        $pdf->setTextColor(255,255,255);
			        $pdf->Cell($width4*3+$width1, $height, 'GESTION '.$this->ano, 1, 0, 'C', true, '', 0, false, 'T', 'C');
			        
			        $this->writeDetalles($this->getDataSource(), $pdf);
			     }else{
			     			$pdf->Cell($width2, $height, 'NO EXISTE NADA PLANIFICADO', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			     }
        $pdf->Output($fileName, 'F');
    }
    
    function writeDetalles (DataSource $dataSource, TCPDF $pdf) {
        $widthMarginLeft = 1;
        $width = 5;
        $width1 = 5;
        $width2 = 10;
        $width3 = 30;
        $pdf->Ln();
        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        $height = 10;
        $pdf->SetFillColor(144,190,53, true);
        $pdf->setTextColor(0,0,0);
        
        //$pdf->Cell($widthMarginLeft, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width1, $height, 'Nro.', 1, 0, 'C', true, '', 1, false, 'T', 'C');        
        $pdf->Cell($width2+$width1, $height, 'Cod. Maquina', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width2*2, $height, 'Frecuencia', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        //$pdf->Cell($width2, $height, 'Contador', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell($width3+$width2+$width1, $height, 'Tarea', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $x=$pdf->getX();
        $y=$pdf->getY();
        $pdf->SetFillColor(140,190,230, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Enero', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(255,190,130, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Febrero', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(140,190,230, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Marzo', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(255,190,130, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Abril', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(140,190,230, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Mayo', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(255,190,130, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Junio', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(140,190,230, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Julio', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(255,190,130, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Agosto', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(140,190,230, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Septiembre', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(255,190,130, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Octubre', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(140,190,230, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Noviembre', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->SetFillColor(255,190,130, true);
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*2, $height/2, 'Diciembre', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setXY($x,$y+$height/2);
        
        for ($j=0; $j < 6; $j++) { 
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            for ($i=1; $i <5 ; $i++) { 
                $pdf->Cell($width2/2, $height/2, $i, 1, 0, 'C', true, '', 0, false, 'T', 'C');        
            }
            
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            for ($i=1; $i <5 ; $i++) { 
                $pdf->Cell($width2/2, $height/2, $i, 1, 0, 'C', true, '', 0, false, 'T', 'C');        
            }            
        }
        
                        
        $pdf->Ln();
        $pdf->setTextColor(0,0,0);
        $pdf->SetFontSize(6.5);
        //$colorRojo=array(255,0,0);
        //$style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
         $cont=1;
        foreach($dataSource->getDataset() as $row) {
            //$pdf->Cell($widthMarginLeft, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width1, $height/2, $cont, 1, 0, 'L', false, '', 0, false, 'T', 'C');
            $pdf->Cell($width2+$width1, $height/2, $row['codigo_equipo'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Cell($width2*2, $height/2, $row['frecuencia'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            //$pdf->Cell($width2, $height/2, '', 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->Cell($width3+$width2+$width1, $height/2, $row['nombre_mant'], 1, 0, 'L', false, '', 1, false, 'T', 'C');
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['january_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['january_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['january_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['january_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['february_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['february_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['february_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['february_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['march_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['march_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['march_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['march_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['april_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['april_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['april_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['april_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['may_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['may_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['may_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['may_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['june_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['june_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['june_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['june_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['july_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['july_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['july_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['july_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['august_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['august_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['august_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['august_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            $x=$pdf->getX();
            $y=$pdf->getY();
            $pdf->Cell($width2/2, $height/2, ($row['september_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['september_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['september_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['september_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['october_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['october_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['october_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['october_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(140,190,230, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['november_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['november_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['november_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['november_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->SetFillColor(255,190,130, true);
            $pdf->setTextColor(0,0,0);
            $pdf->Cell($width2/2, $height/2, ($row['december_'.$this->ano.'_s1']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['december_'.$this->ano.'_s2']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['december_'.$this->ano.'_s3']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $pdf->Cell($width2/2, $height/2, ($row['december_'.$this->ano.'_s4']==1)?'X':'', 1, 0, 'C', true, '', 0, false, 'T', 'C');
            $cont++;
            $pdf->Ln();
        }
    }
  
}
?>
