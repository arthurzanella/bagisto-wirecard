<?php

namespace ArthurZanella\Wirecard\Payment;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use ArthurZanella\Wirecard\Helper\Helper;
use Exception;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Models\Cart;
use Webkul\Checkout\Models\CartAddress;
use Moip\Moip;
use Moip\Auth\BasicAuth;
use Webkul\Payment\Payment\Payment;

/**
 * Class Wirecard
 * @package ArthurZanella\Wirecard\Payment
 */
class Wirecard extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'wirecard';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Wirecard constructor.
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->token = $this->getConfigData('token');
        $this->key = $this->getConfigData('key');
        $this->sandbox = $this->getConfigData('sandbox');
        $this->store_name = $this->getConfigData('store_name');
        $this->webhook_url = $this->getConfigData('webhook_url');
        $this->helper = $helper;
        $this->currentUser = auth()->guard('customer')->user();
    }

    /**
     * @param $dob
     * @return |null
     */
    public function validateCustomerDob($dob)
    {
        return strtotime(Carbon::parse($dob)) < 0 ? null : $dob;
    }

    /**
     * @throws Exception
     */
    public function paymentRequest($request)
    {
        if (!$this->token) {
            throw new Exception('Wirecard: Para usar essa opção de pagamento você precisa informar o Token pagamento!');
        }

        if (!$this->key) {
            throw new Exception('Wirecard: Para usar essa opção de pagamento você precisa informar a Key de pagamento!');
        }

        if (!$this->validateCustomerDob($this->currentUser->date_of_birth)) {
            throw new Exception('Wirecard: Informe sua data de nascimento para continuar.');
            return route('customer.profile.edit');
        }

        if (!$this->getCart()) {
            throw new Exception('Wirecard: Você deve adicionar produtos no carrinho..');
            return route('shop.checkout.cart.index');
        }

        /** @var Cart $cart */
        $cart = $this->getCart();
        // attributes: array:31 [▼
        //     "id" => 7
        //     "customer_email" => "arthurzanella@gmail.com"
        //     "customer_first_name" => "Arthur"
        //     "customer_last_name" => "Zanella"
        //     "shipping_method" => "free_free"
        //     "coupon_code" => null
        //     "is_gift" => 0
        //     "items_count" => 1
        //     "items_qty" => "1.0000"
        //     "exchange_rate" => null
        //     "global_currency_code" => "BRL"
        //     "base_currency_code" => "BRL"
        //     "channel_currency_code" => "BRL"
        //     "cart_currency_code" => "BRL"
        //     "grand_total" => "30.0000"
        //     "base_grand_total" => "30.0000"
        //     "sub_total" => "30.0000"
        //     "base_sub_total" => "30.0000"
        //     "tax_total" => "0.0000"
        //     "base_tax_total" => "0.0000"
        //     "discount_amount" => "0.0000"
        //     "base_discount_amount" => "0.0000"
        //     "checkout_method" => null
        //     "is_guest" => 0
        //     "is_active" => 1
        //     "conversion_time" => null
        //     "customer_id" => 2
        //     "channel_id" => 1
        //     "created_at" => "2020-04-24 16:45:26"
        //     "updated_at" => "2020-04-24 16:45:53"
        //     "applied_cart_rule_ids" => ""
        // ]

        $billingAddress = $cart->getBillingAddressAttribute();
        $shippingAddress = $cart->getShippingAddressAttribute();
        // attributes: array:18 [▼
        //     "id" => 7
        //     "first_name" => "Arthur"
        //     "last_name" => "Zanella"
        //     "email" => "arthurzanella@gmail.com"
        //     "company_name" => "Casa"
        //     "vat_id" => null
        //     "address1" => "rua endereco"
        //     "address2" => null
        //     "country" => "BR"
        //     "state" => "RS"
        //     "city" => "Caxias do Sull"
        //     "postcode" => "95000000"
        //     "phone" => "54999999999"
        //     "address_type" => "billing"
        //     "cart_id" => 7
        //     "customer_id" => 2
        //     "created_at" => "2020-04-24 16:45:44"
        //     "updated_at" => "2020-04-24 16:52:04"
        // ]

        //-------------------------------------
        // Wirecard inicia aqui
        //-------------------------------------

        $token = $this->token;
        $key = $this->key;
        $store_name = $this->store_name;

        if($this->sandbox == true){
            $endpoint = Moip::ENDPOINT_SANDBOX;
        } else {
            $endpoint = Moip::ENDPOINT_PRODUCTION;
        }

        $moip = new Moip(new BasicAuth($token, $key), $endpoint);

        // adress1 = rua
        // adress2 = numero
        // adress3 = bairro
        // adress4 = complemento

        // $holder_name = $this->currentUser->customer_first_name.' '.$this->currentUser->customer_last_name;
        // $holder_name = $cart->customer_first_name.' '.$cart->customer_last_name
        $document = $this->helper->documentParser($this->currentUser->document);
        $ddd = $this->helper->getDDD($billingAddress->phone);
        $phone = $this->helper->getPhoneNumber($billingAddress->phone);
        $birth = $this->currentUser->date_of_birth;

        try {
            $customer = $moip->customers()->setOwnId(uniqid())
                ->setFullname($cart->customer_first_name.' '.$cart->customer_last_name)
                ->setEmail($cart->customer_email)
                ->setBirthDate($this->currentUser->date_of_birth)
                ->setTaxDocument($document)
                ->setPhone($ddd, $phone)
                ->addAddress(
                    'BILLING',
                    $billingAddress->address1,
                    $billingAddress->address2,
                    $billingAddress->address3 ?: '',
                    $billingAddress->city,
                    $billingAddress->state,
                    $billingAddress->postcode,
                    $billingAddress->address4 ?: ''
                )
                ->addAddress(
                    'SHIPPING',
                    $shippingAddress->address1,
                    $shippingAddress->address2,
                    $shippingAddress->address3 ?: '',
                    $shippingAddress->city,
                    $shippingAddress->state,
                    $shippingAddress->postcode,
                    $shippingAddress->address4 ?: ''
                )
                ->create();
        } catch (Exception $e) {
            dd($e->__toString());
        }

        try {
            $order = $moip->orders()->setOwnId(uniqid());              
                // ->addItem("bicicleta", 1, "sku1", 10000)
                foreach ($cart->items as $cartItem) {
                    $price = preg_replace('/[^0-9]/', '', number_format($cartItem->price, 2, ',', '.'));
                    $order->addItem(strval($cartItem->name), (int)$cartItem->quantity, strval($cartItem->sku), (int)$price);
                }
            $order->setShippingAmount(preg_replace('/[^0-9]/', '', number_format(0, 2, ',', '.'))); //achar variavel do preco do frete e atualizar aqui $cart->selected_shipping_rate->price;
            $order->setCustomer($customer);
            $order->create();
        } catch (\Moip\Exceptions\UnautorizedException $e) {
            //StatusCode 401
            echo $e->getMessage();
        } catch (\Moip\Exceptions\ValidationException $e) {
            //StatusCode entre 400 e 499 (exceto 401)
            printf($e->__toString());
        } catch (\Moip\Exceptions\UnexpectedException $e) {
            //StatusCode >= 500
            echo $e->getMessage();
        }

        try {
            $holder = $moip->holders()->setFullname($request->holder)
                ->setBirthDate($request->holder_birt_date)
                ->setTaxDocument($request->holder_cpf, 'CPF')
                ->setPhone($request->holder_phone_ddd, $request->holder_phone_number, 55);
                // ->setAddress(
                //     'BILLING',
                //     $shippingAddress->address1 ?: '',
                //     $shippingAddress->address2 ?: '',
                //     $shippingAddress->address3 ?: '',
                //     $shippingAddress->city ?: '',
                //     $shippingAddress->state ?: '',
                //     $shippingAddress->postcode ?: '',
                //     $shippingAddress->address4 ?: ''
                // );
            
                //dd($holder);
            
            //$hash = "KT7VjDwy2CUInoAtA6i/4xO/hjsCT+gD0AeU9By0+VvFSRvkf5A7h+gvoqNrG/WdXDhUNqgpAsnkQQ1n0BkDoUzUHSfMiAnZKR7tWm/oO8QGX69o+kcnztfbkUHcrNSzHTDHX/2OVYT0GPQpsZX0wUbqwcdTa9FzaMuce44f79g66kpv/ax6upWbCx4dCJgeYkOQkpYgchhYTJgvgvRgqZHjZafDw6fWle5UrF/5uybuzISp6hR7s1qIsU9JPZdaA7fEqHz/8Gr5/G7Ot5W8S2BO3DsIz4fZ0E8bgc+E6GH2ywc8JO3PlcH4hE6hMZmUqRDK3/Nq9b1+IczeUym+Dg==";
            
            $payment = $order->payments()
                ->setCreditCardHash($request->hash, $holder)
                ->setInstallmentCount(1)
                ->setStatementDescriptor($store_name)
                //->setDelayCapture()
                ->execute();
        } catch (\Moip\Exceptions\UnautorizedException $e) {
            //StatusCode 401
            echo $e->getMessage();
        } catch (\Moip\Exceptions\ValidationException $e) {
            //StatusCode entre 400 e 499 (exceto 401)
            printf($e->__toString());
        } catch (\Moip\Exceptions\UnexpectedException $e) {
            //StatusCode >= 500
            echo $e->getMessage();
        }

        //aguardo ok de pagamento antes de consultar
        //sleep(1);

        //se tiver erro no pagamento ou pagamento for recusado
        if(!isset($payment)){
            throw new Exception('Wirecard: Erro ao fazer o pagamento! Tente conferir os dados do seu cartão e endereço e realizar novamente o pagamento!');
            return route('shop.checkout.cart.index');
        } else if($payment->getStatus() == 'REFUNDED'){
            throw new Exception('Wirecard: Erro ao fazer o pagamento! Tente conferir os dados do seu cartão e endereço e realizar novamente o pagamento!');
            return route('shop.checkout.cart.index');
        }

        //consulta status do pagamento
        //$payment_status = $moip->payments()->get($payment->getId());

        //direciona para sucesso
        //return $payment->getStatus();
        return $order->getOwnId();
        //ownId
        //return route('wirecard.success');

    }

    /**
     * Get ownId from webhooks notifications
     *
     * @return array
     */
    public function getOwnIdNotify($request)
    {
        return $request->getOwnId();
    }

    /**
     * Create webhooks notifications
     *
     * @return array
     */
    public function createWebhook()
    {
        if (!$this->token) {
            throw new Exception('Wirecard: Informar o Token pagamento!');
        }

        if (!$this->key) {
            throw new Exception('Wirecard: Informar a Key de pagamento!');
        }

        if (!$this->webhook_url) {
            throw new Exception('Wirecard: Informar a url webhook!');
        }

        $token = $this->token;
        $key = $this->key;
        $store_name = $this->store_name;
        $webhook_url = $this->webhook_url;

        if($this->sandbox == true){
            $endpoint = Moip::ENDPOINT_SANDBOX;
        } else {
            $endpoint = Moip::ENDPOINT_PRODUCTION;
        }

        $moip = new Moip(new BasicAuth($token, $key), $endpoint);

        $notification = $moip->notifications()
            ->addEvent("ORDER.*")
            //->addEvent("PAYMENT.*")
            ->setTarget($webhook_url)
            ->create();

        dd($notification);
    }

    /**
     * List webhooks notifications
     *
     * @return array
     */
    public function listWebhook()
    {
        if (!$this->token) {
            throw new Exception('Wirecard: Informar o Token pagamento!');
        }

        if (!$this->key) {
            throw new Exception('Wirecard: Informar a Key de pagamento!');
        }

        $token = $this->token;
        $key = $this->key;
        $store_name = $this->store_name;
        $webhook_url = $this->webhook_url;

        if($this->sandbox == true){
            $endpoint = Moip::ENDPOINT_SANDBOX;
        } else {
            $endpoint = Moip::ENDPOINT_PRODUCTION;
        }

        $moip = new Moip(new BasicAuth($token, $key), $endpoint);

        $notifications = $moip->notifications()->getList();

        dd($notifications);
    }

    /**
     * Delete webhooks notifications
     *
     * @return array
     */
    public function deleteWebhook($notification_id)
    {
        if (!$this->token) {
            throw new Exception('Wirecard: Informar o Token pagamento!');
        }

        if (!$this->key) {
            throw new Exception('Wirecard: Informar a Key de pagamento!');
        }

        $token = $this->token;
        $key = $this->key;
        $store_name = $this->store_name;
        $webhook_url = $this->webhook_url;

        if($this->sandbox == true){
            $endpoint = Moip::ENDPOINT_SANDBOX;
        } else {
            $endpoint = Moip::ENDPOINT_PRODUCTION;
        }

        $moip = new Moip(new BasicAuth($token, $key), $endpoint);

        $notification = $moip->notifications()->delete($notification_id);

        dd($notification);
    }   

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        if (!$this->validateCustomerDob($this->currentUser->date_of_birth)) {
            session()->flash('error', 'Wirecard: Informe sua data de nascimento para continuar.');
            return route('customer.profile.edit');
        }

        if (!$this->getCart()) {
            session()->flash('error', 'Wirecard: Você deve adicionar produtos no carrinho.');
            return route('shop.checkout.cart.index');
        }

        return route('wirecard.index');
    }

}