<?php

class TrackshipNumbersModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $action = Tools::getValue('action', 'default');
        switch($action){
            case 'add':
                $this->actionAdd();
                break;
            case 'remove':
                $this->actionRemove();
                break;
        }
    }
    
    public function actionAdd()
    {
        $this->ajax = true;
        $requireToken = Configuration::get('TRACKSHIP_TOKEN');
        $paramToken = Tools::getValue('tkn');
        $responseData = array(
            'success' => false,
            'data' => array(),
            'message' => ''
        );
        
        if( ($requireToken != $paramToken) || ($_SERVER['REQUEST_METHOD'] != 'POST') ){
            $responseData['message'] = 'Invalid request';
            echo json_encode($responseData);
            die;
        }
        
        $requestData = array(
            'date_added' => '',
            'id_order' => '',
            'code' => ''
        );
        
        $requestData['id_order'] = (int)Tools::getValue('order_id');
        $requestData['code'] = Tools::getValue('tracking_code');
        $requestData['date_added'] = Tools::getValue('date_added');
        
        if( $requestData['id_order'] <= 0 ){
            $responseData['message'] = 'Order not set';
            echo json_encode($responseData);
            die;
        }
        
        if( empty($requestData['code']) ){
            $responseData['message'] = 'Tracking code not set';
            echo json_encode($responseData);
            die;
        }
        
        $requestData['code'] = trim(htmlentities( strip_tags($requestData['code']) ));
        
        if( empty($requestData['date_added']) || !Validate::isDate($requestData['date_added']) ){
            $responseData['message'] = 'Date not set';
            echo json_encode($responseData);
            die;
        }
        
        foreach( $requestData as $i => $data ){
            $requestData[$i] = pSQL($data);
        }
        
        Db::getInstance()->insert('order_tracking_number', $requestData);
        $responseData['success'] = true;
        $responseData['message'] = 'Tracking code saved';
        echo json_encode($responseData);
        die;
        
    }
    
    public function actionRemove()
    {
        $this->ajax = true;
        //print_r($this->context);
        //echo $this->context->employee->id;
        
        $requireToken = Configuration::get('TRACKSHIP_TOKEN');
        $paramToken = Tools::getValue('tkn');
        $responseData = array(
            'success' => false,
            'id' => null,
        );
        
        if( ($requireToken != $paramToken) || ($_SERVER['REQUEST_METHOD'] != 'POST') ){
            $responseData['message'] = 'Invalid request';
            echo json_encode($responseData);
            die;
        }
        
        $trackshipId = (int)Tools::getValue('id');
        if( empty($trackshipId) ){
            $responseData['message'] = 'Invalid request';
            echo json_encode($responseData);
            die;
        }
        
        Db::getInstance()->delete('order_tracking_number', 'id = '. $trackshipId);
        $responseData['success'] = true;
        $responseData['id'] = $trackshipId;
        echo json_encode($responseData);
        die;
    }
}