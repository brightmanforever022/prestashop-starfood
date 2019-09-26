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

$id_lang = Tools::getValue('id_lang', null);

// Traitement de l'envoi du message
if(Tools::getValue('mail_sent', null)){
    $errors =array();
    if(Tools::getValue('id_customer') == 0) {
        $errors[] = _l('Please select a customer');
    } elseif(Tools::getValue('id_shop') == 0) {
        $errors[] = _l('Please select a shop');
    } else {

        $id_shop = Tools::getValue('id_shop');
        $id_customer = Tools::getValue('id_customer');

        // Subject
        $subject = Tools::getValue('subject');
        if(!isset($subject) || empty($subject)){
            $errors[] = _l('Wrong or empty subject');
        }

        // Message
        $message = Tools::getValue('message');
        if(!isset($message) || empty($message)){
            $errors[] = _l('Wrong or empty message');
        }

        // Fichiers
        $file_attachment = null;
        if (!empty($_FILES['files']['name'])) {
            $file_attachment['content'] = file_get_contents($_FILES['files']['tmp_name']);
            $file_attachment['name'] = $filename;
            $file_attachment['mime'] = $_FILES['files']['type'];

            //Copy in upload folder
            $extension = Tools::strtolower(substr($_FILES['files']['name'], -4));
            $file_attachment['rename'] = uniqid().$extension;
            if(!move_uploaded_file($_FILES['files']['tmp_name'], _PS_UPLOAD_DIR_.'/'.$file_attachment['rename'])) {
                $errors[] = _l('Upload file error');
            }
        }

        if(count($errors) == 0){
            //Envoi de l'email
            $customer = new Customer($id_customer);

            $sql = 'SELECT * 
                    FROM '._DB_PREFIX_.'customer_thread
                    WHERE id_customer = '.(int)$customer->id.' 
                    AND id_order = 0';
            $res = Db::getInstance()->getRow($sql);
            if(!$res) {
                $customer_thread = new CustomerThread();
                $customer_thread->id_contact = 0;
                $customer_thread->id_customer = (int)$customer->id;
                $customer_thread->id_shop = (int)$id_shop;
                $customer_thread->id_lang = (int)$id_lang;
                $customer_thread->email = $customer->email;
                $customer_thread->status = 'open';
                $customer_thread->token = Tools::passwdGen(12);
                $customer_thread->save();
                $id_customer_thread = (int)$customer_thread->id;
                $token = $customer_thread->token;
            } else {
                $id_customer_thread = (int)$res['id_customer_thread'];
                $token = $res['token'];
            }

            if($id_customer_thread && $token)
            {
                $link = new Link();
                if(version_compare(_PS_VERSION_, '1.4.1.0', '>=')) {
                    $link = new Link(Tools::getProtocol(),Tools::getProtocol());
                }
                $tpl_var = array(
                    '{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname,
                    '{reply}' => $message,
                    '{link}' => Tools::url(
                        $link->getPageLink('contact', true, null, null, false, (int)$id_shop),
                        'id_customer_thread='.(int)$id_customer_thread.'&token='.$token
                    ),
                );
                $to_name = $customer->firstname . ' ' . $customer->lastname;
                if (Mail::Send($customer->id_lang, 'reply_msg', $subject, $tpl_var, $customer->email, $to_name, null, null, $file_attachment))
                {
                    $customer_message = new CustomerMessage();
                    $customer_message->id_customer_thread = (int)$id_customer_thread;
                    $customer_message->id_employee = (int)$sc_agent->id_employee;
                    $customer_message->message = $message;
                    $customer_message->private = 0;
                    $customer_message->file_name = $file_attachment['rename'];
                    $customer_message->save();

                    echo _l('Mail sent');
                    die();
                } else {
                    $errors[] = _l('Error to sending email');
                }
            } else {
                $errors[] = _l('Empty id_customer_thread or customer thread token');
            }
        }
    }

    // Si erreurs
    if(count($errors) > 0) {
        echo '<div id="errors">';
        echo '<p>'._l('Some errors found :').'</p>';
        foreach($errors as $error) {
            echo $error."<br/>";
        }
        echo '</div>';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
    <script type="text/javascript" src="lib/js/jquery.typewatch.js"></script>
    <style>
        #errors {
            padding: 5px;
            background: #d81b1b;
            color: #fff;
            margin-bottom: 10px;
        }
        #errors p{
            margin:0;
        }
        #custResult{
            height: 66px;
            border: 1px solid rgb(169, 169, 169);
            overflow: auto;
        }
        #selectCust {
            margin:0;
            padding:5px;
        }
        #selectCust li{
            list-style:none;
            padding:5px 0;
        }
        #selectCust li a {
            text-decoration: none;
            color:#000;
        }
        #selectCust li a:hover,
        #selectCust li a.selected {
            color:#31ab3c;
        }
        #bottom{
            text-align: right;
        }
        #bottom > input[type="file"] {
            float:left;
        }

    </style>
</head>
<body style="padding:0px;margin:0px;font-family: Tahoma;font-size:13px;">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="mail_sent" value="1"/>
        <input id="id_customer" type="hidden" name="id_customer" value="0"/>
        <label for="customer"><?php echo _l('Select customer'); ?> :</label><br/>
        <input id="customer" type="text" name="customer" /><br/><br/>
        <div id="custResult"></div><br/>
        <label for="id_shop"><?php echo _l('Select shop'); ?> :</label><br/>
        <select name="id_shop">
            <?php
            $shops = Shop::getShops(false);
            echo '<option value="0">--</option>';
            foreach($shops as $shop) {
                echo '<option value="'.(int)$shop['id_shop'].'">'.$shop['name'].'</option>';
            }
            ?>
        </select><br/><br/>
        <label for="subject"><?php echo _l('Subject'); ?> :</label><br/>
        <input type="text" name="subject" /><br/><br/>
        <label for="message"><?php echo _l('Message'); ?> :</label><br/>
        <textarea name="message" rows="10" style="-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width: 100%;"></textarea><br/><br/>
        <label for="files"><?php echo _l('File (only one file max)'); ?> :</label><br/>
        <div id="bottom">
            <input id="fieldfiles" name="files" type="file"/>
            <input type="submit" value="<?php echo _l('Send mail'); ?>" />
        </div>
    </form>
<script type="text/javascript">
    $(document).ready(function() {
        $("input#customer").typeWatch({
            captureLength: 2,
            highlight: true,
            wait: 100,
            callback: function () {
                $.ajax({
                    type: "POST",
                    url: "./index.php?ajax=1&act=cusm_send_mail_get",
                    async: true,
                    dataType: "json",
                    data: {
                        action: "searchCustomers",
                        customer_search: $('#customer').val()
                    },
                    success: function (res) {
                        if (res.found) {
                            html = '<ul id="selectCust">';
                            $.each(res.customers, function () {
                                html += '<li id="cus'+this.id_customer+'"><a href="#" onclick="useThisCustomer('+this.id_customer+');return false;">'+this.fullname_and_email+'</a></li>';
                            });
                            html += "</ul>";
                        } else {
                            html = '<div id="nocust"><?php echo _l('No customers found'); ?></div>';
                        }
                        $("#custResult").html('');
                        $("#custResult").html(html);
                    }
                });
            }
        });
    });

    function useThisCustomer(id_customer)
    {
        $('#selectCust li a').removeClass('selected');
        $('#selectCust li#cus'+id_customer+' a').addClass('selected');
        $('#id_customer').val(id_customer);
    }
</script>
</body>
</html>
