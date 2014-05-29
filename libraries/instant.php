<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Instant {
/**
* E-merchantpay CodeIgniter library
*
* This CodeIgniter library handles payments via emerchantpay.
*
* @package codeigniter-eMerchantPay
* @author Rakesh Sharma, <sharmarakesh395[at]gmail.com>
* @copyright Copyright (c) 2014, Rakesh Sharma
* @link https://github.com/rakeshksharma/codeigniter-eMerchantPay/
*/		
	function __construct(){
		//Do nothing
	
	}

	/**
	 * this is a generic function to capture instant payment from card
	 */
	
	public function pay($bankDetails = '', $order = '', $card = '', $user = '', $flag = ''){
	        
            # Bank/Merchant details
            $IPGCall = false ;
            $url = '';
            $data['api_key']  = $bankDetails['api_key'];
            $data['client_id'] = $bankDetails['client_id'];
            $data['form_id'] = "1061";
            # End
			
			# Order details
            $data['order_currency']         = $order['order_currency'];
			$data['amount']                 = $order['amount'];
			$data['order_reference']        = $order['order_reference'];
			$data['notify']                 = $order['notify'];
			$data['payment_type']           = $order['payment_type'];
			$data['test_transaction']       = $order['test_transaction'];
            
            if(isset($flag) && $flag == '1GBP'){
               				
				$data['item_1_code']            = $order['item_1_code'];
				$data['item_1_qty']             = $order['item_1_qty'];
				$data['item_1_predefined']      = $order['item_1_predefined'];
				$data['item_1_name']            = $order['item_1_name'];
				#END
				
				# Card details
				$data['card_holder_name']       = $card['card_holder_name'];
				$data['card_number']            = $card['card_number'];
				$data['exp_month']              = $card['exp_month'];
				$data['exp_year']               = $card['exp_year'];
				$data['ip_address']             = $this->get_user_ip_address(NULL);
				$data['credit_card_trans_type'] = $order['auth_type'];//'auth';
				# End
				
				$data['item_1_rebill']          = 2;
                $data['item_1_unit_price_GBP']  = $data['amount'];
                $data['item_1_digital']         = 1;
               
                # Card details
                $data['cvv']                    = $card['cvv'];
                # End
                //create_customer
                # Customer details
                $data['create_customer']        = 1 ;     
                $data['customer_first_name']    = $user['customer_first_name'];
                $data['customer_last_name']     = $user['customer_last_name'];
                $data['customer_address']       = $user['customer_address'];
                $data['customer_city']          = $user['customer_city'];
                $data['customer_state']         = $user['customer_state'];
                $data['customer_postcode']      = $user['customer_postcode'];
                $data['customer_country']       = $user['customer_country'];
                $data['customer_phone']         = $user['customer_phone'];
                $data['customer_email']         = $user['customer_email'];
				$data['thm_session_id']         = $user['thm_session_id'];
                # End
                $IPGCall = true ;
                $url = "https://my.emerchantpay.com/service/order/submit";
                
            }
            elseif(isset($flag) && $flag == 'REBIL' ){
                
				//$data['customer_first_name']    = $user['customer_first_name'];
                //$data['customer_last_name']     = $user['customer_last_name'];
                //$data['customer_email']         = $user['customer_email'];
                $data['item_id']                = $order['item_id'];
                $data['customer_id']            = $order['customer_id'];
                $data['order_id']               = $order['order_reference'];
				$data['description']            = $order['order_desc'];
                //$data['item_1_rebill']          = 2;
                //$data['item_1_rebill_period']   = 1;
                //$data['item_1_unit_price_GBP']  = $order['amount'];
                //$data['item_1_digital']         = 1;
                $url = "https://my.emerchantpay.com/service/order/rebill";
                $IPGCall = true ;
                
            }
            
            else{
                $IPGCall = false ;
                #redirect(base_url().'home');
            }
            
            if($IPGCall){
                #echo "<pre>";
             	//print_r($data);die;
			  $str = '';
                foreach($data as $key => $val){
                        $str.= $key.'='.$val.'&';
                    }
                    
                    rtrim($str,'&');
                    
                    $url = $url;
                    $result = $this->curl($url, $str);
                    
                   // header('content-type: text/xml');
                   // echo $result; die;
                    
                    
                  $xml = simplexml_load_string($result);
					//print_r($xml);

                    // 7. if result is success
                    if (isset($xml->order_status) && $xml->order_status == 'Pending') {
                        $res = array();
                        $res['order_id'] = (string) $xml->order_id;
                        $res['item_id'] = (string) $xml->cart->item->id;
                        $res['customer_id'] = (string) $xml->customer_id;
                        $res['order_total'] = (string) $xml->order_total;
                        $res['order_datetime'] = (string) $xml->order_datetime;
                        $res['order_status'] = (string) $xml->order_status;
                        $res['trans_response'] = (string) $xml->transaction->response;
                        $res['trans_response_text'] = (string) $xml->transaction->response_text;
                        $res['trans_id'] = (string) $xml->transaction->trans_id;
                        $res['response'] = (string) $result;
                        return $res;
                    } 
                    
                    elseif(isset($xml->transaction->response) && $xml->transaction->response == 'A' && !isset($xml->order_status)) {
                        $res = array();
                        $res['order_id'] = (string) $xml->order->order_id;
                        $res['item_id'] = (string) $xml->item->id;
                        //$res['customer_id'] = (string) $xml->customer_id;
                        //$res['order_total'] = (string) $xml->order_total;
                        //$res['order_datetime'] = (string) $xml->order_datetime;
                        //$res['order_status'] = (string) $xml->order_status;
                        $res['trans_response'] = (string) $xml->transaction->response;
                        $res['trans_response_text'] = (string) $xml->transaction->responsetext;
                        $res['trans_id'] = (string) $xml->transaction->trans_id;
                        $res['response'] = (string) $result;
                        return $res;
                    } 
            		else 
            		{
					    $res = array();
            		    if(sizeof($xml->errors->error) > 0) {
							foreach($xml->errors->error as $e) {
								$res['errorcode'] = (string)$e->code;
								$res['errortext'] = (string)$e->text;
								$res['response'] = (string) $result;
							}
						}
						elseif(isset($xml->transaction->response) && $xml->transaction->response == 'D') {
							$res = array();
							$res['trans_response'] = (string) $xml->transaction->response;
							$res['trans_id'] = (string) $xml->transaction->trans_id;
                            $res['response'] = (string) $result;
                        } 
						else {
						   $res[] = (string)$xml;
						}
						return $res;
                    }
                    
                    //$xml = simplexml_load_string($result);
                    
            }
            
    }
	
	/**
	 * this is a generic function to capture instant payment from card using sale
	 */
	
	public function paysale($bankDetails = '', $order = '', $card = '', $user = '', $flag = ''){
	        
            # Bank/Merchant details
            $IPGCall = false ;
            $url = '';
            $data['api_key']  = $bankDetails['api_key'];
            $data['client_id'] = $bankDetails['client_id'];
            $data['form_id'] = "1061";
            # End
			
			# Order details
            $data['order_currency']         = $order['order_currency'];
			$data['amount']                 = $order['amount'];
			$data['order_reference']        = $order['order_reference'];
			$data['notify']                 = $order['notify'];
			$data['payment_type']           = $order['payment_type'];
			$data['test_transaction']       = $order['test_transaction'];
            
            if(isset($flag) && $flag == '1GBP'){
               				
				$data['item_1_code']            = $order['item_1_code'];
				$data['item_1_qty']             = $order['item_1_qty'];
				$data['item_1_predefined']      = $order['item_1_predefined'];
				$data['item_1_name']            = $order['item_1_name'];
				#END
				
				# Card details
				$data['card_holder_name']       = $card['card_holder_name'];
				$data['card_number']            = $card['card_number'];
				$data['exp_month']              = $card['exp_month'];
				$data['exp_year']               = $card['exp_year'];
				$data['ip_address']             = $this->get_user_ip_address(NULL);
				$data['credit_card_trans_type'] = $order['auth_type'];
				# End
				
				//$data['item_1_rebill']          = 2;
                $data['item_1_unit_price_GBP']  = $data['amount'];
                $data['item_1_digital']         = 1;
               
                # Card details
                $data['cvv']                    = $card['cvv'];
                // $data['create_customer']        = 1 ;     
                $data['customer_first_name']    = $user['customer_first_name'];
                $data['customer_last_name']     = $user['customer_last_name'];
                $data['customer_address']       = $user['customer_address'];
                $data['customer_city']          = $user['customer_city'];
                $data['customer_state']         = $user['customer_state'];
                $data['customer_postcode']      = $user['customer_postcode'];
                $data['customer_country']       = $user['customer_country'];
                $data['customer_phone']         = $user['customer_phone'];
                $data['customer_email']         = $user['customer_email'];
				$IPGCall = true ;
                $url = "https://my.emerchantpay.com/service/order/submit";
                
            }
            else{
                $IPGCall = false ;
            }
            
            if($IPGCall){
			  $str = '';
                foreach($data as $key => $val){
                        $str.= $key.'='.$val.'&';
                    }
                    
                    rtrim($str,'&');
                    
                    $url = $url;
                    $result = $this->curl($url, $str);
                  $xml = simplexml_load_string($result);
                    if (isset($xml->order_status) && $xml->order_status == 'Paid') {
                        $res = array();
                        $res['order_id'] = (string) $xml->order_id;
                        $res['item_id'] = (string) $xml->cart->item->id;
                        $res['customer_id'] = (string) $xml->customer_id;
                        $res['order_total'] = (string) $xml->order_total;
                        $res['order_datetime'] = (string) $xml->order_datetime;
                        $res['order_status'] = (string) $xml->order_status;
                        $res['trans_response'] = (string) $xml->transaction->response;
                        $res['trans_response_text'] = (string) $xml->transaction->response_text;
                        $res['trans_id'] = (string) $xml->transaction->trans_id;
                        $res['response'] = (string) $result;
                        return $res;
                    } 
                    
                    elseif(isset($xml->transaction->response) && $xml->transaction->response == 'A' && !isset($xml->order_status)) {
                        $res = array();
                        $res['order_id'] = (string) $xml->order->order_id;
                        $res['item_id'] = (string) $xml->item->id;
                        $res['trans_response'] = (string) $xml->transaction->response;
                        $res['trans_response_text'] = (string) $xml->transaction->responsetext;
                        $res['trans_id'] = (string) $xml->transaction->trans_id;
                        $res['response'] = (string) $result;
                        return $res;
                    } 
            		else 
            		{
					    $res = array();
            		    if(sizeof($xml->errors->error) > 0) {
							foreach($xml->errors->error as $e) {
								$res['errorcode'] = (string)$e->code;
								$res['errortext'] = (string)$e->text;
								$res['response'] = (string) $result;
							}
						}
						elseif(isset($xml->transaction->response) && $xml->transaction->response == 'D') {
							$res = array();
							$res['trans_response'] = (string) $xml->transaction->response;
							$res['trans_id'] = (string) $xml->transaction->trans_id;
                            $res['response'] = (string) $result;
                        } 
						else {
						   $res[] = (string)$xml;
						}
						return $res;
                    }
             }
            
    }

    
    
   public function curl($url,$data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $result = curl_exec($ch);
        if (!$result) {
            echo "Got " . curl_error($ch) . " when processing request";
            curl_close($ch);
            exit;
        }
        curl_close($ch);
        return $result;
    }
    
    public function get_user_ip_address($force_string=NULL)
        {
        	$ip_addresses = array();
        	$ip_elements = array(
        		'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED_FOR',
        		'HTTP_X_FORWARDED', 'HTTP_FORWARDED',
        		'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_CLUSTER_CLIENT_IP',
        		'HTTP_X_CLIENT_IP', 'HTTP_CLIENT_IP',
        		'REMOTE_ADDR'
        	);
        
        	foreach ( $ip_elements as $element ) {
        		if(isset($_SERVER[$element])) {
        			if ( !is_string($_SERVER[$element]) ) {
        				// Log the value somehow, to improve the script!
        				continue;
        			}
        
        			$address_list = explode(',', $_SERVER[$element]);
        			$address_list = array_map('trim', $address_list);
        
        			// Not using array_merge in order to preserve order
        			foreach ( $address_list as $x ) {
        				$ip_addresses[] = $x;
        			}
        		}
        	}
        
        	if ( count($ip_addresses)==0 ) {
        		return FALSE;
        
        	} elseif ( $force_string===TRUE || ( $force_string===NULL && count($ip_addresses)==1 ) ) {
        		return $ip_addresses[0];
        
        	} else {
        		return $ip_addresses;
        	}
        }
        
        
    public function get_public_ip_address()
            {
            	// TODO: Add a fallback to http://httpbin.org/ip
            	// TODO: Add a fallback to http://169.254.169.254/latest/meta-data/public-ipv4
            
            	$url="simplesniff.com/ip";
            
            	$ch = curl_init();
            	curl_setopt($ch, CURLOPT_URL, $url);
            	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            	$data = curl_exec($ch);
            	curl_close($ch);
            
            	return $data;
            }

}

/* End of file instant.php */