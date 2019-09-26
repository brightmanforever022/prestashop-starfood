<?php
/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

class NewsletterPro_Swift_Plugins_TemplateDecoratorPlugin implements NewsletterPro_Swift_Events_SendListener, NewsletterPro_Swift_Plugins_TemplateDecorator_Replacement
{
	private $_replacements;

    private $_originalBody;

    private $_originalSubject;

    private $_originalHeaders = array();

    private $_originalChildBodies = array();

    private $_lastMessage;

	public function __construct($replacements)
	{
        $this->setReplacements($replacements);
	}

    public static function newInstance($replacements)
    {
        return new self($replacements);
    }

	public function setReplacements($replacements)
	{
		if (!($replacements instanceof NewsletterPro_Swift_Plugins_TemplateDecorator_Replacement))
            $this->_replacements = (array)$replacements;
		else
			$this->_replacements = $replacements;
	}

    public function getReplacements()
    {
        return $this->_replacements;
    }

	public function beforeSendPerformed(NewsletterPro_Swift_Events_SendEvent $evt)
	{
        $message = $evt->getMessage();
        $this->_restoreMessage($message);
        $to = array_keys($message->getTo());
        $address = array_shift($to);

        if (!$message->getBody() || !$message->getSubject())
        {
            $template_default = $this->getTemplateFor($address);

            $this->_originalBody = $template_default['body'];
            $message->setBody($this->_originalBody, 'text/html');

            $this->_originalSubject = $template_default['title'];
            $message->setSubject($this->_originalSubject);
        }

        if ($template = $this->getTemplateFor($address)) 
        {
            // set to email and the full name
            $message->setTo($this->getTo());

            $body_final    = $template['body'];
            $body_final = NewsletterPro::getInstance()->embedImages($message, $body_final);

            $subject_final = $template['title'];

            $body_original    = $message->getBody();
            $subject_original = $message->getSubject();

            if ($body_original != $body_final) 
            {
                $this->_originalBody = $body_original;
                $message->setBody($body_final, 'text/html');
            }

            if ($subject_original != $subject_final)
            {
                $this->_originalSubject = $subject_original;
                $message->setSubject($subject_final);
            }

            $this->_lastMessage = $message;
        }
	}

	public function getTemplateFor($address = null)
    {
        if ($this->_replacements instanceof NewsletterPro_Swift_Plugins_TemplateDecorator_Replacement) 
            return $this->_replacements->getTemplateFor($address);
        else 
        {
            return isset($this->_replacements[$address])
                ? $this->_replacements[$address]
                : null
                ;
        }
    }

    public function getTo()
    {
        if ($this->_replacements instanceof NewsletterPro_Swift_Plugins_TemplateDecorator_Replacement) 
            return $this->_replacements->template->user->to();
    }

	public function sendPerformed(NewsletterPro_Swift_Events_SendEvent $evt)
    {
        $this->_restoreMessage($evt->getMessage());
    }

	private function _restoreMessage(NewsletterPro_Swift_Mime_Message $message)
    {
        if ($this->_lastMessage === $message) 
        {
            if (isset($this->_originalBody)) 
            {
                $message->setBody($this->_originalBody);
                $this->_originalBody = null;
            }
            
            if (!empty($this->_originalHeaders)) 
            {
                foreach ($message->getHeaders()->getAll() as $header) 
                {
                    if (array_key_exists($header->getFieldName(), $this->_originalHeaders)) 
                        $header->setFieldBodyModel($this->_originalHeaders[$header->getFieldName()]);
                }

                $this->_originalHeaders = array();
            }

            if (!empty($this->_originalChildBodies)) 
            {
                $children = (array) $message->getChildren();
            
                foreach ($children as $child) 
                {
                    $id = $child->getId();
                    if (array_key_exists($id, $this->_originalChildBodies))
                        $child->setBody($this->_originalChildBodies[$id]);
                }

                $this->_originalChildBodies = array();
            }

            $this->_lastMessage = null;
        }
    }
}