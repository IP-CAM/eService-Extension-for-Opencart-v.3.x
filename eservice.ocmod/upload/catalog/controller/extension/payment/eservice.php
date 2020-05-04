<?php
require_once modification(DIR_SYSTEM . 'library/eservice/payments.php');
class ControllerExtensionPaymentEservice extends Controller {
	public function index() {
		if ($order_info = $this->model_checkout_order->getOrder($this->session->data['order_id'])) {
			$this->load->model('checkout/order');
			$this->load->model('localisation/country');
			$this->language->load('extension/payment/eservice');
			
			$this->logger(json_encode($order_info));
			
			
			
			$post_data = array();
			$post_data['merchantId'] = trim($this->config->get('payment_eservice_clientid'));
			$post_data['merchantTxId'] = substr(md5(uniqid(mt_rand(), true)), 0, 20);
			$post_data['password'] = trim($this->config->get('payment_eservice_password'));
			$post_data['brandId'] = trim($this->config->get('payment_eservice_brandid'));
			
			$post_data['customerId'] = $order_info['customer_id'];
			$post_data['allowOriginUrl'] = $this->getAllowOriginUrl();
			$post_data['merchantLandingPageUrl'] = html_entity_decode($this->url->link('extension/payment/eservice/redirectBack', 'order_id='.$this->session->data['order_id'],true), ENT_QUOTES, 'UTF-8');
			$post_data['merchantNotificationUrl'] = html_entity_decode($this->url->link('extension/payment/eservice/callback', 'order_id='.$this->session->data['order_id'],true), ENT_QUOTES, 'UTF-8');
			$post_data['timestamp'] = round(microtime(true) * 1000);
			$post_data['channel'] = 'ECOM';
			$post_data['language'] = substr($order_info['language_code'], 0,2);
			$post_data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			$post_data['paymentSolutionId'] = '';
			$post_data['currency'] = $this->config->get('config_currency'); //get the currency from the back office's setting
// 			$post_data['currency'] = $order_info['currency_code'];
// 			$post_data['country'] = $order_info['payment_iso_code_2'];
			$country_info = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));
			$post_data['country'] = $country_info['iso_code_2'];//get the country from the back office's setting
			$post_data['customerFirstName'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$post_data['customerLastName'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$post_data['customerBillingAddressPostalCode'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$post_data['customerBillingAddressCity'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
			$post_data['customerBillingAddressCountry'] = html_entity_decode($order_info['payment_iso_code_2'], ENT_QUOTES, 'UTF-8');
			$post_data['customerBillingAddressStreet'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
			
			
			
			$data = array();
			try {
			    $this->initConfig();
			    $payments = new Payments\Payments();
			    $payment_eservice_pay_action = $this->config->get('payment_eservice_pay_action'); //1 refer to auth, 0 refer to purchase
			    if ($payment_eservice_pay_action){
			        $post_data['action'] = "AUTH";
			        $payments_request = $payments->auth();
			    }else {
			        $post_data['action'] = "PURCHASE";
			        $payments_request = $payments->purchase();
			    }
			    
			    $this->logger(json_encode($post_data));
			    
			    $payments_request->merchantTxId($post_data['merchantTxId'])->
			    brandId($post_data['brandId'])->
			    action($post_data['action'])->
			    customerId($post_data['customerId'])->
			    allowOriginUrl($post_data['allowOriginUrl'])->
			    merchantLandingPageUrl($post_data['merchantLandingPageUrl'])->
			    merchantNotificationUrl($post_data['merchantNotificationUrl'])->
			    timestamp($post_data['timestamp'])->
			    channel($post_data['channel'])->
			    language($post_data['language'])->
			    amount($post_data['amount'])->
			    paymentSolutionId($post_data['paymentSolutionId'])->
			    currency($post_data['currency'])->
			    country($post_data['country'])->
			    customerFirstName($post_data['customerFirstName'])->
			    customerLastName($post_data['customerLastName'])->
			    customerBillingAddressPostalCode($post_data['customerBillingAddressPostalCode'])->
			    customerBillingAddressCity($post_data['customerBillingAddressCity'])->
			    customerBillingAddressCountry($post_data['customerBillingAddressCountry'])->
			    customerBillingAddressStreet($post_data['customerBillingAddressStreet']);
			    $res = $payments_request->token();
			} catch (Exception $e) {
			    $data['error'] = $this->language->get('text_error_connect_gateway');
			    return $this->load->view('extension/payment/eservice', $data);
			}
			
			
			
			if(isset($res->result) && $res->result == 'success'){
			    
			    $data['token'] = $res->token;
			    $data['merchantId'] = trim($this->config->get('payment_eservice_clientid'));
			    
			    $data['button_confirm'] = $this->language->get('button_confirm');
			    
			    $testmode = $this->config->get('payment_eservice_testmode');
			    if ($testmode) $data['action'] = $this->config->get('payment_eservice_test_cashier_url');
			    else $data['action'] = $this->config->get('payment_eservice_cashier_url');
			    
			    $data['java_script_url'] = $this->config->get('payment_eservice_test_javascript_url');
			    $data['cashier_url'] = $this->config->get('payment_eservice_test_cashier_url');
			}else{
			    $data['error'] = $this->language->get('text_error_message');
			}
			if($this->config->get('payment_eservice_pay_type') == 1){
			    //iframe payment mode
			    $data['post_url'] = $post_data['merchantLandingPageUrl'];
			    return $this->load->view('extension/payment/eservice_iframe', $data);
			}else if($this->config->get('payment_eservice_pay_type') == 2){
			    //redirect payment mode
			    $data['integrationMode'] = 'standalone';
			    return $this->load->view('extension/payment/eservice', $data);
			}else{
			    //hostedpay payment mode
			    $data['integrationMode'] = 'hostedPayPage';
			    return $this->load->view('extension/payment/eservice', $data);
			}
		}
	}
	// init the SDK configuration settings
	private function initConfig(){
	    Payments\Config::$MerchantId = trim($this->config->get('payment_eservice_clientid'));
	    Payments\Config::$Password = trim($this->config->get('payment_eservice_password'));
	    $testmode = $this->config->get('payment_eservice_testmode');
	    if ($testmode){
	        Payments\Config::$TestUrls["SessionTokenRequestUrl"] = $this->config->get('payment_eservice_test_token_url');
	        Payments\Config::$TestUrls["PaymentOperationActionUrl"] = $this->config->get('payment_eservice_test_payments_url');
	        Payments\Config::$TestUrls["JavaScriptUrl"] = $this->config->get('payment_eservice_test_javascript_url');
	        Payments\Config::$TestUrls["BaseUrl"] = $this->config->get('payment_eservice_test_cashier_url');
	        Payments\Config::test();
	    }else{
	        Payments\Config::$ProductionUrls["SessionTokenRequestUrl"] = $this->config->get('payment_eservice_token_url');
	        Payments\Config::$ProductionUrls["PaymentOperationActionUrl"] = $this->config->get('payment_eservice_payments_url');
	        Payments\Config::$ProductionUrls["JavaScriptUrl"] = $this->config->get('payment_eservice_javascript_url');
	        Payments\Config::$ProductionUrls["BaseUrl"] = $this->config->get('payment_eservice_cashier_url');
	        Payments\Config::production();
	    }
	    
	}
	//call the Gateway SDK to check for the payment status
	private function checkStatus($order_id,$merchantTxId){
	    $this->load->model('checkout/order');
	    
	    try {
	        $this->initConfig();
	        $payments = new Payments\Payments();
	        $status_check = $payments->status_check();
	        $status_check->merchantTxId($merchantTxId)->
	        action('GET_STATUS')->
	        allowOriginUrl($this->getAllowOriginUrl());
	        $result = $status_check->execute();
	        
	        $this->load->model('checkout/order');
	        $this->load->model('extension/payment/eservice');
	        
	        $order_info = $this->model_checkout_order->getOrder($order_id);
	        $order_data = array(
	            'order_id' => $order_id,
	            'merchant_tx_id' => $merchantTxId,
	            "total" =>number_format($order_info['total'], 2),
	            'currency_code' => $order_info['currency_code']
	        );
	        $this->logger('checkStatus result :'.$result->result);
	        if(!isset($result->result) || $result->result != 'success'){
	            //the order payment was declined or canceled.
	            
	            if($this->model_extension_payment_eservice->getTotalOrderHistories($order_id)){
	                //the order status has been checked already
	                $this->response->redirect($this->url->link('checkout/failure'));
	            }else{
	                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_eservice_failed_status_id'),'Failed');
	                $this->response->redirect($this->url->link('checkout/failure'));
	            }
	            exit;
	        }else{
	            if($this->model_extension_payment_eservice->hasOrder($order_id)){
	                //the order status has been checked already
	                $this->response->redirect($this->url->link('checkout/success'));
	                exit;
	            }
	            
	            if($result->status == 'SET_FOR_CAPTURE' || $result->status == 'CAPTURED'){
	                //PURCHASE was successful
	                $eservice_order_id = $this->model_extension_payment_eservice->addOrder($order_data);
	                $this->model_extension_payment_eservice->addTransaction($eservice_order_id,'payment', $order_info);
	                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_eservice_success_status_id'),'Paid');
	                $this->response->redirect($this->url->link('checkout/success'));
	            }else if($result->status == 'NOT_SET_FOR_CAPTURE'){
	                // AUTH was successful
	                $eservice_order_id = $this->model_extension_payment_eservice->addOrder($order_data);
	                $this->model_extension_payment_eservice->addTransaction($eservice_order_id,'auth', $order_info);
	                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_eservice_auth_status_id'),'Authorized');
	                $this->response->redirect($this->url->link('checkout/success'));
	            }else{
	                if($result->status == "STARTED" || $result->status == "WAITING_RESPONSE" || $result->status == "INCOMPLETE"){
	                    //Do not handle these order status in the plugin system
	                    $this->response->redirect($this->url->link('checkout/failure'));
	                    exit;
	                }
	                //the order payment was declined or canceled.
	                if($this->model_extension_payment_eservice->getTotalOrderHistories($order_id)){
	                    //the order status has been checked already
	                    $this->response->redirect($this->url->link('checkout/failure'));
	                }else{
	                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_eservice_failed_status_id'),'Failed');
	                    $this->response->redirect($this->url->link('checkout/failure'));
	                }
	                exit;
	            }
	        }
	    } catch (Exception $e) {
	        $this->response->redirect($this->url->link('checkout/failure'));
	    }
	}
	//redirect the user to this url when the transaction is done
	public function redirectBack(){
	    $this->load->model('checkout/order');
	    $post = $this->request->post;
	    $this->logger('redirectBack:'.json_encode($post));
	    if(!isset($post['merchantTxId']) || !isset($this->request->get['order_id'])){
	        $this->response->redirect($this->url->link('checkout/checkout', '', true));
	        exit;
	    }else{
	        $order_id = $this->request->get['order_id'];
	        $merchantTxId = $post['merchantTxId'];
	    }
	    $this->checkStatus($order_id,$merchantTxId);
	    
	}
    //Gateway callback to notify the server
	public function callback(){
	    $this->load->model('checkout/order');
	    $post = $this->request->post;
	    $this->logger('CALLBACK:'.json_encode($post));
	    if(!isset($post['merchantTxId']) || !isset($this->request->get['order_id'])){
	        die('Illegal Access');
	    }else{
	        $order_id = $this->request->get['order_id'];
	        $merchantTxId = $post['merchantTxId'];
	    }
	    //the server will also call back the notification when  refund are made, this is to ignore the other action, only purchase
	    if($post['action'] != 'PURCHASE' && $post['action'] != 'AUTH' && $post['action'] != 'CAPTURE'){
	        $this->response->addHeader('HTTP/1.1 200 OK');
	        $this->response->addHeader('Content-Type: application/json');
	    }else{
	        $this->checkStatus($order_id,$merchantTxId);
	        $this->response->addHeader('HTTP/1.1 200 OK');
	        $this->response->addHeader('Content-Type: application/json');
	    }
	}
	
	private function getAllowOriginUrl(){
	    $parse_result = parse_url(HTTPS_SERVER);
	    if(isset($parse_result['port'])){
	        $allowOriginUrl = $parse_result['scheme']."://".$parse_result['host'].":".$parse_result['port'];
	    }else{
	        $allowOriginUrl = $parse_result['scheme']."://".$parse_result['host'];
	    }
	    return $allowOriginUrl;
	}
	
	public function logger($message) {
	    $debug = false;
	    
	    if ($debug) {
			$log = new Log('eservice.log');
			$log->write($message);
		}
	}
	
}
