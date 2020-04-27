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
use Illuminate\View\View;
use InvalidArgumentException;
use Moip\Moip;
use Moip\Auth\BasicAuth;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;

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
     * @param Helper $helper
     */
    public function __construct(
        OrderRepository $orderRepository,
        Helper $helper,
        Wirecard $wirecard
    )
    {
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->wirecard = $wirecard;
    }

    /**
     * Digitar dados do cartao.
     * ao enviar a pagira direciona para a rota wirecar.pay
     */
    public function index()
    {
        return view('wirecard::index');
    }

    /**
     * Direciona para a funcao paymentRequest em Payment->Wirecard.php. 
     * Se não tiver erro no catch direciona para a rota sucess ou cancel
     * 
     * @return RedirectResponse
     */
    public function pay()
    {
        try {
            $redirect = $this->wirecard->paymentRequest();
            return redirect()->route('wirecard.success');
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
    public function success(Request $request, Wirecard $wirecard)
    {
        /**
         * @var \Webkul\Sales\Models\Order $order
         */
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

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

    }
}