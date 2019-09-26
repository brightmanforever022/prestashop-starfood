<?php
/**
 * 2008-2014 Librasoft
 *
 *  For support feel free to contact us on our website at http://www.librasoft.fr/
 *
 *  @author    Librasoft <contact@librasoft.fr>
 *  @copyright 2008-2014 Librasoft
 *  @version   1.0
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once dirname(__FILE__) . '/securimage.php';

$img = new Securimage();

/* You can customize the image by making changes below, some examples are included - remove the "//" to uncomment */

/*$img->ttf_file        = './Quiff.ttf'; */
/*$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text */
/*$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended */
/*$img->image_height    = 90;                                // height in pixels of the image */
/*$img->image_width     = $img->image_height * M_E;          // a good formula for image size based on the height */
/*$img->perturbation    = .75;                               // 1.0 = high distortion, higher numbers = more distortion */
/*$img->image_bg_color  = new Securimage_Color("#0099CC");   // image background color */
/*$img->text_color      = new Securimage_Color("#EAEAEA");   // captcha text color */
/*$img->num_lines       = 8;                                 // how many lines to draw over the image */
/*$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image */
/*$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image */
/*$img->signature_color = new Securimage_Color(rand(0, 64), */
/*                                             rand(64, 128), */
/*                                             rand(128, 255));  // random signature color */

if (Configuration::get('CAPTCHADD_TYPECAPTCHA') == 1)
	$img->captcha_type = Securimage::SI_CAPTCHA_MATHEMATIC;
$img->show();
