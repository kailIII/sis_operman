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
		
		$this->SetFontSize(14);
		$this->SetFont('','B');
		$this->Cell(105, $height/2, 'REGISTRO', 1, 2, 'C', false, '', 0, false, 'T', 'C');        
        $this->Cell(105,$height/2, 'Orden Interna de Trabajo',1,0,'C',false,'',0,false,'T','C');
        
        $this->setXY($x+145,$y);
        $this->SetFont('','');
        $this->Cell(40, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        
        
        $this->SetFontSize(7);
        
        $width1 = 17;
        $width2 = 23;
        $this->SetXY($x+145, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Código:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, 'GMAN-RG-SM-015', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+145, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Revisión:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, '1.0', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+145, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, 'Fecha Emision:', "B", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, '26/05/2012', "B", 0, 'C', false, '', 0, false, 'T', 'C');
        
        $this->SetFont('','');
        $y += 5;
        $this->SetXY($x+145, $y);
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

	public function MultiRowA($pArray,$pWidth,$pAlign,$pTotalFilas,$pFila) {
		// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)
	
		$page_start = $this->getPage();
		$y_start = $this->GetY();
		$i=0;
		$x=$this->getX();
		$y=$this->getY();
		foreach ($pArray as $value) {
			//$this->MultiCell(40, 0, $value, 1, 'R', 1, 2, '', '', true, 0);
			$nb=max($nb,$this->getNumLines($value,$pWidth[$i]));
			$i++;
			
		}
		
		//ALto de las columnas
		$alto=3*$nb;
		$j=0;
		

		foreach ($pArray as $value) {
			if($i>0){
				$this->setXY($x,$y);
			}
			
			//Verificación de borde
			if($pFila==$pTotalFilas){
				if($value==''){
					$borde='LRB';
				} else{
					$borde='LRTB';
				}
			} else{
				if($value==''){
					$borde='LR';
				} else{
					$borde='LRT';
				}
			}
			
			$this->MultiCell($pWidth[$j], $alto, $value, $borde, $pAlign[$j], 0, 2, '', '', true, 0);
			$j++;
			$x=$this->getX();
		}
		$this->Ln(0);
	}
	
	public function array_unshift_assoc(&$arr, $key, $val)
	{
	    $arr = array_reverse($arr, true);
	    $arr[$key] = $val;
	    return array_reverse($arr, true);
	} 
	
	public function MultiRow($pMatriz,$pWidth,$pAlign,$pVisible=array(),$pConNumeracion=1) {
		// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)
		$page_start = $this->getPage();
		$y_start = $this->GetY();
		
		
		
		//Obtiene el total de filas 
		$totalFilas=count($pMatriz)-1;
		//var_dump($pMatriz);exit;
		//echo $totalFilas;exit;
		$fila=0;
		
		foreach ($pMatriz as $row) {
			//Obtiene el alto máximo de la celda de toda la fila
			$i=0;
			$nb=0;
			
			$x=$this->getX();
			$y=$this->getY();
			//var_dump($this->array_unshift_assoc($fila,'nro',$fila));exit;
			foreach ($row as $value) {
				$nb=max($nb,$this->getNumLines($value,$pWidth[$i]));
				$i++;
			}
			//Define el alto máximo
			$alto=3*$nb;
			$j=0;
			$tmp=$fila+1;
			$row=$this->array_unshift_assoc($row,'nro',$tmp);
			//Dibuja la fila
			foreach ($row as $value) {
				if($i>0){
					$this->setXY($x,$y);
				}
				
				//Verificación de borde
				if($fila==$totalFilas){
					if($value==''){
						$borde='LRB';
					} else{
						$borde='LRTB';
					}
				} else{
					if($value==''){
						$borde='LR';
					} else{
						$borde='LRT';
					}
				}
				// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)
				$this->MultiCell($pWidth[$j], $alto, $value, $borde, $pAlign[$j], 0, 0, '', '', true, 0);
				$j++;
				$x=$this->getX();
				//$this->Ln();	
			}
			$this->Ln();
			$fila++;
		}
		
		
		
	}
}

Class ROrdenTrabajo extends Report {

	//TITULOS
	private $rgb1='218'; //'51'
	private $rgb2='218'; //'51'
	private $rgb3='220'; //'153'
	private $rgb1Txt='0'; //'255'
	private $rgb2Txt='0'; //'255'
	private $rgb3Txt='0'; //'255'

	function write($fileName) {
		$pdf = new CustomReport(PDF_PAGE_ORIENTATION, PDF_UNIT, "LETTER", true, 'UTF-8', false);
		$pdf->setDataSource($this->getDataSource());
		$dataSource = $this->getDataSource();
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		/*$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 006');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		*/
		
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
		// $pdf->setLanguageArray($l);
		
		// add a page
		$pdf->AddPage();
		
		$hGlobal = 5;
		$hMedium = 4;
		$wFechaEmision = 15;
		$wSolSector = 20;
		$wPrioridad = 16;
		$wSector = 40;
		$wCuenta = 35;
		$wEquipo = 35;
		$wNoOIT = 24;
		
		$pdf->SetXY(PDF_MARGIN_LEFT, 30);
		
		$pdf->SetFontSize(6.5);
		$pdf->SetFont('', '');
		//$pdf->setTextColor(0,0,255);
		//$pdf->SetFillColor(0,176,240);
		$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->MultiCell($w = $wFechaEmision, $h = $hMedium, $txt = 'FECHA EMISION', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wSolSector+25, $h = $hMedium, $txt = 'SOLICITANTE', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wPrioridad, $h = $hMedium, $txt = 'PRIORIDAD', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wSector+10, $h = $hMedium, $txt = 'SECTOR', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		//$pdf->MultiCell($w = $wCuenta, $h = $hMedium, $txt = 'CUENTA', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wEquipo, $h = $hMedium, $txt = 'EQUIPO Nº/ PROGRESIVA/TAG', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wNoOIT, $h = $hMedium, $txt = 'Nº OIT', $border = 1, $align = 'C', $fill = true, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		
		$pdf->SetFontSize(6.5);
		$pdf->SetFont('', '');
		$pdf->setTextColor(0,0,0);
		$pdf->MultiCell($w = $wFechaEmision, $h = $hGlobal, $txt = $dataSource->getParameter('fechaEmision'), $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wSolSector+25, $h = $hGlobal, $txt = $dataSource->getParameter('nombreSolicitante'), $border = 1, $align = 'L', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wPrioridad, $h = $hGlobal, $txt = $dataSource->getParameter('prioridad'), $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wSector+10, $h = $hGlobal, $txt = $dataSource->getParameter('sector'), $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false); 	
		//$pdf->MultiCell($w = $wCuenta, $h = $hGlobal, $txt = $dataSource->getParameter('centro_costo'), $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wEquipo, $h = $hGlobal, $txt = $dataSource->getParameter('codigo'), $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wNoOIT, $h = $hGlobal, $txt = $dataSource->getParameter('nOit'), $border = 1, $align = 'C', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hGlobal, $valign = 'M', $fitcell = false);
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 20, $h = $hGlobal, $txt = 'TIPO DE OIT:', $border = 1, $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0); 	
		$pdf->Cell($w = 60, $h = $hGlobal, $txt = $dataSource->getParameter('tipoOit') . '' . $dataSource->getParameter('tipoMant'), $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 22, $h = $hGlobal, $txt = 'CENTRO COSTO:', $border = 1, $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0); 	
		$pdf->Cell($w = 28, $h = $hGlobal, $txt = $dataSource->getParameter('centro_costo'), $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 27, $h = $hGlobal, $txt = 'CUENTA CONTABLE:', $border = 1, $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0); 	
		$pdf->Cell($w = 28, $h = $hGlobal, $txt = $dataSource->getParameter('cuenta'), $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 50, $h = $hGlobal, $txt = 'DESCRIPCIÓN EQUIPO/PROGRESIVA: ', $border = 1, $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = 135, $h = $hGlobal, $txt = $dataSource->getParameter('descripcion_progresiva'), $border = 1, $ln = 1, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 30, $h = $hGlobal, $txt = 'ESPECIALIDAD:', $border = 1, $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = 155, $h = $hGlobal, $txt = $dataSource->getParameter('especialidad'), $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DESCRIPCIÓN DE FALLA', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		//$pdf->MultiCell($w = 185, $h = $hMedium, $txt = $dataSource->getParameter('descripcion'), $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
		$pdf->MultiCell($w = 185, 0, $txt = $dataSource->getParameter('descripcion'), $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false);
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DESCRIPCIÓN DETALLADA DEL SERVICIO Y/O REPARACIÓN A REALIZAR', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		
		$pdf->MultiCell($w = 185, 0, $txt = $dataSource->getParameter('observacion'), $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = true);
		$pdf->Ln();
		
		$wDescProceso = 85;
		$wCargo = 25;
		$wUnidad = 25;
		$wEstimadas = 25;
		$wReales = 25;
		
		
		//tabla de actividades
		/*$pdf->setTextColor(0,0,255);
		$pdf->MultiCell($w = $wDescProceso, $h = $hMedium, $txt = 'DESCRIPCION DEL PROCESO', $border = 1, $align = 'L', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wCargo, $h = $hMedium, $txt = 'CARGO', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wUnidad, $h = $hMedium, $txt = 'UNIDAD (HR - MIN)', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wEstimadas, $h = $hMedium, $txt = 'ESTIMADAS', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false); 	
		$pdf->MultiCell($w = $wReales, $h = $hMedium, $txt = 'REALES', $border = 1, $align = 'C', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		
		$pdf->setTextColor(0,0,0);
		foreach($dataSource->getParameter('actividadesDataSource')->getDataset() as $datarow) {
			$pdf->MultiCell($w = $wDescProceso, $h = $hMedium, $txt = $datarow['descripcion'], $border = 1, $align = 'L', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false); 	
			$pdf->MultiCell($w = $wCargo, $h = $hMedium, $txt = 'data', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false); 	
			$pdf->MultiCell($w = $wUnidad, $h = $hMedium, $txt = 'data', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false); 	
			$pdf->MultiCell($w = $wEstimadas, $h = $hMedium, $txt = 'data', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false); 	
			$pdf->MultiCell($w = $wReales, $h = $hMedium, $txt = 'data', $border = 1, $align = 'C', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
		}
		
		$pdf->Cell($w = 35, $h = $hGlobal, $txt = 'Días de Trabajo aproximado: ', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = 15, $h = $hGlobal, $txt = ($dataSource->getParameter('tiempoEstimado').' '.$dataSource->getParameter('unidadMedidaTiempoEstimado')), $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = 20, $h = $hGlobal, $txt = 'Dias Reales: ', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = 15, $h = $hGlobal, $txt = $dataSource->getParameter('diasReales'), $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wCargo, $h = $hGlobal, $txt = '', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wUnidad, $h = $hGlobal, $txt = 'TOTAL (HR)', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wEstimadas, $h = $hGlobal, $txt = 'data goes here', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wReales, $h = $hGlobal, $txt = 'data goes here', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		$pdf->Ln();
*/
		
		//$pdf->SetFillColor(0,176,240);
		$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
		//$pdf->setTextColor(0,0,0);
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DETALLE DE REPUESTOS Y/O MATERIALES', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		
		//REPUESTOS
		$wPedidoCodigo =  25;
		$wExistencias = 20;
		$wMateriales = 45;
		$wUnidad = 15;
		$wPrecio = 20;
		$wCantUtiliz = 25;
		$wObservaciones = 35;
		$counter = 0;
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->MultiCell($w = $wPedidoCodigo, $h = $hMedium, $txt = 'PEDIDO O CÓDIGO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wExistencias, $h = $hMedium, $txt = 'EXISTENCIAS', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wMateriales, $h = $hMedium, $txt = 'MATERIALES/ REPUESTOS', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wUnidad, $h = $hMedium, $txt = 'UNIDAD', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wPrecio, $h = $hMedium, $txt = 'PRECIO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wCantUtiliz, $h = $hMedium, $txt = 'CANT. UTILIZ.', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wObservaciones, $h = $hMedium, $txt = 'COSTO TOTAL', $border = 1, $align = 'C', $fill = true, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		
		$pdf->setTextColor(0,0,0);
		$totMateriales=0;
		$swRep=0;
		foreach($dataSource->getParameter('repuestosDataSource')->getDataset() as $datarow) {
			$swRep=1;
			$pdf->MultiCell($w = $wPedidoCodigo, $h = $hMedium, $txt = $datarow['codigo'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExistencias, $h = $hMedium, $txt = $datarow['existencias'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wMateriales, $h = $hMedium, $txt = $datarow['nombre_item'], $border = 1, $align = 'L', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wUnidad, $h = $hMedium, $txt = $datarow['codigo_unidad_medida'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wPrecio, $h = $hMedium, $txt = $datarow['costo'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wCantUtiliz, $h = $hMedium, $txt = $datarow['cantidad'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wObservaciones, $h = $hMedium, $txt = number_format($datarow['costo_total'],2), $border = 1, $align = 'R', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$counter++;
			$totMateriales+=$datarow['costo_total'];
		}
		if($swRep){
			$pdf->Cell($w = $wPedidoCodigo+$wExistencias+$wMateriales+$wUnidad+$wPrecio+$wCantUtiliz, $h = 5, $txt = 'TOTAL REPUESTOS Y/O MATERIALES:', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($w = $wObservaciones, $h = 5, $txt = number_format($totMateriales,2), $border = 0, $ln = 1, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		}
	
		$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DETALLE DE MANO DE OBRA', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		
		$wHHNormal = 15;
		$wHHExtras = 15;
		$wExtMov = 15;
		$wNombre = 40;
		$wCargo = 45;
		$wObservaciones = 40;
		$counter = 0;
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->MultiCell($w = $wHHNormal, $h = $hMedium, $txt = 'HH NORMAL', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wHHNormal, $h = $hMedium, $txt = 'HH EXTRA', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wHHExtras, $h = $hMedium, $txt = 'HH NOCTURNO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wExtMov, $h = $hMedium, $txt = 'HH. FER. Y DOM.', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wNombre, $h = $hMedium, $txt = 'NOMBRE', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wCargo, $h = $hMedium, $txt = 'CARGO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wObservaciones, $h = $hMedium, $txt = 'COSTO TOTAL', $border = 1, $align = 'C', $fill = true, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		
		$pdf->setTextColor(0,0,0);
		$totManoObra=0;
		$swManoObra=0;
		foreach($dataSource->getParameter('funcionariosDataSource')->getDataset() as $datarow) {
			$swManoObra=1;
			$pdf->MultiCell($w = $wHHNormal, $h = $hMedium, $txt = $datarow['hh_normal'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wHHExtras, $h = $hMedium, $txt = $datarow['hh_extras'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExtMov, $h = $hMedium, $txt = $datarow['hh_ext_mov'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExtMov, $h = $hMedium, $txt = $datarow['hh_fer_dom'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wNombre, $h = $hMedium, $txt = $datarow['nombre_funcionario'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wCargo, $h = $hMedium, $txt = $datarow['cargo_funcionario'], $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wObservaciones, $h = $hMedium, $txt = number_format($datarow['costo_total'],2), $border = 1, $align = 'R', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$i++;
			$totManoObra+=$datarow['costo_total'];
		}
		if($swManoObra){
			$pdf->Cell($w = $wHHNormal+$wHHExtras+$wExtMov+$wExtMov+$wNombre+$wCargo, $h = 5, $txt = 'TOTAL MANO DE OBRA:', $border = 0, $ln = 0, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($w = $wObservaciones, $h = 5, $txt = number_format($totManoObra,2), $border = 0, $ln = 1, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		}

		/*for($i = $counter; $i < 6; $i++) {
			$pdf->MultiCell($w = $wHHNormal, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wHHExtras, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExtMov, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExtMov, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wNombre, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wCargo, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wObservaciones, $h = $hMedium, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
		}*/
		
		
		//Costos
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'OTROS COSTOS', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->MultiCell($w = $wPedidoCodigo+$wExistencias+$wMateriales+$wUnidad+$wPrecio+$wCantUtiliz, $h = $hMedium, $txt = 'DESCRIPCIÓN COSTO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = 35, $h = $hMedium, $txt = 'COSTO TOTAL', $border = 1, $align = 'C', $fill = true, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->setTextColor(0,0,0);
		foreach($dataSource->getParameter('mantCostoDataSource')->getDataset() as $datarow) {
			$pdf->MultiCell($w = $wPedidoCodigo+$wExistencias+$wMateriales+$wUnidad+$wPrecio+$wCantUtiliz, $h = 4, $txt = $datarow['descripcion'], $border = 1, $align = 'R', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = 35, $h = 4, $txt = number_format($datarow['costo'],2), $border = 1, $align = 'R', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
		}
		
		

		//Produjo paro
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell(140, $height+5, '¿EL EQUIPO PRODUJO PARO?      SI              NO', 1, 0, 'C', true, '', 0, false, 'T', 'C');
		$checkBoxSi;
        $checkBoxNo;
		
        if($dataSource->getParameter('ubicacion_tecnica')=='Si'){
            $checkBoxSi='<input type="checkbox" name="boxLugarSi" value="1" checked="checked">';
            $checkBoxNo='<input type="checkbox" name="boxLugarNo" value="1">';    
        }else{
            $checkBoxSi='<input type="checkbox" name="boxLugarSi" value="1">';
            $checkBoxNo='<input type="checkbox" name="boxLugarNo" value="1" checked="checked">';
        }
		$pdf->Cell(20, $height+5, 'HORAS PARO:', 1, 0, 'C', true, '', 0, false, 'T', 'C');
		$pdf->Cell(25, $height+5, $dataSource->getParameter('nota_tecnico_loc'), 1, 0, 'C', true, '', 0, false, 'T', 'C');
		$pdf->setTextColor(0,0,0);
		$pdf->writeHTMLCell(5,5,$pdf->getX()-100,$pdf->getY()+1,"$checkBoxSi");        
        $pdf->writeHTMLCell(5,5,$pdf->getX()+ 8,$pdf->getY(),"$checkBoxNo");
		

		//Certificacion de trabajo
		$pdf->Ln();
		$pdf->Ln();
		//$pdf->SetFillColor(0,176,240);
		$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'CERTIFICACIÓN DE TRABAJO REALIZADO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		
		$wHoraServicio = 40;
		$wFecha = 20;
		$wFechaDato = 25;
		$wHora = 15;
		$wHoraDato = 15;
		$wResp = 30;
		$wRespDato = 40;
		
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wHoraServicio - 13, $h = $hGlobal, $txt = 'INICIO DEL SERVICIO', $border = 'LBT', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wFecha - 3, $h = $hGlobal, $txt = 'FECHA: ', $border = 'BRT', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wFechaDato, $h = $hGlobal, $txt = $dataSource->getParameter('fechaEjecIni'), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wHora-5, $h = $hGlobal, $txt = 'HORA', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wHoraDato, $h = $hGlobal, $txt = $dataSource->getParameter('horaEjeInicio'), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		/*$pdf->Cell($w = $wResp, $h = $hGlobal, $txt = 'RESP (OP. pt)', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wRespDato, $h = $hGlobal, $txt = 'data', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');*/
		
		//$pdf->Ln();
		
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wHoraServicio - 15, $h = $hGlobal, $txt = 'FIN DEL SERVICIO', $border = 'LBT', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wFecha - 4, $h = $hGlobal, $txt = 'FECHA: ', $border = 'RBT', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wFechaDato, $h = $hGlobal, $txt = $dataSource->getParameter('fechaEjecFin'), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wHora-5, $h = $hGlobal, $txt = 'HORA', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wHoraDato, $h = $hGlobal, $txt = $dataSource->getParameter('horaEjeFin'), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		/*$pdf->Cell($w = $wResp, $h = $hGlobal, $txt = 'RESP (OP. pt)', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wRespDato, $h = $hGlobal, $txt = 'data', $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');*/
		
		$pdf->Ln();
		$pdf->Ln();
		$wBlock = 46;
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = $wBlock, $h = $hGlobal, $txt = 'SOLICITADO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wBlock, $h = $hGlobal, $txt = 'APROBADO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wBlock, $h = $hGlobal, $txt = 'EJECUTADO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wBlock + 1, $h = $hGlobal, $txt = 'RECIBIDO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		
		$pdf->Ln();
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wBlock, $h = $hGlobal, $txt = 'Firma', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wBlock, $h = $hGlobal, $txt = 'Firma', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wBlock, $h = $hGlobal, $txt = 'Firma', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wBlock + 1, $h = $hGlobal, $txt = 'Firma', $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		
		$pdf->SetFontSize(6);
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = ($wBlock-25)/2, $h = $hGlobal, $txt = 'Nombre:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = 35.5, $h = $hGlobal, $txt = $dataSource->getParameter('nombreSolicitante'), $border = 'B', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 1, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = ($wBlock-25)/2, $h = $hGlobal, $txt = 'Nombre:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = 35.5, $h = $hGlobal, $txt = $dataSource->getParameter('nombreAprobado'), $border = 'B', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 1, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = ($wBlock-25)/2, $h = $hGlobal, $txt = 'Nombre:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = 35.5, $h = $hGlobal, $txt = $dataSource->getParameter('nombreEjecutado'), $border = 'B', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 1, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = ($wBlock-25)/2, $h = $hGlobal, $txt = 'Nombre:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = 35.5 + 1, $h = $hGlobal, $txt = $dataSource->getParameter('nombreRecibido'), $border = 'BR', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 1, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = 'Fecha:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = '', $border = 'B', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = 'Fecha:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = '', $border = 'B', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = 'Fecha:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = '', $border = 'B', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,255);
		$pdf->Cell($w = $wBlock/2, $h = $hGlobal, $txt = 'Fecha:', $border = 'LB', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($w = ($wBlock/2) + 1, $h = $hGlobal, $txt = '', $border = 'BR', $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'COMENTARIOS SOBRE EL TRABAJO REALIZADO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		$pdf->setTextColor(0,0,0);
		//$pdf->MultiCell($w = 185, $h = $hMedium, $txt = $dataSource->getParameter('comentarios'), $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = true);
		$pdf->MultiCell($w = 185, 0, $txt = $dataSource->getParameter('comentarios'), $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false);
		
		$wTercio = 62;
		$hDouble = 16;
		
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = $wTercio, $h = $hGlobal, $txt = 'ACCIDENTES', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wTercio, $h = $hGlobal, $txt = 'RECLAMOS', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell($w = $wTercio - 1, $h = $hGlobal, $txt = 'OTROS', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Ln();
		$pdf->setTextColor(0,0,0);
		$pdf->MultiCell($w = $wTercio, $h = $hMedium, $txt = $dataSource->getParameter('accidentes'), $border = 1, $align = 'L', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = true);
		$pdf->MultiCell($w = $wTercio, $h = $hMedium, $txt = $dataSource->getParameter('reclamos'), $border = 1, $align = 'L', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = true);
		$pdf->MultiCell($w = $wTercio - 1, $h = $hMedium, $txt = $dataSource->getParameter('otros'), $border = 1, $align = 'L', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false);
		
		//Mantenimientos predefinidos
		//Titulos
		if(count($dataSource->getParameter('mantPredefDataSource')->getDataset())>0){
			$pdf->AddPage();
			$pdf->setTextColor(255,255,255);
			$pdf->SetFont('','B');
			$pdf->Cell($w = 20, $h = 5, $txt = 'TAG:', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->setTextColor(0,0,0);
			$pdf->Cell($w = 50, $h = 5, $txt = $dataSource->getParameter('codigo'), $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->setTextColor(255,255,255);
			$pdf->Cell($w = 50, $h = 5, $txt = 'TIPO DE MANTENIMIENTO:', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->setTextColor(0,0,0);
			$pdf->Cell($w = 65, $h = 5, $txt = $dataSource->getParameter('desc_mant_predef'), $border = 1, $ln = 1, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->SetFontSize(14);
			$pdf->setTextColor(255,255,255);
			$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DETALLE DEL  MANTENIMIENTO A REALIZAR', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->setTextColor(255,255,255);
			$pdf->SetFont('','');
			$pdf->SetFontSize(10);
			$pdf->Cell($w = 10, $h = $hGlobal, $txt = 'NRO.', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($w = 40, $h = $hGlobal, $txt = 'SERVICIO', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($w = 95, $h = $hGlobal, $txt = 'DESCRIPCIÓN', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($w = 30, $h = $hGlobal, $txt = 'MEDIDAS', $border = 1, $ln = 0, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($w = 10, $h = $hGlobal, $txt = 'EJEC.', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		}
		
		//Valores
		$pdf->setTextColor(0,0,0);
		$pdf->SetFontSize(8);
		$anchos=array(10,40,95,30,10);
		$aligns=array('C','L','L','L','C');
		$tot=count($dataSource->getParameter('mantPredefDataSource')->getDataset())-1;
		$fila=0;
		//var_dump($dataSource->getParameter('mantPredefDataSource')->getDataset());exit;
		$data=$dataSource->getParameter('mantPredefDataSource')->getDataset();
		$pdf->MultiRow($dataSource->getParameter('mantPredefDataSource')->getDataset(),$anchos,$aligns);
		
		
		$imprimirMaterialLista=0;
		$imprimirManoObraLista=0;
		//Verifica impresión de hoja adicional de repuestos
		if($dataSource->getParameter('cat_estado')=='generado'||$dataSource->getParameter('cat_estado')=='Borrador'){
			$imprimirMaterialLista=1;
		} else if($dataSource->getParameter('cat_estado')=='Abierto'&&$swRep==0){
			$imprimirMaterialLista=1;
		}
		
		//Verifica impresión de hoja adicional de mano de obra
		if($dataSource->getParameter('cat_estado')=='generado'||$dataSource->getParameter('cat_estado')=='Borrador'){
			$imprimirManoObraLista=1;
		} else if($dataSource->getParameter('cat_estado')=='Abierto'&&$swManoObra==0){
			$imprimirManoObraLista=1;
		}
		
		
		if($imprimirMaterialLista){
			$pdf->AddPage();
			$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
			$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DETALLE DE REPUESTOS Y/O MATERIALES', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->MultiCell($w = $wPedidoCodigo, $h = $hMedium, $txt = 'PEDIDO O CÓDIGO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->MultiCell($w = $wExistencias, $h = $hMedium, $txt = 'EXISTENCIAS', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->MultiCell($w = $wMateriales, $h = $hMedium, $txt = 'MATERIALES/ REPUESTOS', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->MultiCell($w = $wUnidad, $h = $hMedium, $txt = 'UNIDAD', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->MultiCell($w = $wPrecio, $h = $hMedium, $txt = 'PRECIO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->MultiCell($w = $wCantUtiliz, $h = $hMedium, $txt = 'CANT. UTILIZ.', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->MultiCell($w = 35, $h = $hMedium, $txt = 'COSTO TOTAL', $border = 1, $align = 'C', $fill = true, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
			$pdf->setTextColor(0,0,0);
					
			for($i = $counter; $i < 28; $i++) {
				$pdf->MultiCell($w = $wPedidoCodigo, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
				$pdf->MultiCell($w = $wExistencias, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
				$pdf->MultiCell($w = $wMateriales, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
				$pdf->MultiCell($w = $wUnidad, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
				$pdf->MultiCell($w = $wPrecio, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
				$pdf->MultiCell($w = $wCantUtiliz, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
				$pdf->MultiCell($w = 35, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			}
		}
	if($imprimirManoObraLista){
		$pdf->AddPage();
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($w = 185, $h = $hGlobal, $txt = 'DETALLE DE MANO DE OBRA', $border = 1, $ln = 1, $align = 'C', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->MultiCell($w = $wHHNormal, $h = $hMedium, $txt = 'HH NORMAL', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wHHNormal, $h = $hMedium, $txt = 'HH EXTRA', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wHHExtras, $h = $hMedium, $txt = 'HH NOCTURNO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wExtMov, $h = $hMedium, $txt = 'HH. FER. Y DOM.', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wNombre, $h = $hMedium, $txt = 'NOMBRE', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wCargo, $h = $hMedium, $txt = 'CARGO', $border = 1, $align = 'C', $fill = true, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->MultiCell($w = $wObservaciones, $h = $hMedium, $txt = 'COSTO TOTAL', $border = 1, $align = 'C', $fill = true, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'M', $fitcell = false);
		$pdf->setTextColor(0,0,0);
				
		for($i = $counter; $i < 28; $i++) {
			$pdf->MultiCell($w = $wHHNormal, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wHHExtras, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExtMov, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wExtMov, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wNombre, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wCargo, $h = $hMedium+3, $txt = '', $border = 1, $align = 'C', $fill = false, $ln = 0, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
			$pdf->MultiCell($w = $wObservaciones, $h = $hMedium+3, $txt = '', $border = 1, $align = 'R', $fill = false, $ln = 1, $x = '',$y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = $hMedium, $valign = 'T', $fitcell = false);
		}
	}

		$pdf->Output($fileName, 'F');
	}
}
?>
