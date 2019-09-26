<?php

class AgentVoucher extends ObjectModel
{
    public $id;


    public static $definition = array(
        'table' => 'agentcomm_agent_voucher',
        'primary' => 'id_agent_voucher',
        'multilang' => false,
        'fields' => array(
            'id_agent' => array('type' => self::TYPE_INT),
            'id_voucher' => array('type' => self::TYPE_INT),
            'status' => array('type' => self::TYPE_INT),
            'agent_commision' => array('type' => self::TYPE_INT)
        )
    );

    
}