<?php
class ControllerExtensionPaymentEservice extends Controller{
    
    private $error = array();
    private $showUrlFields4Sandbox = 0; // parameter to determine whether the sandbox urls should show or not
    private $showUrlFields4Live = 0; //parameter to determine whether the live urls should show or not
    
    //Define the $integration_modes,specifies whether integration mode should be shown or not, 1 means to show, 0 means not
    private $integration_show_iframe = "0";
    private $integration_show_redirect = "0";
    private $integration_show_hostedpay = "0";
    /*
     * The index of the mode:
     * hostedpay:0
     * iframe:1
     * redirect: 2
     */
    //specifies the default index of integration mode
    private $default_integration_mode = "0";
    
    public function index(){
        $this->language->load('extension/payment/eservice');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        
        if(($this->request->server['REQUEST_METHOD']) == 'POST' && $this->validate()){
            $post_data = $this->request->post;
            if(empty($this->showUrlFields4Sandbox)){
                $post_data['payment_eservice_test_token_url'] = "https://apiuat.test.secure.eservice.com.pl/token";
                $post_data['payment_eservice_test_payments_url'] = "https://apiuat.test.secure.eservice.com.pl/payments";
                $post_data['payment_eservice_test_javascript_url'] = "https://cashierui-apiuat.test.secure.eservice.com.pl/js/api.js";
                $post_data['payment_eservice_test_cashier_url'] = "https://cashierui-apiuat.test.secure.eservice.com.pl/ui/cashier";
            }
            if(empty($this->showUrlFields4Live)){
                $post_data['payment_eservice_token_url'] = "https://api.secure.eservice.com.pl/token";
                $post_data['payment_eservice_payments_url'] = "https://api.secure.eservice.com.pl/payments";
                $post_data['payment_eservice_javascript_url'] = "https://cashierui-api.secure.eservice.com.pl/js/api.js";
                $post_data['payment_eservice_cashier_url'] = "https://cashierui-api.secure.eservice.com.pl/ui/cashier";
            }
            $this->model_setting_setting->editSetting('payment_eservice', $post_data);
            
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
//             $this->response->redirect($this->url->link('extension/payment/eservice', 'user_token=' . $this->session->data['user_token'] , true));
        }
        $data = array();
        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['text_payment'] = $this->language->get('text_payment');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        
        
        $data['text_textmode'] = $this->language->get('text_textmode');
        $data['text_textmodelogo'] = $this->language->get('text_textmodelogo');
        $data['text_graphicmode'] = $this->language->get('text_graphicmode');
        
        $data['entry_appearance'] = $this->language->get('entry_appearance');
        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['entry_clientid'] = $this->language->get('entry_clientid');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_testmode'] = $this->language->get('entry_testmode');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        
        $data['error_permission'] = $this->language->get('error_permission');
        $data['error_clientid'] = $this->language->get('error_clientid');
        $data['error_password'] = $this->language->get('error_password');
        $data['error_brandid'] = $this->language->get('error_brandid');
        
     
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        if (isset($this->error['clientid'])) {
            $data['error_clientid'] = $this->error['clientid'];
        } else {
            $data['error_clientid'] = '';
        }
        
        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }
        
        if (isset($this->error['brandid'])) {
            $data['error_brandid'] = $this->error['brandid'];
        } else {
            $data['error_brandid'] = '';
        }
      
        if (isset($this->error['sort_order'])) {
            $data['error_sort_order'] = $this->error['sort_order'];
        } else {
            $data['error_sort_order'] = '';
        }
       
        
        
    
        $data['breadcrumbs'] = array();
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );
        
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/eservice', 'user_token=' . $this->session->data['user_token'], true)
        );
        
        $data['action'] = $this->url->link('extension/payment/eservice', 'user_token=' . $this->session->data['user_token'], true);
        
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        
        
        if (isset($this->request->post['payment_eservice_clientid'])) {
            $data['payment_eservice_clientid'] = $this->request->post['payment_eservice_clientid'];
        } else {
            $data['payment_eservice_clientid'] = $this->config->get('payment_eservice_clientid');
        }
        if (isset($this->request->post['payment_eservice_password'])) {
            $data['payment_eservice_password'] = $this->request->post['payment_eservice_password'];
        } else {
            $data['payment_eservice_password'] = $this->config->get('payment_eservice_password');
        }
        if (isset($this->request->post['payment_eservice_brandid'])) {
            $data['payment_eservice_brandid'] = $this->request->post['payment_eservice_brandid'];
        } else {
            $data['payment_eservice_brandid'] = $this->config->get('payment_eservice_brandid');
        }
        if (isset($this->request->post['payment_eservice_status'])) {
            $data['payment_eservice_status'] = $this->request->post['payment_eservice_status'];
        } else {
            $data['payment_eservice_status'] = $this->config->get('payment_eservice_status');
        }
        
       
        
        if (isset($this->request->post['payment_eservice_testmode'])) {
            $data['payment_eservice_testmode'] = $this->request->post['payment_eservice_testmode'];
        } else {
            $data['payment_eservice_testmode'] = $this->config->get('payment_eservice_testmode');
        }
        if (isset($this->request->post['payment_eservice_test_token_url'])) {
            $data['payment_eservice_test_token_url'] = $this->request->post['payment_eservice_test_token_url'];
        } else {
            $data['payment_eservice_test_token_url'] = $this->config->get('payment_eservice_test_token_url');
        }
        if (isset($this->request->post['payment_eservice_test_payments_url'])) {
            $data['payment_eservice_test_payments_url'] = $this->request->post['payment_eservice_test_payments_url'];
        } else {
            $data['payment_eservice_test_payments_url'] = $this->config->get('payment_eservice_test_payments_url');
        }
        if (isset($this->request->post['payment_eservice_test_javascript_url'])) {
            $data['payment_eservice_test_javascript_url'] = $this->request->post['payment_eservice_test_javascript_url'];
        } else {
            $data['payment_eservice_test_javascript_url'] = $this->config->get('payment_eservice_test_javascript_url');
        }
        if (isset($this->request->post['payment_eservice_test_cashier_url'])) {
            $data['payment_eservice_test_cashier_url'] = $this->request->post['payment_eservice_test_cashier_url'];
        } else {
            $data['payment_eservice_test_cashier_url'] = $this->config->get('payment_eservice_test_cashier_url');
        }
        if(empty($this->showUrlFields4Sandbox)){
            $data['showUrlFields4Sandbox'] = 0;
        }else{
            $data['showUrlFields4Sandbox'] = 1;
        }
        
        if (isset($this->request->post['payment_eservice_token_url'])) {
            $data['payment_eservice_token_url'] = $this->request->post['payment_eservice_token_url'];
        } else {
            $data['payment_eservice_token_url'] = $this->config->get('payment_eservice_token_url');
        }
        if (isset($this->request->post['payment_eservice_payments_url'])) {
            $data['payment_eservice_payments_url'] = $this->request->post['payment_eservice_payments_url'];
        } else {
            $data['payment_eservice_payments_url'] = $this->config->get('payment_eservice_payments_url');
        }
        if (isset($this->request->post['payment_eservice_javascript_url'])) {
            $data['payment_eservice_javascript_url'] = $this->request->post['payment_eservice_javascript_url'];
        } else {
            $data['payment_eservice_javascript_url'] = $this->config->get('payment_eservice_javascript_url');
        }
        if (isset($this->request->post['payment_eservice_cashier_url'])) {
            $data['payment_eservice_cashier_url'] = $this->request->post['payment_eservice_cashier_url'];
        } else {
            $data['payment_eservice_cashier_url'] = $this->config->get('payment_eservice_cashier_url');
        }
        if(empty($this->showUrlFields4Live)){
            $data['showUrlFields4Live'] = 0;
        }else{
            $data['showUrlFields4Live'] = 1;
        }
        if(empty($this->integration_show_iframe) && empty($this->integration_show_redirect) && empty($this->integration_show_hostedpay)){
            $data['payment_eservice_pay_type'] = $this->default_integration_mode;
            $data['showPayTypeField'] = 0;
        }else{
            $data['showPayTypeField'] = 1;
            if (isset($this->request->post['payment_eservice_pay_type'])) {
                $data['payment_eservice_pay_type'] = $this->request->post['payment_eservice_pay_type'];
            } else {
                $data['payment_eservice_pay_type'] = $this->config->get('payment_eservice_pay_type');
            }
        }
        
        
        if (isset($this->request->post['payment_eservice_pay_action'])) {
            $data['payment_eservice_pay_action'] = $this->request->post['payment_eservice_pay_action'];
        } else {
            $data['payment_eservice_pay_action'] = $this->config->get('payment_eservice_pay_action');
        }
        
        //set up order status
        $this->load->model('localisation/order_status');
        
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        if (isset($this->request->post['payment_eservice_success_status_id'])) {
            $data['payment_eservice_success_status_id'] = $this->request->post['payment_eservice_success_status_id'];
        } else {
            $data['payment_eservice_success_status_id'] = $this->config->get('payment_eservice_success_status_id');
        }
        if (isset($this->request->post['payment_eservice_failed_status_id'])) {
            $data['payment_eservice_failed_status_id'] = $this->request->post['payment_eservice_failed_status_id'];
        } else {
            $data['payment_eservice_failed_status_id'] = $this->config->get('payment_eservice_failed_status_id');
        }
        if (isset($this->request->post['payment_eservice_refunded_status_id'])) {
            $data['payment_eservice_refunded_status_id'] = $this->request->post['payment_eservice_refunded_status_id'];
        } else {
            $data['payment_eservice_refunded_status_id'] = $this->config->get('payment_eservice_refunded_status_id');
        }
        if (isset($this->request->post['payment_eservice_auth_status_id'])) {
            $data['payment_eservice_auth_status_id'] = $this->request->post['payment_eservice_auth_status_id'];
        } else {
            $data['payment_eservice_auth_status_id'] = $this->config->get('payment_eservice_auth_status_id');
        }
        if (isset($this->request->post['payment_eservice_voided_status_id'])) {
            $data['payment_eservice_voided_status_id'] = $this->request->post['payment_eservice_voided_status_id'];
        } else {
            $data['payment_eservice_voided_status_id'] = $this->config->get('payment_eservice_voided_status_id');
        }
        if (isset($this->request->post['payment_eservice_canceled_status_id'])) {
            $data['payment_eservice_canceled_status_id'] = $this->request->post['payment_eservice_canceled_status_id'];
        } else {
            $data['payment_eservice_canceled_status_id'] = $this->config->get('payment_eservice_canceled_status_id');
        }
       
        if (isset($this->request->post['payment_eservice_sort_order'])) {
            $data['payment_eservice_sort_order'] = $this->request->post['payment_eservice_sort_order'];
        } else {
            $data['payment_eservice_sort_order'] = $this->config->get('payment_eservice_sort_order');
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/eservice', $data));
    }
    
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/eservice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if (!$this->request->post['payment_eservice_clientid']) {
            $this->error['clientid'] = $this->language->get('error_clientid');
        }
        
        if (!$this->request->post['payment_eservice_password']) {
            $this->error['password'] = $this->language->get('error_password');
        }
        
        if (!$this->request->post['payment_eservice_brandid']) {
            $this->error['brandid'] = $this->language->get('error_brandid');
        }
      
        
        return !$this->error;
    }
    public function install() {
        $this->load->model('extension/payment/eservice');
        $this->model_extension_payment_eservice->install();
    }
    
    public function uninstall() {
        $this->load->model('extension/payment/eservice');
        $this->model_extension_payment_eservice->uninstall();
    }
    // Legacy 2.0.0
    public function orderAction() {
        return $this->order();
    }
    
    // Legacy 2.0.3
    public function action() {
        return $this->order();
    }
    //show the order tab in the back office
    public function order() {
        if ($this->config->get('payment_eservice_status')) {
            $this->load->model('extension/payment/eservice');
            
            $order_info = $this->model_extension_payment_eservice->getOrder($this->request->get['order_id']);
            if (!empty($order_info)) {
                $this->load->language('extension/payment/eservice');
                
                $order_info['total_formatted'] = $this->currency->format($order_info['total'], $order_info['currency_code'], 1, true);
                
                $order_info['total_captured'] = $this->model_extension_payment_eservice->getTotalCaptured($order_info['eservice_order_id']);
                $order_info['total_captured_formatted'] = $this->currency->format($order_info['total_captured'], $order_info['currency_code'], 1, true);
                
                $order_info['total_refunded'] = $this->model_extension_payment_eservice->getTotalRefunded($order_info['eservice_order_id']);
                $order_info['total_refunded_formatted'] = $this->currency->format($order_info['total_refunded'], $order_info['currency_code'], 1, true);

                $order_info['unrefunded'] = number_format($order_info['total_captured'] - $order_info['total_refunded'],2,'.','');
                
                $data = array();
                $data['user_token'] = $this->request->get['user_token'];
                $data['order_info'] = $order_info;
                $data['order_id'] = $this->request->get['order_id'];
                
                return $this->load->view('extension/payment/eservice_order', $data);
            }
        }
    }
    public function capture() {
        $this->load->language('extension/payment/eservice');
        $json = array();
        
        if (isset($this->request->post['order_id']) && $this->request->post['order_id'] != '') {
            $this->load->model('extension/payment/eservice');
            
            $order = $this->model_extension_payment_eservice->getOrder($this->request->post['order_id']);
            if(empty($order)){
                $json['error'] = true;
                $json['msg'] = 'Order does not exsit';
            }else{
                //does not support partial capture yet, so capture only one time
                $capture_amount = $order['total'];
                
                try {
                    $capture_response = $this->model_extension_payment_eservice->capture($this->request->post['order_id'], $capture_amount);
                } catch (Exception $e) {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('text_error_connect_gateway');
                    $this->response->addHeader('Content-Type: application/json');
                    $this->response->setOutput(json_encode($json));
                    return;
                }
                
                if ($capture_response) {
                    $this->model_extension_payment_eservice->addTransaction($order['eservice_order_id'], 'payment', $capture_amount);
                    $json['msg'] = 'Capture successfully';
                    $this->model_extension_payment_eservice->updateCaptureStatus($order['eservice_order_id'], 1);
                    
                    $this->model_extension_payment_eservice->updateOrderHistory($this->request->post['order_id'],$this->config->get('payment_eservice_success_status_id'),'Captured');
                    
                    $json['data'] = array();
                    $json['data']['created'] = $this->model_extension_payment_eservice->getMysqlNowTime();
                    $json['data']['amount'] = $capture_amount;
                    $json['data']['total_captured_formatted'] = $this->currency->format($capture_amount, $order['currency_code'], 1, true);
                    $json['data']['capture_status'] = 1;
                    $json['data']['total'] = $capture_amount;
                    $json['error'] = false;
                } else {
                    $json['error'] = true;
                    $json['msg'] = 'Capture error';
                }
            }
        } else {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function void() {
        $this->load->language('extension/payment/eservice');
        $json = array();
        
        if (isset($this->request->post['order_id']) && $this->request->post['order_id'] != '') {
            $this->load->model('extension/payment/eservice');
            
            $order = $this->model_extension_payment_eservice->getOrder($this->request->post['order_id']);
            
            try {
                $void_response = $this->model_extension_payment_eservice->void($this->request->post['order_id']);
            } catch (Exception $e) {
                $json['error'] = true;
                $json['msg'] = $this->language->get('text_error_connect_gateway');
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }
            
            
            if ($void_response) {
                $this->model_extension_payment_eservice->addTransaction($order['eservice_order_id'], 'void', 0.00);
                $this->model_extension_payment_eservice->updateVoidStatus($order['eservice_order_id'], 1);
                
                $this->model_extension_payment_eservice->updateOrderHistory($this->request->post['order_id'],$this->config->get('payment_eservice_voided_status_id'),'Voided');
                
                $json['msg'] = $this->language->get('text_void_ok');
                $json['data'] = array();
                $json['data']['created'] = $this->model_extension_payment_eservice->getMysqlNowTime();
                $json['error'] = false;
            } else {
                $json['error'] = true;
                $json['msg'] = 'Void error';
            }
        } else {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function refund() {
        $this->load->language('extension/payment/eservice');
        $json = array();
        
        if (isset($this->request->post['order_id']) && $this->request->post['order_id'] != '' && isset($this->request->post['amount']) && $this->request->post['amount'] > 0) {
            $refund_amount = (double)$this->request->post['amount'];
            
            $this->load->model('extension/payment/eservice');
            
            $order = $this->model_extension_payment_eservice->getOrder($this->request->post['order_id']);
            try {
                $refund_response = $this->model_extension_payment_eservice->refund($this->request->post['order_id'],$refund_amount);
            } catch (Exception $e) {
                $json['error'] = true;
                $json['msg'] = $this->language->get('text_error_connect_gateway');
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }
            
            
            if ($refund_response) {
                if($refund_response == 2){
                    $json['error'] = true;
                    $json['msg'] = 'Transaction not refundable,please wait for the settlement';
                }else{
                    $this->model_extension_payment_eservice->addTransaction($order['eservice_order_id'], 'refund', $refund_amount);
                    
                    $total_captured = $this->model_extension_payment_eservice->getTotalCaptured($order['eservice_order_id']);
                    $total_refunded = $this->model_extension_payment_eservice->getTotalRefunded($order['eservice_order_id']);
                    $refund_status = 0;
                    if ($total_captured == $total_refunded) {
                        $refund_status = 1;
                        $this->model_extension_payment_eservice->updateRefundStatus($order['eservice_order_id'], $refund_status);
                        $this->model_extension_payment_eservice->updateOrderHistory($this->request->post['order_id'],$this->config->get('payment_eservice_refunded_status_id'),'Refunded');
                    }
                    $remaining = number_format($total_captured - $total_refunded, 2, '.', '');
                    
                    
                    $json['data'] = array();
                    $json['data']['amount'] = number_format($refund_amount, 2, '.', '');
                    $json['data']['total_refunded_formatted'] = $this->currency->format($total_refunded, $order['currency_code'], 1, true);
                    $json['data']['refund_status'] = $refund_status;
                    $json['data']['remaining'] = $remaining;
                    $json['msg'] = $this->language->get('text_refund_ok');
                    $json['data']['created'] = $this->model_extension_payment_eservice->getMysqlNowTime();
                    $json['error'] = false;
                }
            } else {
                $json['error'] = true;
                $json['msg'] = 'Refund error';
            }
        } else {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}