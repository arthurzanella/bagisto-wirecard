<?php

namespace ArthurZanella\Wirecard\Http\Controllers;

use ArthurZanella\Wirecard\Helper\Helper;
use ArthurZanella\Wirecard\Payment\Wirecard;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use InvalidArgumentException;
use Moip\Moip;
use Moip\Auth\BasicAuth;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use ArthurZanella\Wirecard\Repositories\WirecardRepository;

/**
 * Class WirecardController
 * @package ArthurZanella\Wirecard\Http\Controllers
 */
class WirecardController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * WirecardRepository object
     *
     * @var WirecardRepository
     */
    protected $wirecardRepository;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var
     */
    protected $wirecard;

    /**
     * Create a new controller instance.
     *
     * @param OrderRepository $orderRepository
     * @param WirecardRepository $wirecardRepository
     * @param Helper $helper
     */
    public function __construct(
        OrderRepository $orderRepository,
        WirecardRepository $wirecardRepository,
        Helper $helper,
        Wirecard $wirecard
    )
    {
        $this->orderRepository = $orderRepository;
        $this->wirecardRepository = $wirecardRepository;
        $this->helper = $helper;
        $this->wirecard = $wirecard;
        $this->currentUser = auth()->guard('customer')->user();
    }

    /**
     * Digitar dados do cartao.
     * ao enviar a pagira direciona para a rota wirecar.pay
     */
    public function index()
    {
        return view('wirecard::index', ['user' => $this->currentUser, 'billingAddress' => '']);
    }

    /**
     * Direciona para a funcao paymentRequest em Payment->Wirecard.php. 
     * Se não tiver erro no catch direciona para a rota sucess ou cancel
     * 
     * @return RedirectResponse
     */
    public function pay(Request $request)
    {
        try {
            //$redirect = $this->wirecard->paymentRequest($request->hash);
            $payment = $this->wirecard->paymentRequest($request->hash);
            return redirect()->route('wirecard.success', ['reference' => $payment]);
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um problema: '.$e->getMessage());
            //session()->flash('error', 'Ocorreu um problema ao efetuar o pagamento, tente novamente mais tarde.');
            return redirect()->route('shop.checkout.cart.index');
        }
    }

    /**
     * Cancel payment from wirecard.
     *
     * @return Response
     */
    public function cancel()
    {
        session()->flash('error', 'Você cancelou o pagamento, pedido não finalizado');
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Conclui o pedido e direciona para a rota de pedido concluido
     * 
     * @return RedirectResponse
     * @throws Exception
     */
    public function success($reference)
    {
        /**
         * @var \Webkul\Sales\Models\Order $order
         */
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        //criar um registro de status na tabela 'wirecard'
        $status = $this->wirecardRepository->createStatus($order->id, null, $reference);

        Cart::deActivateCart();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }

    /**
     * @param Request $request
     * @param Wirecard $wirecard
     */
    public function notify(Request $request, Wirecard $wirecard)
    {
        try {
            $reference = $this->wirecard->getOwnIdNotify($request);
            $order_id = $this->$wirecardRepository->getOrderId($reference);
            $event = 'evento.teste';
            $status = $this->wirecardRepository->createStatus($order_id, 'aa', $reference, $event);
            //return $status;
        } catch (\Exception $e) {
            ///session()->flash('error', 'Ocorreu um problema: '.$e->getMessage());
            //return redirect()->route('shop.checkout.cart.index');
            abort(404);
        }
    }

    public function createWebhook(){
        try {
            $createWebhook = $this->wirecard->createWebhook();
            return $createWebhook;
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um problema: '.$e->getMessage());
            return redirect()->route('shop.checkout.cart.index');
        }
    }

    public function listWebhook(){
        try {
            $listWebhook = $this->wirecard->listWebhook();
            return $listWebhook;
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um problema: '.$e->getMessage());
            return redirect()->route('shop.checkout.cart.index');
        }
    }

    public function deleteWebhook($notification_id){
        try {
            $deleteWebhook = $this->wirecard->deleteWebhook($notification_id);
            return $deleteWebhook;
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um problema: '.$e->getMessage());
            return redirect()->route('shop.checkout.cart.index');
        }
    }

}