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
 

$id_discussion=intval(Tools::getValue('id',0));
$id_lang=intval(Tools::getValue('id_lang',0));

$errors = array();
$success = false;

function displayMessageEmail($message_infos)
{
	$return = "";
	if(!empty($message_infos["message"]))
	{
		$name = $message_infos['customer_name']." ("._l("Customer").")";
		if(!empty($message_infos['id_employee']))
		{
			$name = $message_infos['employee_name']." ("._l("Advisor").")";
		}
		
		$message_infos['message'] = preg_replace(
				'/(https?:\/\/[a-z0-9#%&_=\(\)\.\? \+\-@\/]{6,1000})([\s\n<])/Uui',
				'<a href="\1">\1</a>\2',
				html_entity_decode($message_infos['message'],
						ENT_NOQUOTES, 'UTF-8')
		);
		
		$return .= '<p><strong>'.$name.'</strong> ('.$message_infos["date_add"].') : '.$message_infos["message"].'</p><br/>';
	}
	
	return $return;
}

if(isset($_POST["submitSend"]))
{
	if(empty($_POST["message"]))
		$errors[] = _l('You must write a message to send an answer');
	
	if(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($_POST["private"]))
		$private = $_POST["private"];
	
	if(!empty($_POST["id_employee"]))
		$transert_to = $_POST["id_employee"];
	
	if (isset($_FILES) && !empty($_FILES['file']['name']) && $_FILES['file']['error'] != 0)
		$errors[] = _l('An error occured during file upload. Please try again.');
	else
	{
		$file_attachment = null;
		if (!empty($_FILES['file']['name']))
		{
			$file_attachment['content'] = file_get_contents($_FILES['file']['tmp_name']);
			$file_attachment['name'] = $_FILES['file']['name'];
			$file_attachment['mime'] = $_FILES['file']['type'];

			//Copy in upload folder
			$extension = Tools::strtolower(substr($_FILES['file']['name'], -4));
			$file_attachment['rename'] = uniqid().$extension;
			move_uploaded_file($_FILES['file']['tmp_name'], _PS_UPLOAD_DIR_.'/'.$file_attachment['rename']);
		}
	}
	
	if(empty($errors))
	{
		// REPONSE NORMALE
		if(empty($transert_to))
		{
			$ct = new CustomerThread($id_discussion);
			$cm = new CustomerMessage();
			$cm->id_employee = (int)$sc_agent->id_employee;
			$cm->id_customer_thread = $ct->id;
			if (!empty($file_attachment['rename']) && file_exists(_PS_UPLOAD_DIR_.'/'.$file_attachment['rename'])) {
				$cm->file_name = pSQL($file_attachment['rename']);
			}

			$cm->message = /*Tools::htmlentitiesutf8(*/Tools::getValue('message');//);
			$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$cm->private = (int)$private;
			
			$customer = new Customer($ct->id_customer);
			
			if ($cm->add())
			{
				$link = new Link();
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					$params = array(
							'{reply}' => nl2br(Tools::getValue('message')),
							'{link}' => Tools::url(
									$link->getPageLink('contact', true),
									'id_customer_thread='.(int)$ct->id.'&token='.$ct->token
							),
							'{firstname}' => $customer->firstname,
							'{lastname}' => $customer->lastname
					);
				}
				else
				{
					$params = array(
							'{reply}' => nl2br(Tools::getValue('message')),
							'{link}' => $link->getPageLink('contact', true),
									'id_customer_thread='.(int)$ct->id.'&token='.$ct->token
					);
				}
				
				
				
				// Envoi du message au client
				$to_update = false;
				if($cm->private)
					$to_update=true;
				else
				{
					$send_email = true;
					if(version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_MAIL_METHOD")==3) 
						$send_email = false;
					if($send_email)
					{
						if(!SCMS)
						{
							$to_update=Mail::Send(
							(int)$ct->id_lang,
							'reply_msg',
							sprintf(Mail::l('An answer to your message is available #ct%1$s #tc%2$s', $ct->id_lang), $ct->id, $ct->token),
							$params,
							$ct->email,
							null,
							null,
							null,
							$file_attachment,
							null,
							_PS_MAIL_DIR_,
							true);
						}
						else
						{
							$to_update=Mail::Send(
							(int)$ct->id_lang,
							'reply_msg',
							sprintf(Mail::l('An answer to your message is available #ct%1$s #tc%2$s', $ct->id_lang), $ct->id, $ct->token),
							$params,
							$ct->email,
							null,
							null,
							null,
							$file_attachment,
							null,
							_PS_MAIL_DIR_,
							true,
							SCI::getSelectedShop());
						}
					}
					else
					{
						$to_update=true;
					}
				}
				
				if ($to_update)
				{
					//$ct->status = 'closed';
					$ct->update();
					$success = true;
				}
			}
		}
		// TRANSFERT A UN EMPLOYEE
		elseif(!empty($transert_to))
		{
			$messages = Db::getInstance()->executeS('
					SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name,
						CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
					FROM '._DB_PREFIX_.'customer_thread ct
					LEFT JOIN '._DB_PREFIX_.'customer_message cm
						ON (ct.id_customer_thread = cm.id_customer_thread)
					LEFT JOIN '._DB_PREFIX_.'contact_lang cl
						ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.(int)$id_lang.')
					LEFT OUTER JOIN '._DB_PREFIX_.'employee e
						ON e.id_employee = cm.id_employee
					LEFT OUTER JOIN '._DB_PREFIX_.'customer c
						ON (c.email = ct.email)
					WHERE ct.id_customer_thread = '.(int)$id_discussion.'
					ORDER BY cm.date_add DESC
				');
			$output = '';
			foreach ($messages as $message)
			{
				$output .= displayMessageEmail($message);
			}
			
			$cm = new CustomerMessage();
			$cm->id_employee = (int)$sc_agent->id_employee;
			$cm->id_customer_thread = (int)$id_discussion;
			$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
			if (!empty($file_attachment['rename']) && file_exists(_PS_UPLOAD_DIR_.'/'.$file_attachment['rename'])) {
				$cm->file_name = pSQL($file_attachment['rename']);
			}
			$current_employee = $sc_agent;
			
			$id_employee = (int)$transert_to;
			$employee = new Employee($id_employee);
			$email = $employee->email;
			
			if ($id_employee && $employee && Validate::isLoadedObject($employee))
			{
				$to_update = false;
				$send_email = true;
				if(version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_MAIL_METHOD")==3)
					$send_email = false;
				
				if($send_email)
				{
					if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
					{
						$params = array(
								'{messages}' => stripslashes($output),
								'{employee}' => $current_employee->firstname.' '.$current_employee->lastname,
								'{comment}' => stripslashes(Tools::nl2br($_POST['message'])),
								'{firstname}' => $employee->firstname,
								'{lastname}' => $employee->lastname,
						);
					}
					else 
					{
						$params = array(
							'{messages}' => nl2br(stripslashes($output)),
							'{employee}' => $current_employee->firstname.' '.$current_employee->lastname,
							'{comment}' => stripslashes($_POST['message']));
					}
					$to_update = Mail::Send(
						$id_lang,
						'forward_msg',
						Mail::l('Fwd: Customer message', $id_lang),
						$params,
						$employee->email,
						$employee->firstname.' '.$employee->lastname,
						$current_employee->email,
						$current_employee->firstname.' '.$current_employee->lastname,
						$file_attachment, 
						null, 
						_PS_MAIL_DIR_, 
						true);
				}
				else
					$to_update=true;
				if ($to_update)
				{
					if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						$cm->private = 1;
					$cm->message = _l('Message forwarded to').' '.$employee->firstname.' '.$employee->lastname."\n"._l('Comment:').' '.$_POST['message'];
					$cm->add();
					$success = true;
				}
			}
		}
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Affiliation</title>
<style type="text/css">

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	line-height: 1;
	color: #000000;
    font-family: Tahoma;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}

label {
	width: 130px;
	font-size: 11px;
	color: #000000;
	font-weight: bold;
	float: left;
	line-height: 20px;
}

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
}
.btn.submit {float: right; margin-left: 20px;}

.left {float: left;}
.right {float: right; margin-left: 20px; height: 27px; line-height: 27px;}


.error {
padding: 10px;
border: 1px solid #ce0000;
color: #ce0000;
background: #ffe4e4;
margin: 10px;
font-size: 11px;
}

</style>
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script type="text/javascript">
<?php if($success) { ?>
parent.successAnswer();
<?php } ?>

function orderOverwriteMessage(sl, text)
{
	var $zone = $('#txt_msg');
	var sl_value = sl.options[sl.selectedIndex].value;

	if (sl_value != '0')
	{
		if ($zone.val().length > 0 && !confirm(text))
			return ;
		$zone.val(sl_value);
	}
}
</script>
</head>
<body>

	<form method="post" action="" enctype="multipart/form-data">
	
		<?php 
		if(!empty($errors)) 
		{
			echo '<div class="error">';
			foreach($errors as $error)
			{
				echo $error.'<br/>';
			}
			echo '</div>';
		} 
		?>
	
		<textarea name="message" id="txt_msg" style="height: 5em; width: 95%; margin-bottom: 1em;"></textarea>
	
		
		<label><?php echo _l('Attachment')?></label><input type="file" name="file" value="" />
		
		
		<button type="submit" name="submitSend" class="btn submit"><?php echo _l('Send')?></button>
		
		<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
		<label class="right" style="width: auto; margin-left: 5px;"><?php echo _l('Private')?></label>
		<input type="checkbox" name="private" value="1" class="right" />
		<?php } ?>
		
		<select name="id_employee" class="right">
			<option value=""><?php echo _l('Transfer to...')?></option>
			<?php 
			$employees = Employee::getEmployees();
			foreach ($employees as $employee)
			{
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					echo '<option value="'.$employee["id_employee"].'">'.$employee["firstname"].' '.$employee["lastname"].'</option>';
				else
					echo '<option value="'.$employee["id_employee"].'">'.$employee["name"].'</option>';
			}
			?>
		</select>
		
		<select name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '<?php echo _l('Do you want to overwrite your existing message?')?>')" class="right">
			<option value=""><?php echo _l('Choose a standard message')?></option>
			<?php 
			$orderMessages = OrderMessage::getOrderMessages((int)$id_lang);
			foreach ($orderMessages as $orderMessage)
			{
				echo '<option value="'.strip_tags($orderMessage['message']).'">'.$orderMessage['name'].'</option>';
			}
			?>
		</select>
		
		<div style="clear: both;"></div>
	</form>

</body>
</html>
