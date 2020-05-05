@if($order->payment->method == "wirecard")
    <div class="row">
        <span class="title">
            {{ __('Payment status') }}
        </span>
        <span class="value">
            @if("CREATED" == "CREATED")
                <small><span class="badge badge-md badge-warning">Criado</span></small>
                <small> - Pedido criado. Mas ainda não possui nenhum pagamento.</small>
            @elseif($order->payment->method == "WAITING")
                <span class="badge badge-md badge-warning">Aguardando</span>
                <small> - Pedido aguardando confirmação de pagamento. Indica que há um pagamento de cartão em análise ou um boleto que ainda não foi confirmado pelo banco.</small>
            @elseif($order->payment->method == "PAID")
                <span class="badge badge-md badge-success">Pago</span>
                <small> - Pedido pago. O pagamento criado nesse pedido foi autorizado.</small>
            @elseif($order->payment->method == "NOT_PAID")
                <span class="badge badge-md badge-danger">Não Pago</span>
                <small> - Pedido não pago. O pagamento criado nesse pedido foi cancelado (Pagamentos com cartão podem ser cancelados pela Wirecard ou pelo emissor do cartão, boletos são cancelados 5 dias após vencimento, débito bancário é cancelado em caso de falha).</small>
            @elseif($order->payment->method == "REVERTED")
                <span class="badge badge-md badge-info">Revertido</span>
                <small> - Pedido revertido. Sofreu um chargeback ou foi completamente reembolsado.</small>
            @endif
        </span>
    </div>
@endif