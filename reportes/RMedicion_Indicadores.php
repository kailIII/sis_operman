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
        $this->Cell(115, $height/2, 'REGISTRO', 1, 2, 'C', false, '', 0, false, 'T', 'C');        
        $this->Cell(115,$height/2, 'Indicadores de Mantenimiento',1,0,'C',false,'',0,false,'T','C');
        
        $this->setXY($x+115,$y);
        $this->SetFont('','');
        $this->Cell(40, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        
        
        $this->SetFontSize(7);
        
        $width1 = 17;
        $width2 = 23;
        $this->SetXY($x+115, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Código:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, 'GMAN-RG-SM-028', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+115, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Revisión:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, '1.0', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+115, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Fecha Emision:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, '26/05/2012', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+115, $y);
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

Class RMedicionIndicadores extends Report {
        
    private $totalNumParos=0;
	private $totalNumParosPl=0;
    private $totalTiempoOpHrs=0.00;
    private $totalTiempoStandByHrs=0.00;
    private $totalTiempoMnpHrs=0.00;
    private $totalTiempoMppHrs=0.00;
    private $dias=0; 
    private $numDias=0;
    private $mesLiteral='';
    private $anio=0;
    private $maximo=1;
    private $widthCelda;
    private $heightCelda;
    private $puntoOrigenX;
    private $puntoOrigenY;   

    function write($fileName) {
        $pdf = new CustomReport('P', PDF_UNIT, "LETTER", true, 'UTF-8', false);
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
        $width0 = 13;
        $width1 = 15;
        $width2 = 20;
        $width3 = 30;
                
        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2, $height, 'Localizacion:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($width3*2, $height, $this->getDataSource()->getParameter('localizacion'), 'B', 0, 'L', false, '', 0, false, 'T', 'C');        
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2, $height, 'Sistema:', 0, 0, 'R', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($width3+5, $height, $this->getDataSource()->getParameter('sistema'), 'B', 0, 'L', false, '', 0, false, 'T', 'C');        
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2, $height, 'Tag:', 0, 0, 'R', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->setTextColor(51,51,153);
        $pdf->Cell($width3, $height, $this->getDataSource()->getParameter('codigo'), 'B', 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln();
        $pdf->Ln();
                
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width1+$width0+3, $height, '', 0, 0, 'R', false, '', 0, false, 'T', 'C');
        $pdf->SetFont('', 'B');
        $pdf->SetFillColor(175,238,238, true);
        $pdf->Cell(($width0*4)-4, $height, 'Tiempo en Horas', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Ln();      
        $pdf->Cell($width1-4, $height, 'MES', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setTextColor(0,0,255);
        $pdf->Cell($width0-3, $height, 'PAROS NPL', '1', 0, 'C', true, '', 1, false, 'T', 'C');
		$pdf->Cell($width0-3, $height, 'PAROS PL', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setTextColor(208,32,144);
        $pdf->Cell($width0-1, $height, 'OPERATIVO', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setTextColor(255,215,0);
        $pdf->Cell($width0-1, $height, 'STAND BY', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setTextColor(255,0,0);
        $pdf->Cell($width0-1, $height, 'MNP', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setTextColor(0,100,0);
        $pdf->Cell($width0-1, $height, 'MPP', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setTextColor(0,0,0);
        $x=$pdf->getX();
        $y=$pdf->getY();        
        $pdf->Cell(120, $height, 'CALCULO DE INDICADORES', '0', 0, 'C', false, '', 1, false, 'T', 'C');        
        $pdf->Ln();
        
        $this->writeDetalles($this->getDataSource(), $pdf);
        /*
        $pdf->SetFillColor(175,238,238, true);
        $pdf->setXY($x+10,$y+$height);
        $pdf->Cell(45, $height*2, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(45, $height*2, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->setXY($x+10,$y+$height*4);
        $pdf->SetFillColor(0,0,255, true);
        $pdf->Cell(45, $height*1, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*1, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(45, $height*1, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        */
        /*
        $pdf->setXY($x+10,$y+$height*7);
        $pdf->SetFillColor(175,238,238, true);
        $pdf->Cell(45, $height*2, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(45, $height*2, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        */
		$pdf->setXY($x+10,$y+$height*10);        
        $pdf->SetFillColor(0,0,255, true);
        //$pdf->Cell(45, $height*1, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*1, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        //$pdf->Cell(45, $height*1, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');        
        
        $pdf->setXY($x+10,$y+$height);
        //CALCULANDO DATOS
        if($this->dias!==0){        
            $disponibilidad = round((($this->dias*24) - $this->totalTiempoMnpHrs - $this->totalTiempoMppHrs)*100/($this->dias*24),2);
			if($this->totalNumParos==0){
				$tmef = 0;
				$tmpr = 0;
				if($tmef+$tmpr==0){
					$confiabilidad = 100;
				} else{
					$confiabilidad = round(($tmef*100)/($tmef+$tmpr),2);	
				}
            	
			} else{
				$tmef = round((($this->dias*24)-$this->totalTiempoMnpHrs)/$this->totalNumParos,2);
				$tmpr = round($this->totalTiempoMnpHrs/$this->totalNumParos,2);
            	$confiabilidad = round(($tmef*100)/($tmef+$tmpr),2);	
				if($tmef+$tmpr==0){
					$confiabilidad = 100;
				} else{
					$confiabilidad = round(($tmef*100)/($tmef+$tmpr),2);	
				}
			}         
            
        }else {
            $disponibilidad = 0;
            $tmef = 0;
            $tmpr = 0;
            $confiabilidad = 0;
        }
        /*
        $pdf->Cell(5, $height*2, 'D=', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(35, $height, 'Horas Totales Mes - Hrs MNP - Hrs MPP', 'B', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, 'x100', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');        
        $pdf->Cell(10, $height*2, 'TMEF=', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(35, $height, 'Horas Totales Mes - Hrs MNP', 'B', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setXY($x+10,$y+$height*2);
        $pdf->Cell(45, $height, 'Horas Totales Mes', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');        
        $pdf->Cell(45, $height, 'Número de paros', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        */ 
		$pdf->setXY($x+10,$y+10);//+$height*4);
        $pdf->setTextColor(255,255,255);  
        $pdf->Cell(45, $height*1, "Disponibilidad(%) = $disponibilidad", '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*1, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(45, $height*1, "TMEF(Horas) = $tmef", '1', 0, 'C', true, '', 1, false, 'T', 'C');        
        
        $pdf->setXY($x+10,$y+$height*7);
        //CALCULANDO DATOS
        /*
        $pdf->setTextColor(0,0,0);
        $pdf->Cell(5, $height*2, 'C=', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(30, $height, 'TMEF', 'B', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(10, $height*2, 'x100', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');        
        $pdf->Cell(10, $height*2, 'TMPR=', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(35, $height, 'Cantidad de horas en MNP', 'B', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setXY($x+10,$y+$height*8);
        $pdf->Cell(45, $height, 'TMEF + TMPR', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*2, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');        
        $pdf->Cell(45, $height, 'Número de paros', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        */ 
								$pdf->setXY($x+10,$y+20);//$height*10);
								$pdf->setTextColor(255,255,255);          
        $pdf->Cell(45, $height*1, "Confiabilidad(%) = $confiabilidad", '1', 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Cell(5, $height*1, '', '0', 0, 'C', false, '', 1, false, 'T', 'C');        
        $pdf->Cell(45, $height*1, "TMPR(Horas) = $tmpr", '1', 0, 'C', true, '', 1, false, 'T', 'C');        
                
        $pdf->Ln();
        
        $pdf->setTextColor(0,0,0);
        //$this->dibujarEjeCartesiano($x+10,$y+$height*12, $this->maximo, $this->numDias,'Comportamiento del Sistema '.$this->getDataSource()->getParameter('codigo'),
        $this->dibujarEjeCartesiano($x+10,$y+32, $this->maximo, $this->numDias,'Comportamiento del Sistema '.$this->getDataSource()->getParameter('codigo'),
                                    strtoupper($this->mesLiteral),$this->anio,$pdf);
                
        $this->dibujarLineas($this->puntoOrigenX, $this->puntoOrigenY, $this->widthCelda, $this->heightCelda,$this->numDias, $this->getDataSource(), $pdf);
        
        $pdf->Output($fileName, 'F');
    }
    
    function writeDetalles (DataSource $dataSource, TCPDF $pdf) {    	
        $widthMarginLeft = 1;
        $width0 = 13;
        $width1 = 15;
        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', 'B');
        $height = 5;
        $pdf->SetFillColor(255,255,255, true);
        
        $mes = $dataSource->getParameter('mes');
        $this->anio = $dataSource->getParameter('anio');
        $this->numDias = $dataSource->getParameter('numDias');
        $this->mesLiteral = $dataSource->getParameter('mesLiteral');
        
        $dataset = $dataSource->getDataSet();
        
        for ($i=1; $i < $this->numDias+1; $i++) {        	 
            $pdf->setTextColor(0,0,0);
            //$pdf->Cell($width1, $height, "$i-$this->mesLiteral-$this->anio", 1, 0, 'C', false, '', 1, false, 'T', 'C');
            //$pdf->Cell($width1-4, $height, $dataSource->getParameter('mesLiteralAbrev').'/'.$this->anio, 1, 0, 'C', false, '', 1, false, 'T', 'C');
            $row=current($dataset); 
            $pdf->Cell($width1-4, $height, $row['fecha_med_real'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
            
            //var_dump($row['fecha_med']);
            //var_dump(str_pad($i, 2, '0',STR_PAD_LEFT)."-$this->mesLiteral-$this->anio");           
            if($row['fecha_med']==str_pad($i, 2, '0',STR_PAD_LEFT)."-$this->mesLiteral-$this->anio"){
            	
                //var_dump($row['fecha_med']);
                $mayor=max(array($row['num_paros'],$row['tiempo_op_hrs'],$row['tiempo_standby_hrs'],$row['tiempo_mnp_hrs'],$row['tiempo_mpp_hrs']));
                $this->maximo=($this->maximo<$mayor)?$mayor:$this->maximo;
                $pdf->setTextColor(0,0,255);
                $pdf->Cell($width0-3, $height, $row['num_paros'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
				$pdf->Cell($width0-3, $height, $row['num_paros_planif'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(208,32,144);
                $pdf->Cell($width0-1, $height, $row['tiempo_op_hrs'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(255,215,0);
                $pdf->Cell($width0-1, $height, $row['tiempo_standby_hrs'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(255,0,0);
                $pdf->Cell($width0-1, $height, $row['tiempo_mnp_hrs'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(0,100,0);
                $pdf->Cell($width0-1, $height, $row['tiempo_mpp_hrs'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $this->totalNumParos=$this->totalNumParos+$row['num_paros'];
				$this->totalNumParosPl=$this->totalNumParosPl+$row['num_paros_planif'];
                $this->totalTiempoOpHrs=$this->totalTiempoOpHrs+$row['tiempo_op_hrs'];
                $this->totalTiempoStandByHrs=$this->totalTiempoStandByHrs+$row['tiempo_standby_hrs'];
                $this->totalTiempoMnpHrs=$this->totalTiempoMnpHrs+$row['tiempo_mnp_hrs'];
                $this->totalTiempoMppHrs=$this->totalTiempoMppHrs+$row['tiempo_mpp_hrs'];
                $this->dias=$this->dias+1;           
                next($dataset);    
            }else{
                $pdf->setTextColor(0,0,255);
                $pdf->Cell($width0-3, $height, '0', 1, 0, 'C', false, '', 1, false, 'T', 'C');
				$pdf->Cell($width0-3, $height, '0', 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(208,32,144);
                $pdf->Cell($width0-1, $height, '0.00', 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(255,215,0);
                $pdf->Cell($width0-1, $height, '0.00', 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(255,0,0);
                $pdf->Cell($width0-1, $height, '0.00', 1, 0, 'C', false, '', 1, false, 'T', 'C');
                $pdf->setTextColor(0,100,0);
                $pdf->Cell($width0-1, $height, '0.00', 1, 0, 'C', false, '', 1, false, 'T', 'C');
            }                
            $pdf->Ln();    
        }
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width1+6, $height, "Dias = $this->dias", 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(($width0*5)-7, $height, "", 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Ln();
        $pdf->Cell($width1-4, $height, $this->dias*24, 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setTextColor(0,0,255);
        $pdf->Cell($width0-3, $height, $this->totalNumParos, 1, 0, 'C', false, '', 1, false, 'T', 'C');
		$pdf->Cell($width0-3, $height, $this->totalNumParosPl, 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setTextColor(208,32,144);
        $pdf->Cell($width0-1, $height, $this->totalTiempoOpHrs, 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setTextColor(255,215,0);
        $pdf->Cell($width0-1, $height, $this->totalTiempoStandByHrs, 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setTextColor(255,0,0);
        $pdf->Cell($width0-1, $height, $this->totalTiempoMnpHrs, 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setTextColor(0,100,0);
        $pdf->Cell($width0-1, $height, $this->totalTiempoMppHrs, 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Ln();
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width1-4, $height, "Hrs. Tot. Mes", 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell($width0-3, $height, "Paros NPL", 1, 0, 'C', false, '', 1, false, 'T', 'C');
		$pdf->Cell($width0-3, $height, "Paros PL", 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell(($width0*2)-2, $height, "Total Horas de trabajo", 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell($width0-1, $height, "Tot.Hrs. MNP", 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell($width0-1, $height, "Tot.Hrs. MPP", 1, 0, 'C', false, '', 1, false, 'T', 'C');        
    }
    
    function dibujarEjeCartesiano($x, $y, $max, $numDias, $titulo, $mes, $anio, TCPDF $pdf){
        $xOrig = $x;
        $yOrig =$y;    
        $width = 105;
        $width1 = 10;
        $height = 5;
        $pdf->setXY($x+10,$y);       
        $pdf->Cell($width-13, $height, $titulo, '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->setXY($x,$y+$height);        
        $y=$pdf->getY()+5;
        $pdf->Cell($width, $height, "$mes $anio", '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $inc = ceil($max/6);
        $numFilas=0;
        $cont=0;        
        while ($max > $cont) {
            $cont = $cont + $inc;
            $numFilas++; 
        }
        //var_dump("$numFilas");
        $heightCelda = 90/$numFilas;
        //se define el alto de la celda en una unidad para poder graficar las lineas
        $this->heightCelda=$heightCelda/$inc;
        for ($i=$numFilas; $i >= 1; $i--) {                        
            $pdf->setXY($x,$y);    
            $pdf->Cell(5, 1, $i*$inc , '0', 0, 'L', false, '', 1, true, 'T', 'C');         
            $pdf->setXY($x+5,$y);
            $pdf->SetFillColor(175,238,238, true);
            $pdf->Cell($width-5, $heightCelda, '', '1', 0, 'C', true, '', 1, false, 'T', 'C');
            $y=$y+$heightCelda;
        }
        $widthDias=($width-5)/$numDias;
        //se define el ancho de la celda en una unidad para poder graficar las lineas
        $this->widthCelda=$widthDias;
        //punto origen de los ejes
        $this->puntoOrigenX=$x+5;
        $this->puntoOrigenY=$y;
        
        $pdf->setXY($x+3,$y+1);
        for ($i=0; $i <= $numDias; $i++) {             
            $pdf->Cell($widthDias, 1, $i, '0', 0, 'L', false, '', 1, true, 'T', 'C');            
        }
        $black = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $blue = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 255));
        $magent = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(208,32,144));   
        $gold = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,215,0));
        $red = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,0,0));
        $green = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,100,0));
        //MARCO PARA LAS ETIQUETAS
        $pdf->setXY($x+11,$y+6);
        $pdf->Cell($width-18, $height, '', '1', 0, 'C', false, '', 1, false, 'T', 'C');
        //LAS ETIQUETAS
        $pdf->SetFontSize(5.5);
        $pdf->setXY($x+20,$y+7);                
        $pdf->Line($x+15,$y+8.5,$x+20,$y+8.5,$blue);        
        $pdf->Cell($width1, $height-2, 'Nº PARO', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Line($x+31,$y+8.5,$x+36,$y+8.5,$magent);
        $pdf->setXY($x+20+$width1+6,$y+7);
        $pdf->Cell($width1, $height-2, 'OPERATIVO', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Line($x+47,$y+8.5,$x+52,$y+8.5,$gold);
        $pdf->setXY($x+20+$width1*2+6*2,$y+7);
        $pdf->Cell($width1, $height-2, 'STAND BY', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Line($x+63,$y+8.5,$x+68,$y+8.5,$red);
        $pdf->setXY($x+20+$width1*3+6*3,$y+7);
        $pdf->Cell($width1, $height-2, 'MNP', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Line($x+79,$y+8.5,$x+84,$y+8.5,$green);
        $pdf->setXY($x+20+$width1*4+6*4,$y+7);
        $pdf->Cell($width1, $height-2, 'MPP', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        
        //IMPRIME DIAS DE TRABAJO EN EL MES
        $pdf->SetFontSize(7.5);
        $pdf->setXY($x,$y+6+$height);
        $pdf->Cell($width, $height, 'DIAS DE TRABAJO EN EL MES', '0', 0, 'C', false, '', 1, false, 'T', 'C');
        
        //MARCO GRANDE
        $pdf->setLineStyle($black);
        $pdf->setXY($xOrig-7,$yOrig);
        $pdf->Cell($width+10, $height*23, '', '1', 0, 'C', false, '', 1, false, 'T', 'C');
        
        //IMPRIME HORAS
        $pdf->StartTransform();
        $pdf->Rotate(90,$xOrig+25,$yOrig+25);
        $pdf->Text($xOrig-10, $yOrig-5, 'Horas');        
        $pdf->StopTransform();
    }

    function dibujarLineas($x, $y,$widthCelda,$heightCelda, $numLineas, DataSource $dataSource, TCPDF $pdf){
        $blue = array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 255));
        $magent = array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(208,32,144));   
        $gold = array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,215,0));
        $red = array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,0,0));
        $green = array('width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,100,0));
        $xNumParos=$x;
        $yNumParos=$y-0.30;
        $xOperativo=$x;
        $yOperativo=$y-0.15;
        $xStandBy=$x;
        $yStandBy=$y;
        $xMNP=$x;
        $yMNP=$y+0.15;
        $xMPP=$x;
        $yMPP=$y+0.30;
        
        $dataset = $dataSource->getDataSet();
        $yOrigen=$y;
        for ($i=1; $i <= $numLineas; $i++) {
            $row=current($dataset);
            $cantidadY=substr($row['fecha_med'], 0,2);
            if($cantidadY==str_pad($i, 2, '0',STR_PAD_LEFT)){
                //$row['fecha_med']==str_pad($i, 2, '0',STR_PAD_LEFT)  
                $pdf->Line($xNumParos,$yNumParos,$xNumParos+$widthCelda,$yOrigen-($heightCelda*$row['num_paros'])-0.3,$blue);
                $pdf->Line($xOperativo,$yOperativo,$xOperativo+$widthCelda,$yOrigen-($heightCelda*$row['tiempo_op_hrs'])-0.15,$magent);
                $pdf->Line($xStandBy,$yStandBy,$xStandBy+$widthCelda,$yOrigen-($heightCelda*$row['tiempo_standby_hrs']),$gold);                
                $pdf->Line($xMNP,$yMNP,$xMNP+$widthCelda,$yOrigen-($heightCelda*$row['tiempo_mnp_hrs'])+0.15,$red);
                $pdf->Line($xMPP,$yMPP,$xMPP+$widthCelda,$yOrigen-($heightCelda*$row['tiempo_mpp_hrs'])+0.3,$green);
                next($dataset);  
                $xNumParos=$xNumParos+$widthCelda;
                $yNumParos=$yOrigen-($heightCelda*$row['num_paros'])-0.3;                
                $xOperativo=$xOperativo+$widthCelda;
                $yOperativo=$yOrigen-($heightCelda*$row['tiempo_op_hrs'])-0.15;
                $xStandBy=$xStandBy+$widthCelda;
                $yStandBy=$yOrigen-($heightCelda*$row['tiempo_standby_hrs']);
                $xMNP=$xMNP+$widthCelda;
                $yMNP=$yOrigen-($heightCelda*$row['tiempo_mnp_hrs'])+0.15;
                $xMPP=$xMPP+$widthCelda;
                $yMPP=$yOrigen-($heightCelda*$row['tiempo_mpp_hrs'])+0.3;
            }else{
                $pdf->Line($xNumParos,$yNumParos,$xNumParos+$widthCelda,$yOrigen-0.30,$blue);
                $pdf->Line($xOperativo,$yOperativo,$xOperativo+$widthCelda,$yOrigen-0.15,$magent);
                $pdf->Line($xStandBy,$yStandBy,$xStandBy+$widthCelda,$yOrigen,$gold);
                $pdf->Line($xMNP,$yMNP,$xMNP+$widthCelda,$yOrigen+0.15,$red);
                $pdf->Line($xMPP,$yMPP,$xMPP+$widthCelda,$yOrigen+0.3,$green);                
                $xNumParos=$xNumParos+$widthCelda;
                $yNumParos=$yOrigen-0.3;
                $xOperativo=$xOperativo+$widthCelda;
                $yOperativo=$yOrigen-0.15;
                $xStandBy=$xStandBy+$widthCelda;
                $yStandBy=$yOrigen;
                $xMNP=$xMNP+$widthCelda;
                $yMNP=$yOrigen+0.15;
                $xMPP=$xMPP+$widthCelda;
                $yMPP=$yOrigen+0.3;
            }    
        }        
    }
      
}
?>
