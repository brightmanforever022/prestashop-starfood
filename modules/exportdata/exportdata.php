<?php
/**
 * Export Data Module
 *
 * @version  1.4.4
 * @date  29-10-2014
 *
 *
 * @author    azelab
 * @copyright All rights by azelab
 * @license   Commercial license
 * Support by mail: support@azelab.com
 * Skype: eibrahimov
 */

if (!defined('_PS_VERSION_'))
    exit;

class Exportdata extends Module
{
    public $entities = array();
    public $languages = array();
    protected $error = false;

    public function __construct()
    {
        $this->name = 'exportdata';
        $this->tab = 'others';
        $this->version = '1.4.4';
        $this->author = 'Azelab';
        $this->module_key = '9581b42794aee77c5be55b1342e37671';
        parent::__construct();
        $this->displayName = $this->l('Export Data');
        $this->description = $this->l('Export all data from Prestashop 1.4 to 1.5');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->entities = array(
            $this->l('Categories'),
            $this->l('Products'),
            $this->l('Combinations'),
            $this->l('Customers'),
            $this->l('Addresses'),
            $this->l('Manufacturers'),
            $this->l('Suppliers'),
        );
        foreach (Language::getLanguages() as $language)
            $this->languages[$language['id_lang']] = $language['name'];
    }

    public function install()
    {
        if (!parent::install())
            return false;
        else
            return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;
        else
            return true;
    }

    public function getContent()
    {
        $this->_html = "<h2>" . $this->displayName . "</h2>";
        if (Tools::isSubmit('submitExportData')) {
            $this->entitiesTmp = array_flip($this->entities);
            switch ((int)Tools::getValue('entity')) {
                case $this->entitiesTmp[$this->l('Combinations')]:
                    break;
                case $this->entitiesTmp[$this->l('Categories')]:
                    break;
                case $this->entitiesTmp[$this->l('Products')]:
                    break;
                case $this->entitiesTmp[$this->l('Customers')]:
                    break;
                case $this->entitiesTmp[$this->l('Addresses')]:
                    break;
                case $this->entitiesTmp[$this->l('Manufacturers')]:
                    break;
                case $this->entitiesTmp[$this->l('Suppliers')]:
            }
            /* Export csv file */
//                Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)(Tools::getValue('always_display')));
            $this->_html
                .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('Confirmation') . '" />'
                . $this->l('Export data successfully') . '</div>';
        }
//            $this->initJS();
        $this->_displayForm();

        return $this->_html;
    }

    public function _displayForm()
    {
        $this->_html .= '<form accept-charset="utf-8" method="post" action="' . __PS_BASE_URI__ . 'modules/exportdata/exportdata-csv.php" id="export_form">
            <input type="hidden" value="' . Tools::getAdminTokenLite('AdminModules') . '" name="token" >
			<fieldset>
				<legend><img src="' . $this->_path . 'logo.gif" alt="" title="" />' . $this->l('Settings') . '</legend>';
        $this->_html .= $this->_displayEntitiesHtml();
        $this->_html .= $this->_displayLanguagesHtml();
        $this->_html
            .= '
            <input type="hidden" value="export" name="export" id="export">
            <label for="delimiter">' . $this->l('Field separator') . ':</label>
            <div class="margin-form">
            <input type="text" value=";" size="1" name="delimiter" id="delimiter">
            </div>
            <label for="multi_delimiter">' . $this->l('Multiple value separator') . ':</label>
            <div class="margin-form">
            <input type="text" value="," size="1" name="multi_delimiter" id="multi_delimiter">
            </div>
            <!--<label for="image_url">' . $this->l('Images URL') . ':</label>-->
            <div class="margin-form">
            <input type="hidden" value="" size="20" name="image_url" id="image_url">
            </div>
            <div class="margin-form">
            <a onclick="$(\'#export_form\').submit();" id="exportnow" class="export_btn button">' . $this->l('Export Now')
            . '</a>
            </div>
            </fieldset>
        </form>';
    }

    public function _displayEntitiesHtml()
    {
        $result
            = '
            <label for="entity">' . $this->l('What kind of entity would you like to export?') . ':</label>
            <div class="margin-form">
            <select id="entity" name="entity">';
        foreach ($this->entities AS $key => $val)
            $result .= '<option value="' . $key . '">' . $val . '</option>';

        return $result . '</select></div>';
    }

    public function _displayLanguagesHtml()
    {
        $result
            = '<br />
            <label for="lang">' . $this->l('Language of the file') . ':</label>
            <div class="margin-form">
            <select size="1" name="lang" id="lang">';
        foreach ($this->languages AS $key => $val)
            $result .= '<option value="' . $key . '">' . $val . '</option>';

        return $result . '</select></div>';
    }
    /*public function initJS(){
        $this->_html .= '
        <script type="text/javascript">
            $(document).ready(function(){
                function pexport() {
                    $("#export_form").submit();
                }
            });
        </script>';
    }*/
    /*        public function array_to_csv_download($array, $filename = "export.csv", $delimiter = ";")
            {
                // open raw memory as file so no temp files needed, you might run out of memory though
    //            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    //            header('Content-Description: File Transfer');
    //            header("Content-type: text/csv");
    //            header("Content-Disposition: attachment; filename={$filename}");
    //            header("Expires: 0");
    //            header("Pragma: public");
    //            $f = @fopen( 'php://output', 'w' );
                $f = fopen('php://memory', 'w');
                // loop over the input array
                foreach ($array as $line) {
                    // generate csv lines from the inner arrays
                    fputcsv($f, $line, $delimiter);
                }
                // rewrind the "file" with the csv lines
                fseek($f, 0);
                // tell the browser it's going to be a csv file
                header('Content-Type: application/csv');
                // tell the browser we want to save it instead of displaying it
                header('Content-Disposition: attachement; filename="'.$filename.'"');
                // make php send the generated csv lines to the browser
                fpassthru($f);
            }*/
}