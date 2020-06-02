<?php

$wirecardRepository = app('ArthurZanella\Wirecard\Repositories\WirecardRepository');

$reference = $wirecardRepository->getReference($order->id);
$status = $wirecardRepository->findWhere(['reference' => $reference]);

?>

@if($order->payment->method == "wirecard")
    <div class="row">
        <span class="title">
            {{ __('Wirecard status') }}
        </span>
        
        <span class="value" style="display: inline-grid;">
            @foreach ($status as $i)
                @if($i->status == "PAID")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-success" style="font-size: 13px; margin-right: 0.5rem;">Pago</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "CREATED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-warning" style="font-size: 13px; margin-right: 0.5rem;">Criado</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "WAITING")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-warning" style="font-size: 13px; margin-right: 0.5rem;">Aguardando</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "IN_ANALYSIS")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-warning" style="font-size: 13px; margin-right: 0.5rem;">Em análise</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "PRE_AUTHORIZED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-warning" style="font-size: 13px; margin-right: 0.5rem;">Pré autorizado</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "AUTHORIZED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-success" style="font-size: 13px; margin-right: 0.5rem;">Autorizado</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "CANCELLED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-danger" style="font-size: 13px; margin-right: 0.5rem;">Cancelado</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "REFUNDED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-info" style="font-size: 13px; margin-right: 0.5rem;">Reembolsado </span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "REVERSED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-info" style="font-size: 13px; margin-right: 0.5rem;">Estornado</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @elseif($i->status == "SETTLED")
                    <div style="margin-bottom: 0.6rem;">
                        <span class="badge badge-md badge-info" style="font-size: 13px; margin-right: 0.5rem;">Concluído</span>
                        <small><span>{{ $i->created_at }}</span></small>
                    </div>
                @endif
                
            @endforeach
        </span>
    </div>
    <div class="row">
        <span class="title">
            {{ __('Wirecard reference') }}
        </span>
        <span class="value">
                @if($reference)
                    {{ $reference }}
                @endif
        </span>
    </div>
@endif

@push('scripts')
<script>
    $(document).ready(function() {
        $('.badge').css('margin-button','10px');
    });
</script>
@endpush