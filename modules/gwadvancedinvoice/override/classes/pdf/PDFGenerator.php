<?php
/**
* This file will override class PDFGeneratorCore. Do not modify this file if you want to upgrade the module in future
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

class PDFGenerator extends PDFGeneratorCore
{
    public function writePageGw($mgheader=0,$mgfooter=0,$mgcontent='10-10-10-10')
	{
        $this->SetHeaderMargin($mgheader);
		$this->SetFooterMargin($mgfooter);
        $mgcontents = explode('-',$mgcontent);
		$this->setMargins((isset($mgcontents[0]) ? (int)$mgcontents[0] : 0), (isset($mgcontents[1]) ? (int)$mgcontents[1] : 0), (isset($mgcontents[2]) ? (int)$mgcontents[2] : 0));
        $this->setAutoPageBreak(true,(isset($mgcontents[3]) ? (int)$mgcontents[3] : 0));
		$this->AddPage();
		$this->writeHTML($this->content, true, false, true, false, '');
     }
    public function setCurOrientation($format='A4',$orientation = 'P'){
         $this->setPageFormat($format, $orientation);
  	}
    public function renderInvoice($filename, $display = true)
	{
		if (empty($filename))
			throw new PrestaShopException('Missing filename.');
		$this->lastPage();
		if ($display === true)
			$output = 'D';
		elseif ($display === false)
			$output = 'S';
		elseif ($display == 'D')
			$output = 'D';
		elseif ($display == 'S')
			$output = 'S';
		elseif ($display == 'F')
			$output = 'F';
		else
			$output = 'I';
		return $this->output($filename, $output);
	}
    public function addWaterMark($mark='',$img='',$rotatemark = 45,$rotateimg = 0,$alpha='0.1',$tipoLetra='Helvetica',$tamanoLetra=35,$estiloLetra='B'){
        $nbr_page = $this->getNumPages();
        $myPageWidth = $this->getPageWidth();
        $myPageHeight = $this->getPageHeight();
        $myXMark = 0;
        $myYMark = 0;
        if($mark !=''){
            $widthCadena = $this->GetStringWidth(trim($mark), $tipoLetra, $estiloLetra, $tamanoLetra, false );
            $factorCentrado = round(($widthCadena * sin(deg2rad($rotatemark))) / 2 ,0);
            $myXMark = ( $myPageWidth / 2 ) - $factorCentrado;
            $myYMark = ( $myPageHeight / 2 ) + $factorCentrado;
            
        }
        $myX = 0;
        $myY = 0;
        $ImageW = 0;
        $ImageH = 0;
        if($img !=''){
            $imgwh = getimagesize($img);
            
            if($imgwh){
                $ImageW = $imgwh[0]/2.83;
                $ImageH = $imgwh[1]/2.83;
                $myX = round($myPageWidth / 2  - $ImageW/2);
                $myY = round($myPageHeight / 2  - $ImageH/2);
            }
        }
        for($i=1;$i<=$nbr_page;$i++){
            $this->setPage($i);
            if($mark !=''){
                $this->SetAlpha($alpha);
                $this->StartTransform();
                $this->SetFont($tipoLetra, $estiloLetra, $tamanoLetra);
                $this->Rotate($rotatemark, $myXMark, $myYMark);
                $this->Text($myXMark, $myYMark ,trim($mark));
                $this->StopTransform();
                $this->SetAlpha(1);
            }
            if($img !=''){
                if($ImageW > 0){
                    $this->SetAlpha($alpha);
                    $this->StartTransform();
                    $this->Rotate($rotateimg, $myX, $myY);
                    $this->Image($img, $myX, $myY, $ImageW, $ImageH, '', '', '', false);
                    $this->StopTransform();
                    $this->SetAlpha(1);
                }
            }
            
            
        }
    }
}
?>