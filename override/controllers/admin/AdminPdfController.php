<?php

class AdminPdfController extends AdminPdfControllerCore
{
    public function generatePDF($object, $template)
    {
        $fileName = $object->getInvoiceFileName();
        // send to browser
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=$fileName");

        header('Pragma: no-cache',true);
        header('Expires: 0',true);
        
        if (file_exists($object->getInvoiceFilePath()))
        {
            readfile($object->getInvoiceFilePath());
        }
        else
        {
            $pdf = new PDF($object, $template, Context::getContext()->smarty);
            $pdfFileContent = $pdf->render('S');
            // save file
            file_put_contents($object->getInvoiceFilePath(), $pdfFileContent);
                
            echo $pdfFileContent;
        }
    }

    public function processgeneratePDFlabels() {


        $data = json_decode(Tools::getValue('products'), TRUE);
        
        include_once '../tools/mpdf/mpdf.php';



        $mpdf = new mPDF('utf-8', array(41,89), 0, '', 2, 2, 5, 5, 0, 0);

        ob_start();

        $letter = "";

        $count = count($data);

        foreach($data as $k=>$val) {
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; font-weight: bold; padding-top: 5px'>Artikelnr. / Style:</div>";
            // echo "<br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left;'>".$val['suprefference']."</div>";
            echo "<br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; font-weight: bold; '>Größe / Size:</div>";
            // echo "<br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; padding: 0px'>".$val['size']."</div>";
            echo "<br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; font-weight: bold; padding: 0px'>Farbe / Color:</div>";
            // echo "<br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; padding: 0px'>".$val['dbkname']. "</div>";
            echo "<br><br><br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; font-weight: bold; padding: 0px'>Preis / Price:</div>";
            // echo "<br>";
            echo "<div style='font-family: Arial; font-size: 12px; text-align: left; padding: 0px'>".$val['price']."</div>";
            if(($k+1) < $count) {
                echo "<pagebreak>";
            }
        }



        $html = ob_get_contents();

        ob_end_clean();


        $mpdf->WriteHTML($html);
        $mpdf->Output();
        //print_r($ids);

        return;
    }
}