@extends('shop::layouts.master')

@section('page_title')
    {{ __('Wirecard') }}
@stop


@push('css')
    <style>
        .button-group {
            margin-bottom: 25px;
        }
        .primary-back-icon {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content-wrapper')


<div class="auth-content">

    <form method="post" action="{{ route('wirecard.pay') }}" @submit.prevent="onSubmit">

        {{ csrf_field() }}

        <div class="login-form">

            <div class="login-text">{{ __('Credit card') }}</div>

            <div class="control-group" :class="[errors.has('public_key') ? 'has-error' : '']" style="display:none">
                <label for="public_key">{{ __('Public key') }}</label>
                <textarea name="public_key" id="public_key" class="control" style="">-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApoTLQKM7kpPOvCH3Fjyq
i4X1KSet3xRJhYpwdjUM/D7VjNqciwOu6AH7t2AVorERY8BxzbWbkuFkvHJTo1zA
sZWcixSnGakrm3RvO+MX4tEGZ9ayIvTUZBKkJcNOooK3rFWjMA7fsu/MzdiYqHUd
R+CCR/rtY5RnG/NlaebT0hApvuL2Kq7cSTrAeVJiskqsuX9/Fsh+qKguenx0ASGC
/sz6we55ANuKghtGeiPhA0Y//cTPOWtRTjTY+8sXkv8lPnS6oUMNZJZgzAc4/rk0
uXHLf/j9lE99kAR0jfxqo/JP4PQqeh/+9Vawd3EwzYfWBxlJ5jSz1+1GEWMUWY5X
SQIDAQAB
-----END PUBLIC KEY-----</textarea>
                <span class="control-error" v-if="errors.has('public_key')">@{{ errors.first('public_key') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('holder') ? 'has-error' : '']">
                <label for="holder">{{ __('Holder') }}</label>
                <input type="text" class="control" name="holder" id="holder" value="JOAO NINGUEM" v-validate="'required'">
                <span class="control-error" v-if="errors.has('holder')">@{{ errors.first('holder') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('number') ? 'has-error' : '']">
                <label for="number">{{ __('Number') }}</label>
                <input type="text" class="control" name="number" id="number" value="5555666677778884" v-validate="'required'" >
                <span class="control-error" v-if="errors.has('number')">@{{ errors.first('number') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('month') ? 'has-error' : '']">
                <label for="month">{{ __('Month') }}</label>
                <input type="text" class="control" name="month" id="month" value="12" v-validate="'required'" >
                <span class="control-error" v-if="errors.has('month')">@{{ errors.first('month') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('year') ? 'has-error' : '']">
                <label for="year">{{ __('Year') }}</label>
                <input type="text" class="control" name="year" id="year" value="2022" v-validate="'required'" >
                <span class="control-error" v-if="errors.has('year')">@{{ errors.first('year') }}</span>
            </div>

            <div class="control-group" :class="[errors.has('cvc') ? 'has-error' : '']">
                <label for="cvc">{{ __('CVC') }}</label>
                <input type="text" class="control" name="cvc" id="cvc" value="123" v-validate="'required'" >
                <span class="control-error" v-if="errors.has('cvc')">@{{ errors.first('cvc') }}</span>
            </div>

            <small class="text-secondary text-center">Pagamento processado através do Wirecard.</small>

            <div class="button-group">
                <button type="submit" class="btn btn-lg btn-primary">
                    {{ __('Pay') }}
                </button>
            </div>

            <div class="control-group" style="margin-bottom: 0px;">
                <a href="{{ route('wirecard.cancel') }}">
                    <i class="icon primary-back-icon"></i>
                    {{ __('Cancel') }}
                </a>
            </div>            

            <hr>

            <!-- <div class="control-group" style="margin-bottom: 0px;">
                <div class="form-check">
                    <label class="form-check-label">
                        <input id="holder_check" type="checkbox" class="form-check-input" value="">Editar endereço de cobrança
                    </label>
                </div>
            </div>

            <div id="holder_div" class="card-body pt-0" style="display: block;">

                <h3>Dados do proprietário do cartão de crédito</h3>

                <div class="control-group" :class="[errors.has('holder_full_name') ? 'has-error' : '']">
                    <label for="holder_full_name">{{ __('holder_full_name') }}</label>
                    <input type="text" class="control" name="holder_full_name" id="holder_full_name" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_full_name')">@{{ errors.first('holder_full_name') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_birt_date') ? 'has-error' : '']">
                    <label for="holder_birt_date">{{ __('holder_birt_date') }}</label>
                    <input type="text" class="control" name="holder_birt_date" id="holder_birt_date" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_birt_date')">@{{ errors.first('holder_birt_date') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_cpf') ? 'has-error' : '']">
                    <label for="holder_cpf">{{ __('holder_cpf') }}</label>
                    <input type="text" class="control" name="holder_cpf" id="holder_cpf" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_cpf')">@{{ errors.first('holder_cpf') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_phone_ddd') ? 'has-error' : '']">
                    <label for="holder_phone_ddd">{{ __('holder_phone_ddd') }}</label>
                    <input type="text" class="control" name="holder_phone_ddd" id="holder_phone_ddd" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_phone_ddd')">@{{ errors.first('holder_phone_ddd') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_phone_number') ? 'has-error' : '']">
                    <label for="holder_phone_number">{{ __('holder_phone_number') }}</label>
                    <input type="text" class="control" name="holder_phone_number" id="holder_phone_number" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_phone_number')">@{{ errors.first('holder_phone_number') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_street') ? 'has-error' : '']">
                    <label for="holder_address_street">{{ __('holder_address_street') }}</label>
                    <input type="text" class="control" name="holder_address_street" id="holder_address_street" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_street')">@{{ errors.first('holder_address_street') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_number') ? 'has-error' : '']">
                    <label for="holder_address_number">{{ __('holder_address_number') }}</label>
                    <input type="text" class="control" name="holder_address_number" id="holder_address_number" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_number')">@{{ errors.first('holder_address_number') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_district') ? 'has-error' : '']">
                    <label for="holder_address_district">{{ __('holder_address_district') }}</label>
                    <input type="text" class="control" name="holder_address_district" id="holder_address_district" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_district')">@{{ errors.first('holder_address_district') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_city') ? 'has-error' : '']">
                    <label for="holder_address_city">{{ __('holder_address_city') }}</label>
                    <input type="text" class="control" name="holder_address_city" id="holder_address_city" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_city')">@{{ errors.first('holder_address_city') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_state_code') ? 'has-error' : '']">
                    <label for="holder_address_state_code">{{ __('holder_address_state_code') }}</label>
                    <input type="text" class="control" name="holder_address_state_code" id="holder_address_state_code" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_state_code')">@{{ errors.first('holder_address_state_code') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_zip') ? 'has-error' : '']">
                    <label for="holder_address_zip">{{ __('holder_address_zip') }}</label>
                    <input type="text" class="control" name="holder_address_zip" id="holder_address_zip" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_zip')">@{{ errors.first('holder_address_zip') }}</span>
                </div>

                <div class="control-group" :class="[errors.has('holder_address_complemento') ? 'has-error' : '']">
                    <label for="holder_address_complemento">{{ __('holder_address_complemento') }}</label>
                    <input type="text" class="control" name="holder_address_complemento" id="holder_address_complemento" value="" v-validate="'required'" >
                    <span class="control-error" v-if="errors.has('holder_address_complemento')">@{{ errors.first('holder_address_complemento') }}</span>
                </div>

                <input type="hidden" class="form-control" name="hash" id="hash" placeholder="" required="">
                <input type="hidden" class="form-control" name="brand" id="brand" placeholder="" required="">

            </div> -->

        </div>
    </form>

</div>
@endsection


@push('scripts')
    <script type="text/javascript" src="https://assets.moip.com.br/v2/moip.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <!-- gerar hash wirecard -->
    <script type="text/javascript">

        jQuery(document).ready(function(){

            function getHash() {
                var cc = new Moip.CreditCard({
                    number  : $("#number").val(),
                    cvc     : $("#cvc").val(),
                    expMonth: $("#month").val(),
                    expYear : $("#year").val(),
                    pubKey  : $("#public_key").val()
                });
                console.log(cc);
                if(cc.isValid()){
                    $("#hash").val(cc.hash());
                    $("#brand").val(cc.cardType());
                    $("#loading").prop('disabled', false);
                }
                else{
                    $("#hash").val('');
                    $("#brand").val('');
                    $("#loading").prop('disabled', true);
                    console.log('Invalid credit card. Verify parameters: number, cvc, expiration Month, expiration Year');
                }
            };
            $("#deeplink").click(function() {
                window.location.replace("wirecard://payment?paymentId=1093019888&paymentType=debit&amount=10&installmentType=1&installments=3&scheme=instore");
            });
            $("#number").keyup(function() {
                getHash();
            });
            $("#cvc").keyup(function() {
                getHash();
            });
            $("#month").keyup(function() {
                getHash();
            });
            $("#year").keyup(function() {
                getHash();
            });

        });
        getHash();
    </script>
    
@endpush