{*
* 2008-2014 Librasoft
*
*  @author    Librasoft <contact@librasoft.fr>
*  @copyright 2008-2014 Librasoft
*  @version   1.0
*  International Registered Trademark & Property of Librasoft
*
*  For support feel free to contact us on our website at http://www.librasoft.fr/
*
*}
<!-- Module Captchadd By http://www.librasoft.fr/ -->
<div class="captchaContainer">
    <img id='captcha' src='{$base_dir|escape:'htmlall'}modules/captchadd/securimage/securimage_show.php' alt='CAPTCHA Image' />
    <input type='text' name='captcha_code' size='10' maxlength='6' />
    <a id='lien'><img src='{$base_dir|escape:'htmlall'}modules/captchadd/securimage/images/refresh.png' style='cursor: pointer'></a></div>
{literal}<script type='text/javascript'>
    $(document).ready(function(){
        $('#lien').click(function(){
            $('#captcha').attr('src','{/literal}{$base_dir|escape:'htmlall'}{literal}modules/captchadd/securimage/securimage_show.php?' + Math.random());
        });
    });
</script>{/literal}
<!-- Module Captchadd By http://www.librasoft.fr/ -->