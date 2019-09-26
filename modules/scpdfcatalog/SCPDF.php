<?php
/**
 * PDF Catalog
 *
 * @category migration_tools
 * @author Store Commander <support@storecommander.com>
 * @copyright 2009-2015 Store Commander
 * @version 2.6.2
 * @license commercial
 *
 **************************************
 **           PDF Catalog             *
 **   http://www.StoreCommander.com   *
 **            V 2.6.2                *
 **************************************
 * +
 * +Languages: EN, FR
 * +PS version: 1.2
 * */

if (!isset($PDFCatalogFromAdmin))
{
	include_once(dirname(__FILE__).'/../../config/config.inc.php');
	include_once(dirname(__FILE__).'/../../init.php');
}

@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
@ini_set("display_errors", "ON");

@set_time_limit(0);
ini_set('memory_limit', '512M');

/*$get_debug = Tools::getValue('debug');
if((isset($get_debug)) && ($get_debug ==1)) { error_reporting(1); ini_set('display_errors', 1); ini_set('display_errors', 'on'); define('_PS_DEBUG_SQL', true); }*/

if (!isset($PDFCatalogFromAdmin) && Tools::getValue('key') != md5(_COOKIE_KEY_))
	die('You don\'t have access to this page.');

$start = 0;
$limit = 0;
if (!isset($paramFormat)){
	global $paramFormat;
	$paramFormat = Tools::getValue('format');
}
if (!isset($divsaveparam)){
	global $divsaveparam;
	$divsaveparam = Tools::getValue('divsaveparam');
}
$pdfConfig = simplexml_load_file(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/catalog/'.$divsaveparam.'.xml');
$title = (string)$pdfConfig->title;
$legalnotice = (string)$pdfConfig->legalnotice;
$filename = (string)$pdfConfig->filename;
$firstpage = (string)$pdfConfig->firstpage;
$tocdisplay = (int)$pdfConfig->tocdisplay;
$format = (string)$pdfConfig->format;
$orderby = (string)$pdfConfig->orderby;
$doctitle = (string)$pdfConfig->doctitle;
$docsubject = (string)$pdfConfig->docsubject;
$doccreator = (string)$pdfConfig->doccreator;
$doclogo=(string)$pdfConfig->doclogo;
$author = (string)$pdfConfig->author;
$orderBy = 'position';
$orderWay = 'ASC';
//if($tocdisplay != ''){
	$tmp = explode('$',$orderby);
	if (count($tmp) == 2){
		$orderBy = Tools::strtolower($tmp[0]);
		if ($orderBy == 'MANUFACTURER' || $orderBy == 'manufacturer') $orderBy = 'manufacturer_name';
		$orderWay = $tmp[1];
	}
//}
if (!Validate::isFilename($filename)) die('Filename error');
if (Tools::strtoupper(Tools::substr($filename,-4,4)) != '.PDF') $filename.= '.pdf';
require (_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/catalog.php');
$pdf = new catalog('P', 'mm', 'A4');
if (!empty($PDFCatalogFromAdmin))
	$pdf->isInAdmin();
$pdf->setTitle($doctitle);
$pdf->setSubject($docsubject);
$pdf->setAuthor($author);
$pdf->setCreator($doccreator);
if($firstpage) $pdf->addFirstPage($firstpage,$title,$legalnotice);

$pdf->createCatalog($format, $start, $limit, $orderBy, $orderWay, $pdfConfig);
if($tocdisplay){
	$pdf->showPageNum = false;
     $pdf->is_toc = true;
	$pdf->AddPage();
     $pdf->is_toc = false;
	$pdf->SetFont($pdf->fontname(), '', 16);
	$pdf->WriteHTMLCell(190,10,10,$pdf->logo_height,$pdf->mod->t('Table of contents'),0,0,0,'C');
	$pdf->SetY($pdf->logo_height+15);
	$pdf->SetFont($pdf->fontname(), '', 10);
	if($firstpage){
		$pdf->addTOC(2,'','.',$pdf->mod->t('Table of contents'));
	}else{
		$pdf->addTOC(1,'','.',$pdf->mod->t('Table of contents'));
	}
} 
$pdf->Output(_PS_MODULE_DIR_.'scpdfcatalog/export/'.$filename, 'F');

$displayNow = Tools::getValue("display_now", 0);
if($displayNow)
	Tools::redirectAdmin(_PS_BASE_URL_.__PS_BASE_URI__."modules/scpdfcatalog/export/".$filename);
/*else
	echo "ok";*/
