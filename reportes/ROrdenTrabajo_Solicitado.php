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
        $this->Cell(30, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $this->Image(dirname(__FILE__).'/logo.png', 25, 14, 18);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetFontSize(14);
        $this->SetFont('','B');        
        $this->Cell(115, $height/2, 'REGISTRO', 1, 2, 'C', false, '', 0, false, 'T', 'C');        
        $this->Cell(115,$height/2, 'Solicitud de Mantenimiento',1,0,'C',false,'',0,false,'T','C');
        
        $this->setXY($x+115,$y);
        $this->SetFont('','');
        $this->Cell(40, $height, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        
        
        $this->SetFontSize(7);
        
        $width1 = 17;
        $width2 = 23;
        $this->SetXY($x+115, $y);
        $this->setCellPaddings(2);
        $this->Cell($width1, $height/4, ''/*'Código:'*/, "", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
		$dataset1=$this->getDataSource()->getDataset();
        $this->Cell($width2, $height/4, ''/*$dataset1[0]['nro_sol']*/, "", 0, 'C', false, '', 0, false, 'T', 'C');
        
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
        $this->Cell($width1, $height/4, ''/*'Fecha Emision:'*/, "", 0, '', false, '', 0, false, 'T', 'C');
        $this->SetFont('','B');
        $this->Cell($width2, $height/4, ''/*'26/05/2012'*/, "", 0, 'C', false, '', 0, false, 'T', 'C');
        
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

Class ROrdenTrabajoSolicitado extends Report {

    //TITULOS
    private $rgb1='218'; //'51'
    private $rgb2='218'; //'51'
    private $rgb3='220'; //'153'
    private $rgb1Txt='0'; //'255'
    private $rgb2Txt='0'; //'255'
    private $rgb3Txt='0'; //'255'

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
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM-10);
        
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        //set some language-dependent strings
        
        // add a page
        $pdf->AddPage();
        
        $height = 5;
        $width1 = 17.5;
        $width2 = 30;
        $width3 = 30;
        $width4 = 75;
		
		$pdf->SetXY(PDF_MARGIN_LEFT, 30);
		
		//var_dump($this->getDataSource()->getParameters());exit;
        $dataset = $this->getDataSource()->getDataset();
        $aux=$this->getDataSource()->getParameters();
		//var_dump($aux['insumDataSource']->getDataset());exit;
		$datasetInsum=$aux['insumDataSource']->getDataset();
		//var_dump($datasetInsum);exit;
        $gray = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(169, 169, 169));        
        //$pdf->setLineStyle($gray);
        $pdf->SetFontSize(7.5);
        $pdf->SetFont('', '');
        //$pdf->setTextColor(0,0,0);
        $pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);        
        //$pdf->setFillColor(220,220,220,true);
		$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
		$pdf->Cell($width2, $height, 'Solicitado por', 1, 0, 'C', true, '', 0, true, 'T', 'C');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($width2+37, $height, $dataset[0]['desc_solicitante'], 1, 0, 'L', false, '', 0, false, 'T', 'C');
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		$pdf->Cell($width1, $height, 'Cargo', 1, 0, 'L', true, '', 0, true, 'T', 'C');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($width1+53, $height, $dataset[0]['cargo_solicitante'], 1, 0, 'L', false, 'L', 0, false, 'T', 'C');
		//$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		/*$pdf->Cell($width1-9, $height, '', 1, 0, 'L', true, '', 0, true, 'T', 'C');
		$pdf->setTextColor(0,0,0);
		$pdf->Cell($width1+11, $height, '', 1, 0, 'L', false, '', 0, false, 'T', 'C');*/
		$pdf->Ln();

		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2, $height, 'Fecha de Envio', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2, $height, 'Fecha requerida', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2+40, $height, 'Sector', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2+25, $height, ' EQUIPO Nº /TAG ', 1, 0, 'C', true, '', 1, false, 'T', 'C');
        $pdf->Ln();
        //var_dump($dataset);
        
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2, $height, $dataset[0]['fecha'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2, $height, $dataset[0]['fecha_requerida'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2+40, $height, $dataset[0]['desc_localizacion'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Cell($width2+25, $height, $dataset[0]['desc_equipo'], 1, 0, 'C', false, '', 1, false, 'T', 'C');
        $pdf->Ln();
        $pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2*3, $height, 'Criticidad', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2*3+5, $height, 'Prioridad', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Ln();
        $pdf->setTextColor(0,0,0);
        $pdf->Cell($width2*3, $height, $dataset[0]['importancia'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell($width2*3+5, $height, $dataset[0]['prioridad'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln();
        /*echo '<pre>';
        print_r($dataset[0]);
        echo '</pre>';exit;*/
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2+$width1+137.5, $height, 'OBSERVACIONES', 1, 1, 'C', true, '', 0, false, 'T', 'C');
		$pdf->setTextColor(0,0,0);
        $pdf->MultiCell(185,$height*3,$dataset[0]['observacion'],1,'J',false);
        $pdf->Ln(0);
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2*2+$width1, $height, 'ACCIÓN A REALIZAR', 'LTB', 0, 'C', true, '', 0, false, 'T', 'C');
		$pdf->setTextColor(0,0,0);
        $pdf->Cell($width2, $height, '', 'TBR', 0, 'L', true, '', 0, false, 'T', 'C');
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2*2+$width1, $height, 'CROQUIS ACTUAL', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Ln();
        $x=$pdf->getX();
        $y=$pdf->getY();
		
		$pdf->setTextColor(0,0,0);
        $pdf->MultiCell($width2*3+$width1,$height*9,$dataset[0]['descripcion'],1,'J',false,0);
        $pdf->MultiCell($width2*2+$width1,$height*9,'',1,'J',false,0);
		$pdf->Ln();
		//$pdf->Ln();
        //$pdf->setXY($x,$y+$height*9);
        
        if($this->getDataSource()->getParameter('imagePath') != null) {
			$pdf->Image($file = $this->getDataSource()->getParameter('imagePath'), $x = $x+108, $y = $y, 
				$w = 76, $h = 44, $type = '', $link = '', $align = '', $resize = true, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 1, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = array()); 	
		} else {
			//$pdf->SetXY($x, $y);
			$pdf->Cell(65, 35, '', 1, 0, 'L', false, '', 0, false, 'T', 'C');
		}
		$pdf->Ln(0);
		
		//$pdf->SetXY($x, $y);
        
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell(185, $height, 'INSUMOS - CANTIDAD REQUERIDA', 1, 1, 'C', true, '', 0, false, 'T', 'C');
        //$pdf->setXY($x-108,$y+$height*10);
        $pdf->Cell(127.5, $height, 'DESCRIPCIÓN', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width1+10, $height, 'UNIDAD', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2, $height, 'CANTIDAD', 1, 0, 'C', true, '', 0, false, 'T', 'C');
		$pdf->Ln();
		//Detalle insumos
		//var_dump($dataset);exit;
		$pdf->setTextColor(0,0,0);
		foreach($datasetInsum as $datarow) {
			//var_dump($datarow);exit;
			$pdf->Cell(127.5, $height, $txt = $datarow['descripcion'], $border = 1, $ln = 0, $align = 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($width1+10, $height, $txt = $datarow['unidad_medida'], $border = 1, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
			$pdf->Cell($width2, $height, $txt = $datarow['cant_insum'], $border = 1, $ln = 1, $align = 'R', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		}
		
        //$pdf->Ln($height*6);
        $pdf->Ln(0);
        $checkBoxSi;
        $checkBoxNo;
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
		//var_dump($dataset[0]['inspeccion_lugar']);
        if($dataset[0]['inspeccion_lugar']=='si'){
            $checkBoxSi='<input type="checkbox" name="boxLugarSi" value="1" checked="checked">';
            $checkBoxNo='<input type="checkbox" name="boxLugarNo" value="1">';    
        }else{
            $checkBoxSi='<input type="checkbox" name="boxLugarSi" value="1">';
            $checkBoxNo='<input type="checkbox" name="boxLugarNo" value="1" checked="checked">';
        }
        $pdf->Cell($width2*3+$width1, $height, '        ¿Se inspeccionó en el lugar?           SI              NO', 1, 0, 'L', true, '', 0, false, 'T', 'C');
        //var_dump($dataset);
        $pdf->setTextColor(0,0,0);
        $pdf->writeHTMLCell(5,5,$pdf->getX()-51,$pdf->getY()+1,"$checkBoxSi");        
        $pdf->writeHTMLCell(5,5,$pdf->getX()+ 9,$pdf->getY(),"$checkBoxNo");
        $x=$pdf->getX();
        $y=$pdf->getY();
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->setXY($x+32,$y-1);
        $pdf->Cell($width2*2+$width1, $height, '        ¿Se MARCO el lugar exacto?      SI              NO', 1, 0, 'L', true, '', 0, false, 'T', 'C');
        if($dataset[0]['inspeccion_exacto']=='si'){
            $checkBoxSi='<input type="checkbox" name="boxExacSi" value="1" checked="checked">';
            $checkBoxNo='<input type="checkbox" name="boxExacNo" value="1">';    
        }else{
            $checkBoxSi='<input type="checkbox" name="boxExacSi" value="1">';
            $checkBoxNo='<input type="checkbox" name="boxExacNo" value="1" checked="checked">';
        }
		$pdf->setTextColor(0,0,0);
        $pdf->writeHTMLCell(5,5,$pdf->getX()-25,$pdf->getY()+1,"$checkBoxSi");        
        $pdf->writeHTMLCell(5,5,$pdf->getX()+ 9,$pdf->getY(),"$checkBoxNo");
        $pdf->Ln(4);
        //$pdf->setFillColor(50,135,215);
        /*$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
        
		$pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2*2+$width1, $height, '¿Priorización SOLICITANTE?', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2*3+$width1, $height, 'Clase-Importancia del equipo:', 1, 1, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2*2+$width1, $height*8, '', 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $x=$pdf->getX();
        $y=$pdf->getY();
        $pdf->SetFontSize(7);
		$pdf->SetTextColor(0,0,0);
        $text = "Clase A-Equipo cuya parada interrumpe el servicio, llevando a la facturación cesante.\nClase B-Equipo que participa del servicio pero que su parada por algún tiempo no interrumpe la producción.\nClase C-Equipo que no participa del servicio."; 
        $pdf->MultiCell($width2*3+$width1,$height*3,$text,1,'',false,0);
        $pdf->setXY($x,$y+$height*3);
        $pdf->SetFontSize(7.5);
        $pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2*3+$width1, $height, 'PRIORIDAD', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setXY($x,$y+$height*4);
        $pdf->SetFontSize(7);
		$pdf->SetTextColor(0,0,0);
        $text = "Prioridad 1 - Emergencia - Mantenimiento que debe ser realizado inmediatamente después de detectada su necesidad.\nPrioridad 2 - Necesaria - Mantenimiento que puede ser postergado por algunos días pero que su ejecucion no debe superar un mes.\nPrioridad 3 - Deseable - Mantenimiento que puede ser postergado por algunas semanas (recomendable 4 ó 5) pero no debe ser omitido.";
        $pdf->MultiCell($width2*3+$width1,$height*4,$text,1,'',false,0);
        //prioridades
        $pdf->setXY($x-$width2*2-$width1,$y);
        $pdf->Ln($height);
        $pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell(29.5, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'Clase', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'Clase', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'Clase', 0, 1, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell(29.5, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'A', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'B', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'C', 0, 1, 'C', false, '', 0, false, 'T', 'C');
        //primera fila
        $xFilas=$pdf->getX();
        $yFilas=$pdf->getY();
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(13.5, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'Prioridad 1', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setFillColor(255,0,0);
        $pdf->Cell(16, $height, '1', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        $colorUno;
        $colorDos;
        $colorTres;
        $checkBox;
        if($dataset[0]['prioridad']==1){
            $colorUno=255;
            $colorDos=0;
            $colorTres=0;
            $checkBox='<input type="checkbox" name="mybox" value="1" checked="checked">';    
        }else{
            $checkBox='<input type="checkbox" name="mybox" value="1" checked="unchecked">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas+45.5,$yFilas);
        $pdf->setFillColor(255,153,0);
        $pdf->Cell(16, $height, '4', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==4){
            $colorUno=255;
            $colorDos=153;
            $colorTres=0;
            $checkBox='<input type="checkbox" name="mybox2" value="1" checked="checked">';    
        }else{
            $checkBox='<input type="checkbox" name="mybox2" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas+61.5,$yFilas);
        $pdf->setFillColor(102,255,102);
        $pdf->Cell(16, $height, '7', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==7){
            $colorUno=102;
            $colorDos=255;
            $colorTres=102;
            $checkBox='<input type="checkbox" name="mybox3" value="1" checked="checked">';    
        }else{
            $checkBox='<input type="checkbox" name="mybox3" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas,$yFilas+$height);
        //segunda fila
        $pdf->setFillColor(50,135,215);
        $pdf->Cell(13.5, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'Prioridad 2', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setFillColor(255,102,51);
        $pdf->Cell(16, $height, '2', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==2){
            $colorUno=255;
            $colorDos=102;
            $colorTres=51;
            $checkBox='<input type="checkbox" name="mybox4" value="1" checked="checked">';    
        }else{
            $checkBox='<input type="checkbox" name="mybox4" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas+45.5,$yFilas+$height);
        $pdf->setFillColor(255,255,0);
        $pdf->Cell(16, $height, '5', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==5){
            $colorUno=255;
            $colorDos=255;
            $colorTres=0;
            $checkBox='<input type="checkbox" name="mybox5" value="1" checked="checked">';
        }else{
            $checkBox='<input type="checkbox" name="mybox5" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas+61.5,$yFilas+$height);
        $pdf->setFillColor(51,204,51);
        $pdf->Cell(16, $height, '8', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==8){
            $colorUno=51;
            $colorDos=204;
            $colorTres=51;
            $checkBox='<input type="checkbox" name="mybox6" value="1" checked="checked">';    
        }else{
            $checkBox='<input type="checkbox" name="mybox6" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas,$yFilas+$height*2);
        //tercera fila
        $pdf->setFillColor(50,135,215);
        $pdf->Cell(13.5, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Cell(16, $height, 'Prioridad 3', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setFillColor(255,153,102);
        $pdf->Cell(16, $height, '3', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==3){
            $colorUno=255;
            $colorDos=153;
            $colorTres=102;
            $checkBox='<input type="checkbox" name="mybox7" value="1" checked="checked">';
        }else{
            $checkBox='<input type="checkbox" name="mybox7" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas+45.5,$yFilas+$height*2);
        $pdf->setFillColor(204,255,153);
        $pdf->Cell(16, $height, '6', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==6){
            $colorUno=204;
            $colorDos=255;
            $colorTres=153;
            $checkBox='<input type="checkbox" name="mybox8" value="1" checked="checked">';
        }else{
            $checkBox='<input type="checkbox" name="mybox8" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas+61.5,$yFilas+$height*2);
        $pdf->setFillColor(51,153,51);
        $pdf->Cell(16, $height, '9', 0, 0, 'C', true, '', 0, false, 'T', 'C');
        if($dataset[0]['prioridad']==9){
            $colorUno=51;
            $colorDos=153;
            $colorTres=51;
            $checkBox='<input type="checkbox" name="mybox9" value="1" checked="checked">';
        }else{
            $checkBox='<input type="checkbox" name="mybox9" value="1">';
        }
        $pdf->writeHTMLCell(5,5,$pdf->getX()-6,$pdf->getY()+1,"$checkBox");
        $pdf->setXY($xFilas,$yFilas+$height*2);
        $pdf->Ln($height*2);
        $pdf->Cell(29.5, $height, '', 0, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->setFillColor(220,220,220);
        $pdf->setTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell(16, $height, 'PRIORIDAD', 1, 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->setFillColor($colorUno,$colorDos,$colorTres);
        $pdf->Cell(16, $height, $dataset[0]['prioridad'], 1, 1, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Ln();
        //$pdf->setFillColor(102,102,51);
		$pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);*/
        $pdf->Cell($width2*5+$width1*2, $height, 'MANTENIMIENTO', 1, 1, 'C', true, '', 0, false, 'T', 'C');
        //$pdf->setFillColor(220,220,220);
        $pdf->SetFillColor($this->rgb1,$this->rgb2,$this->rgb3, true);
        $pdf->Cell($width2*2+$width1+16.5, $height, 'OBSERVACIONES JEFE DE MANTENIMIENTO', 'LBT', 0, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Cell($width2*3+$width1-16.5, $height, 'OBSERVACIONES RESPONSABLE: '.$dataset[0]['desc_responsable'], 'RBT', 1, 'C', true, '', 0, false, 'T', 'C');
        $pdf->Ln(0);
        $pdf->Cell($width2+16, $height, 'EL TRABAJO DEBE SER ATENDIDO POR:', 1, 0, 'R', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        //$pdf->Cell(32, $height, $dataset[0]['nombre_uo'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
		$pdf->MultiCell(61,$height,$dataset[0]['nombre_uo'],1,'L',false,0);
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2+16, $height, 'FECHA DE ENTREGA TENTATIVA OIT:', 1, 0, 'R', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        $pdf->Cell(32, $height, $dataset[0]['fecha_estimada'], 1, 1, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln(0);
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2+16, $height, 'RESPONSABLE ELABORACIÓN OIT/OTT:', 1, 0, 'R', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        //$pdf->Cell(32, $height, $dataset[0]['desc_responsable'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
        $pdf->MultiCell(61,$height,$dataset[0]['desc_responsable'],1,'L',false,0);
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2+16, $height, 'FECHA DE ENTREGA REAL OIT:', 1, 0, 'R', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        $pdf->Cell(32, $height, $dataset[0]['fecha_real'], 1, 1, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln(0);
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2+16, $height, 'FECHA DE RECEPCIÓN:', 1, 0, 'R', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        $pdf->Cell(61, $height, $dataset[0]['fecha_recepcion'], 1, 0, 'C', false, '', 0, false, 'T', 'C');
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);
        $pdf->Cell($width2+16, $height, 'LE CORRESPONDE OIT/OTT Nº:', 1, 0, 'R', true, '', 1, false, 'T', 'C');
        $pdf->Cell(32, $height, '', 1, 1, 'C', false, '', 0, false, 'T', 'C');
        $pdf->Ln(0);
        $checkBoxAlta;
        $checkBoxMedia;
        $checkBoxBaja;
        if($dataset[0]['importancia']=='Alta'){
            $checkBoxAlta='<input type="checkbox" name="boxAlta" value="1" checked="checked">';
        }else{
            $checkBoxAlta='<input type="checkbox" name="boxAlta" value="1">';
        }
        if($dataset[0]['importancia']=='Media'){
            $checkBoxMedia='<input type="checkbox" name="boxMedia" value="1" checked="checked">';
        }else{
            $checkBoxMedia='<input type="checkbox" name="boxMedia" value="1">';
        }
        if($dataset[0]['importancia']=='Baja'){
            $checkBoxBaja='<input type="checkbox" name="boxBaja" value="1" checked="checked">';
        }else{
            $checkBoxBaja='<input type="checkbox" name="boxBaja" value="1">';
        }
        $pdf->Cell($width2+16, $height, 'IMPORTANCIA', 1, 0, 'R', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        $pdf->Cell(21, $height, 'ALTA', 1, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->writeHTMLCell(5,5,$pdf->getX()-7,$pdf->getY()+1,"$checkBoxAlta");
        $pdf->setXY($pdf->getX()+2,$pdf->getY()-1);
        $pdf->Cell(20, $height, 'MEDIA', 1, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->writeHTMLCell(5,5,$pdf->getX()-7,$pdf->getY()+1,"$checkBoxMedia");
        $pdf->setXY($pdf->getX()+2,$pdf->getY()-1);        
        $pdf->Cell(20, $height, 'BAJA', 1, 0, 'L', false, '', 0, false, 'T', 'C');
        $pdf->writeHTMLCell(5,5,$pdf->getX()-7,$pdf->getY()+1,"$checkBoxBaja");
        $pdf->setXY($pdf->getX()+2,$pdf->getY()-1);
		$pdf->SetTextColor($this->rgb1Txt,$this->rgb2Txt,$this->rgb3Txt);        
        $x=$pdf->getX();
        $y=$pdf->getY();        
        $pdf->Cell($width2*3-12, $height*5, '', 1, 0, 'R', false, '', 1, false, 'T', 'C');
        $pdf->setXY($x,$y);
        $pdf->Cell($width2+16, $height, 'OBSERVACIONES DEL RESPONSABLE:', 1, 1, 'R', true, '', 1, false, 'T', 'C');
        $x=$pdf->getX();
        $y=$pdf->getY();
        //$pdf->Cell($width2*3+4, $height, '', 0, 0, 'R', false, '', 1, false, 'T', 'C');
        $pdf->Cell($width2, $height, 'NOTA', 'LBT', 0, 'L', true, '', 1, false, 'T', 'C');
        $pdf->Cell(16, $height, '', 'TB', 0, 'R', true, '', 1, false, 'T', 'C');
        $pdf->Cell(61, $height, '¿SOLICITUD RECHAZADA?                     SI     NO', 1, 0, 'L', true, '', 1, false, 'T', 'C');
		$pdf->SetTextColor(0,0,0);
        $pdf->writeHTMLCell(4,4,$pdf->getX()-12,$pdf->getY()+1,'<input type="checkbox" name="boxSoliSi" value="1">');
        $pdf->writeHTMLCell(4,4,$pdf->getX()+3,$pdf->getY(),'<input type="checkbox" name="boxSoliNo" value="1">');
        $pdf->MultiCell($width2*3-10,$height*4,$dataset[0]['observaciones_resp'],0,'J',false,0);
        $pdf->setXY($x,$y+$height);        
        //$pdf->Cell(106.8, $height*3, '', 1, 0, 'R', false, '', 1, false, 'T', 'C');
		$pdf->MultiCell(106.9,$height*3,'',1,'L',false,0);
        $pdf->setXY($x,$y+$height);
        
        $pdf->Ln(0);
        $pdf->MultiCell($width2*2+$width1,$height*2,$dataset[0]['nota'],0,'L',false,0);
        $pdf->Ln($height*3);
		$pdf->setTextColor(0,0,0);
        $pdf->MultiCell($width2*4,$height,'SEÑOR '. strtoupper($dataset[0]['desc_responsable']).", FAVOR IMPRIMIR, HACER FIRMAR CON EL JEFE DE MANTENIMIENTO Y ARCHIVAR JUNTO A OIT/OTT GENERADA",0,'C',false,0);
        $pdf->Cell($width1, $height, '', 0, 0, 'R',false, '', 1, false, 'T', 'C');
        $pdf->setXY($pdf->getX(),$pdf->getY()+$height);
		$pdf->setTextColor(0,0,0);
		$pdf->Ln(10);
		$pdf->Cell(135, $height, '', 0, 0, 'C',false, '', 1, false, 'T', 'C');
        $pdf->Cell($width2+$width1, $height, 'FIRMA', 'T', 0, 'C',false, '', 1, false, 'T', 'C');
        $pdf->Output($fileName, 'F');
    }
    
}
?>

