<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/

    $action = Tools::getValue('action','0');
    $value = Tools::getValue('value','0');
    $product_list = Tools::getValue('product_list','0');
    $attachment_list = Tools::getValue('attachment_list','0');
    $description = Tools::getValue('description','0');
    $name = Tools::getValue('name','0');
    $colname = Tools::getValue('colname','0');
    $lang = Tools::getValue('lang','0');
    $fields_lang = array();
    $idlangByISO = array();
    $todo = array();
    $todo_lang = array();

    $id_lang = intval(Tools::getValue('id_lang'));
    $ids = intval(Tools::getValue('ids', 0));
    $id_attachment_download = intval(Tools::getValue('id_attachment_download', 0));
    $file_size = @filesize(_PS_DOWNLOAD_DIR_ . $file);
    $error = "";
    $success = false;
    $uploadable = true;
    $error_uploadable = array();

    if (isset($_POST["submitUpload"])) {
        if (empty($_FILES['download']['name'])) {
            $error = _l('You must select a file to upload.', 1);
        }
        if (empty($id_attachment_download)) {
            $exist = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT `id_attachment`
                    FROM `' . _DB_PREFIX_ . 'attachment`
                    WHERE `id_attachment` = ' . (int)$ids . '
                ');

            if (empty($error) && !empty($exist[0]["id_product_download"])) {
                $error = _l('This product already has a downloadable file.', 1);
            }
        }
        if (empty($error)) {
            $dossier = _PS_DOWNLOAD_DIR_;
            $display_filename = basename($_FILES['download']['name']);
            $filename = basename($_FILES['download']['name']);
            $mime = basename($_FILES['download']['type']);
            if (move_uploaded_file($_FILES['download']['tmp_name'], $dossier . $filename)) {
                $download = new Attachment($id_attachment_download);

                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                    @unlink(_PS_DOWNLOAD_DIR_ . "/" . $download->filename);
                } else {
                    @unlink(_PS_DOWNLOAD_DIR_ . "/" . $download->physically_filename);
                }
                $download->display_filename = $display_filename;

                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                    $download->filename = $filename;
                } else {
                    $download->physically_filename = $filename;
                }
                if ($download->date_expiration == "0000-00-00 00:00:00") {
                    $download->date_expiration = null;
                }
                
                $download->save();
                $success = true;
                $sql = "UPDATE ". _DB_PREFIX_ ."attachment SET file='".pSQL($filename)."', file_name='".pSQL($filename)."', file_size='".pSQL($file_size)."', mime='".pSQL($mime)."' WHERE id_attachment=".(int)$id_attachment_download;
                Db::getInstance()->Execute($sql);                
            } else {
                $error = _l('An error occured during file upload. Please try again.', 1);
            }
        }
    }
    ?>
    <style type="text/css">
        .btn {
            background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
            border: 1px solid #a4bed4;
            color: #34404b;
            font-size: 11px;
            height: 27px;
            overflow: hidden;
            position: relative;
            font-weight: bold;
            cursor: pointer;
            float: right;
            margin-top: 6px;
        }
    </style>
    <script type="text/javascript">
        <?php if(!empty($error)) { ?>
        parent.dhtmlx.message({text: '<?php echo $error; ?>', type: 'error', expire: 10000});
        <?php }
        if($success) { ?>
        parent.displayAttachments();
        parent.prop_tb._attachmentsLayout.cells('b').collapse();
        <?php } ?>
    </script>
    <?php if ($uploadable) { ?>
        <form method="POST" action="" enctype="multipart/form-data">
            Fichier : <input type="file" name="download"/>
            <button class="btn" name="submitUpload" type="submit"><?php echo _l('Upload file'); ?></button>
            <input type="hidden" name="id_product" value="<?php echo $id_product; ?>"/>
        </form>
    <?php }
            else 
            {
                foreach ($error_uploadable as $error) 
                {
                echo '<strong>' . $error . '</strong><br/><br/>';
                }
            }

