<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment extends CI_Controller {

    function __construct() {
        parent::__construct();
    }


 /**
  * Starting payment process for intial amout euro 1.
  */ 
	function ipg_payment() {
	 		// Loading library for payment process.
			$this->load->library('instant');
			// Define blank arrays.
			$user = array();
			$card = array();
			$bank_details = array();
			$order = array();
			// Passing api values.
			$bank_details['api_key'] = 'yourpaikey';
			$bank_details['client_id'] = 'yourclientid';
			
			// Add card info based on post fields.
            $card['card_holder_name'] = $this->input->post('cardholder');
            $card['card_number'] = $this->input->post('cardnumber');
            $card['cvv'] = $this->input->post('cardcv2');
            $card['exp_month'] = $this->input->post('cardendm');
            $card['exp_year'] = substr($this->input->post('cardendy'), 2, 2);
			
			// Sending order deatils.
			$order['order_currency']          = 'GBP';
            $order['amount']                  = '1.00';
            $order['order_reference']         = rand(2,20);
            $order['notify']                  = 0;
            $order['payment_type']            = 'creditcard';
            $order['test_transaction']        = '1'; // set 0 to live
			$order['auth_type'] = 'sale'  // change to sale, auth etc.
            
            $order['item_1_code']             = 'product_code';
            $order['item_1_qty']              = 1;
            $order['item_1_predefined']       = 0;
            $order['item_1_name']             = 'product_name';
			
			$user['customer_first_name']      = 'Rakesh';
            $user['customer_last_name']       = 'Sharma';
            $user['customer_address']         =  "123 Franklin Street";
            $user['customer_city']            = "Philadelphia";
            $user['customer_state']           = "PA";
            $user['customer_postcode']        = "91304";
            $user['customer_country']         = "GB";
            $user['customer_phone']           = '123456';
            $user['customer_email']           = 'sharmarakesh395@gmail.com';
			 // Getting payment response.			
			$payment_reponse = $this->instant->paysale($bank_details, $order, $card, $user, '1GBP');
			
			//For rebill process use
			//$payment_reponse = $this->instant->paysale($bank_details, $order, $card, $user, '1GBP');
			//$payment_reponse = $this->instant->paysale($bank_details, $order, $card, $user, 'REBILL');
			
			return $payment_reponse;
			// do stuff with payment reponse and check 
			/* if($payment_reponse['order_status'])
	}
}