<?php
/**
 * Helper
 *
 * @copyright Copyright © 2020 Arthur Zanella. All rights reserved.
 * @author    Arthur Zanella <arthurzanella@gmail.com>
 */

namespace ArthurZanella\Wirecard\Helper;

use ArthurZanella\Wirecard\Payment\Wirecard;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\RefundRepository;
use function core;

/**
 * Class Helper
 * @package ArthurZanella\Wirecard\Helper
 */
class Helper
{
    /**
     * OrderRepository object
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var RefundRepository
     */
    protected $refundRepository;

    /**
     * Helper constructor.
     * @param OrderRepository $orderRepository
     * @param InvoiceRepository $invoiceRepository
     * @param RefundRepository $refundRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        RefundRepository $refundRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->refundRepository = $refundRepository;

    }

    /**
     * @param $response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateOrder($response)
    {

        /** @var \Webkul\Sales\Models\Order $order */
        if ($order = $this->orderRepository->findOneByField(['cart_id' => $response->reference])) {
            $this->orderRepository->update(['status' => self::PAYMENT_STATUS[$response->status]], $order->id);

            // If order is paid or waiting create the invoice
            if ($response->status === 'PAID' || $response->status === 'WAITING') {
                if ($order->canInvoice() && !$order->invoices->count()) {
                    $this->invoiceRepository->create($this->prepareInvoiceData($order));
                }
            }

            // Create refunds
            if ($response->status === 'REVERTED') {
                if ($order->canRefund()) {
                    $this->refundRepository->create($this->prepareRefundData($order));
                }
            }

            if ($response->status === 'NOT_PAID') {
                if ($order->canCancel()) {
                    $this->orderRepository->cancel($order->id);
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function prepareInvoiceData(Order $order)
    {
        $invoiceData = [
            "order_id" => $order->id,
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * @param \Webkul\Sales\Models\Order $order
     * @return array
     */
    protected function prepareRefundData(\Webkul\Sales\Models\Order $order)
    {
        $refundData = [
            "order_id" => $order->id,
            'adjustment_refund'      => $order->sub_tota,
            'base_adjustment_refund' => $order->base_sub_total,
            'adjustment_fee'         => 0,
            'base_adjustment_fee'    => 0,
            'shipping_amount'        => $order->shipping_invoiced,
            'base_shipping_amount'   => $order->base_shipping_invoiced,
        ];

        foreach ($order->items as $item) {
            $refundData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $refundData;
    }

    /**
     * @param $number
     * @return string|string[]|null
     */
    static public function justNumber($number)
    {
        return preg_replace('/\D/', '', $number);
    }

    /**
     * @param string $phoneString
     * @param bool $forceOnlyNumber
     * @return array|null
     */
    static function phoneParser(string $phoneString, bool $forceOnlyNumber = true): ?array
    {
        $phoneString = preg_replace('/[()]/', '', $phoneString);
        if (preg_match('/^(?:(?:\+|00)?(55)\s?)?(?:\(?([0-0]?[0-9]{1}[0-9]{1})\)?\s?)??(?:((?:9\d|[2-9])\d{3}\-?\d{4}))$/', $phoneString, $matches) === false) {
            return null;
        }

        $ddi = $matches[1] ?? '';
        $ddd = preg_replace('/^0/', '', $matches[2] ?? '');
        $number = $matches[3] ?? '';
        if ($forceOnlyNumber === true) {
            $number = preg_replace('/-/', '', $number);
        }

        return ['ddi' => $ddi, 'ddd' => $ddd, 'number' => $number];
    }

}