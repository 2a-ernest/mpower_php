<?php
/*Author Asare-Asiedu Ernest
Provide integration with Mpower mobile payment platform*/

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

// class ControllerResponsesExtensionMpower extends AController {

// 		public $data = array ();
// 		private $error = array ();
//  }
class ControllerResponsesExtensionMpower extends AController {

	public function main() {
		$this->loadLanguage('mpower/mpower');
		$template_data['button_confirm'] = $this->language->get('button_confirm');
		$template_data['button_back'] = $this->language->get('button_back');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		
		// $template_data['action'] = $this->html->getURL('checkout/shipping/mpower');
		$template_data['action'] = $this->html->getURL('extension/mpower/pay');

		

		$template_data['sid'] = $this->config->get('mpower_master_key');
		$template_data['total'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$template_data['cart_order_id'] = $this->session->data['order_id'];
		$template_data['order_number'] = $this->session->data['order_id'];
		$template_data['card_holder_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$template_data['street_address'] = $order_info['payment_address_1'];
		$template_data['city'] = $order_info['payment_city'];
		$template_data['state'] = $order_info['payment_zone'];
		$template_data['zip'] = $order_info['payment_postcode'];
		$template_data['country'] = $order_info['payment_country'];
		$template_data['email'] = $order_info['email'];
		$template_data['phone'] = $order_info['telephone'];
		if ($order_info['shipping_lastname']) {
			$template_data['ship_name'] = $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname'];
		} else {
			$template_data['ship_name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
		}

		if ($this->cart->hasShipping()) {
			$template_data['ship_street_address'] = $order_info['shipping_address_1'];
			$template_data['ship_city'] = $order_info['shipping_city'];
			$template_data['ship_state'] = $order_info['shipping_zone'];
			$template_data['ship_zip'] = $order_info['shipping_postcode'];
			$template_data['ship_country'] = $order_info['shipping_country'];
		} else {
			$template_data['ship_street_address'] = $order_info['payment_address_1'];
			$template_data['ship_city'] = $order_info['payment_city'];
			$template_data['ship_state'] = $order_info['payment_zone'];
			$template_data['ship_zip'] = $order_info['payment_postcode'];
			$template_data['ship_country'] = $order_info['payment_country'];
		}

		$template_data['products'] = array();

		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$template_data['products'][] = array(
				'product_id' => $product['product_id'],
				'name' => $product['name'],
				'description' => $product['name'],
				'quantity' => $product['quantity'],
				'price' => $this->currency->format($product['price'], $order_info['currency'], $order_info['value'], FALSE)
			);
		}

		if ($this->config->get('mpower_master_key')) {
			$template_data['demo'] = 'Y';
		}

		$template_data['lang'] = $this->session->data['language'];

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$template_data['back'] = $this->html->getSecureURL('checkout/payment');
		} else {
			$template_data['back'] = $this->html->getSecureURL('checkout/guest_step_2');
		}
		$this->view->batchAssign($template_data);
		$this->processTemplate('responses/mpower.tpl');
	}

	public function pay(){

		$this->mpower_init();

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		// require __DIR__.'/mpower_php/mpower.php';
			//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);


		
	    // echo $this->config->get('mpower_test_mode').' is val of Test mode';

	// Setup your checkout store information


	    $products = $this->cart->getProducts();
	    $total = $this->cart->gettotal();

	    //use totalPrice for charging
	    $totalPrice = 0;
	    foreach ($products as $product) {
	    	// print_r($product);
	    	$totalPrice += $product['total'];
	    }

	    printf('<br>Recalculated total is :GHC %s <br>',$totalPrice);

	    

	    $co = new MPower_Checkout_Invoice();

	    //add items to invoice
	    foreach ($products as $product) {
	    	// addItem(name_of_item,quantity,unit_price,total_price,optional_description)
	    	$co->addItem($product['name'],$product['quantity'],$product['price'],$product['total']);
	    }

	    //add customer info and shipping
	    $co->addCustomData("Firstname", $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']);
	    $co->addCustomData('cart_order_id',$this->session->data['order_id']);
	    $co->addCustomData('total',$this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE));

	    // $co->addCustomData('order_number',$this->session->data['order_id']);
		$co->addCustomData('card_holder_name',$order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']);
		$co->addCustomData('street_address',$order_info['payment_address_1']);
		$co->addCustomData('city',$order_info['payment_city']);
		$co->addCustomData('state',$order_info['payment_zone']);
		$co->addCustomData('zip',$order_info['payment_postcode']);
		$co->addCustomData('country',$order_info['payment_country']);
		$co->addCustomData('email',$order_info['email']);
		$co->addCustomData('phone',$order_info['telephone']);
		if ($order_info['shipping_lastname']) {
			$co->addCustomData('ship_name',$order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname']);
		} else {
			$co->addCustomData('ship_name', $order_info['firstname'] . ' ' . $order_info['lastname']);
		}

		if ($this->cart->hasShipping()) {
			$co->addCustomData('ship_street_address', $order_info['shipping_address_1']);
			$co->addCustomData('ship_city', $order_info['shipping_city']);
			$co->addCustomData('ship_state', $order_info['shipping_zone']);
			$co->addCustomData('ship_zip', $order_info['shipping_postcode']);
			$co->addCustomData('ship_country', $order_info['shipping_country']);
		} else {
			$co->addCustomData('ship_street_address', $order_info['payment_address_1']);
			$co->addCustomData('ship_city', $order_info['payment_city']);
			$co->addCustomData('ship_state', $order_info['payment_zone']);
			$co->addCustomData('ship_zip', $order_info['payment_postcode']);
			$co->addCustomData('ship_country', $order_info['payment_country']);
		}


	    ## Set the total amount to be charged ! Important
		    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

	        $co->setTotalAmount($this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE));

        ## Setup Tax Information (Optional)

            // $co->addTax("VAT (15)",50);
            // $co->addTax("NHIL (10)",50);

         ## Redirecting to your checkout invoice page

         if($co->create()) {
            header("Location: ".$co->getInvoiceUrl());
         }else{
           echo $co->response_text;
         }

		// fetch product into product
		
		// var_dump($products);
		// var_dump($total);
		// break;

	}

	public function callback() {
		
		$this->mpower_init();

		if (!isset($this->request->get['token'])) {
			//return to the homepage if no token is found attached to the url
			$this->redirect($this->html->getURL('index/home'));
		}

		$get  = $this->request->get;

		//create invoice object to access the status of order completion
		$invoice = new MPower_Checkout_Invoice();
		if($invoice->confirm($get['token'])){

			//on order completion
			$order_id = (int)$invoice->getCustomData('cart_order_id');
			$order_status = $invoice->getStatus();
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($order_id);
			$order_status_id = $this->order_status->getStatusByTextId($order_status);

			$this->load->model('checkout/order');

			if (!$order_info) {
					return null;
				}

			if( $order_status== 'completed'){
				
				$this->model_checkout_order->confirm($order_id, (int)$order_status_id);
				$this->model_checkout_order->updatePaymentMethodData((int)$invoice->getCustomData('cart_order_id'), serialize($invoice));
				// var_dump($invoice);
				$this->redirect($this->html->getURL('checkout/success'));

			}else if($order_status == 'canceled'){
					$this->model_checkout_order->confirm($order_id, (int)$order_status_id);
					$this->redirect($this->html->getURL('index/home'));
				}


			}else{
					$this->redirect($this->html->getURL('index/home'));
			}
			
	}


	//initalize mpoer configurations 
	private function mpower_init(){
		require( DIR_EXTENSIONS.'mpower/core/mpower_php/mpower.php');

		MPower_Checkout_Store::setName($this->config->get('mpower_store_name'));
	    MPower_Checkout_Store::setTagline($this->config->get('mpower_store_tag_line'));
	    MPower_Checkout_Store::setPhoneNumber($this->config->get('mpower_store_phone_number'));
	    MPower_Checkout_Store::setPostalAddress($this->config->get('mpower_store_postal_address'));
	    MPower_Setup::setMasterKey($this->config->get('mpower_master_key'));
	    MPower_Checkout_Store::setReturnUrl($this->html->getURL('extension/mpower/callback'));
	    MPower_Checkout_Store::setCancelUrl($this->html->getURL('extension/mpower/callback'));

		//mpower config

		// echo $this->config->get('mpower_master_key');
		// Setup your API Keys

		if(!$this->config->get('mpower_test_mode')){
			//use test config vals if test mode is false
			MPower_Setup::setPublicKey($this->config->get('mpower_public_key'));
			MPower_Setup::setPrivateKey($this->config->get('mpower_private_key'));
			MPower_Setup::setMode("live");
			MPower_Setup::setToken($this->config->get('mpower_live_token'));
		}else{
			//use live config vals
			MPower_Setup::setPublicKey($this->config->get('mpower_test_public_key'));
			MPower_Setup::setPrivateKey($this->config->get('mpower_test_private_key'));
			MPower_Setup::setMode("test");
			MPower_Setup::setToken($this->config->get('mpower_test_token'));
		}
	}

	
}