<!-- {{ Form::open(array('url' => 'productservice','enctype' => "multipart/form-data")) }} -->
<!-- {{ Form::open(array('route' => 'currency.settings', 'enctype' => 'multipart/form-data', 'id' => 'myForm')) }} -->

{{ Form::model($setting, ['route' => 'currency.settings', 'method' => 'post', 'id' => 'currency_setting']) }}
<div class="modal-body">
    {{-- start for ai module--}}
    @php
        $plan= \App\Models\Utility::getChatGPTSettings();
    @endphp
    
    <div class="row">
      
<!--Currency Settings-->
<div id="currency-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Currency Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your currency details') }}</small>
                        </div>
                        <!-- {{ Form::model($setting, ['route' => 'currency.settings', 'method' => 'post', 'id' => 'currency_setting']) }} -->
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency', __('Currency *'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency', $setting['site_currency'], ['class' => 'form-control font-style currency_preview', 'required', 'placeholder' => __('Enter Currency')]) }}
                                    <small> {{ __('Note: Add currency code as per three-letter ISO code.') }}<br>
                                        <a href="https://stripe.com/docs/currencies"
                                            target="_blank">{{ __('You can find out how to do that here.') }}</a></small>
                                    <br>
                                    @error('site_currency')
                                        <span class="invalid-site_currency" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency_symbol', __('Currency Symbol *'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency_symbol', null, ['class' => 'form-control currency_preview']) }}
                                    @error('site_currency_symbol')
                                        <span class="invalid-site_currency_symbol" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::label('decimal_number', __('Decimal Number Format'), ['class' => 'form-label']) }}
                                    {{ Form::number('decimal_number', null, ['class' => 'form-control currency_preview']) }}
                                    @error('decimal_number')
                                        <span class="invalid-decimal_number" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="decimal_separator"
                                        class="form-label">{{ __('Decimal Separator') }}</label>
                                    <select type="text" name="decimal_separator"
                                        class="form-control selectric currency_preview" id="decimal_separator">
                                        <option value="dot"
                                            @if (@$setting['decimal_separator'] == 'dot') selected="selected" @endif>
                                            {{ __('Dot') }}</option>
                                        <option value="comma"
                                            @if (@$setting['decimal_separator'] == 'comma') selected="selected" @endif>
                                            {{ __('Comma') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="thousand_separator"
                                        class="form-label">{{ __('Thousands Separator') }}</label>
                                    <select type="text" name="thousand_separator"
                                        class="form-control selectric currency_preview" id="thousand_separator">
                                        <option value="dot"
                                            @if (@$setting['thousand_separator'] == 'dot') selected="selected" @endif>
                                            {{ __('Dot') }}</option>
                                        <option value="comma"
                                            @if (@$setting['thousand_separator'] == 'comma') selected="selected" @endif>
                                            {{ __('Comma') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label"
                                        for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                                    <div class="row ms-1">
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input currency_preview" type="radio"
                                                name="site_currency_symbol_position" value="pre"
                                                @if (@$setting['site_currency_symbol_position'] == 'pre') checked @endif id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                {{ __('Pre') }}
                                            </label>
                                        </div>
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input currency_preview" type="radio"
                                                name="site_currency_symbol_position" value="post"
                                                @if (@$setting['site_currency_symbol_position'] == 'post') checked @endif id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                                {{ __('Post') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('currency_space', __('Currency Symbol Space'), ['class' => 'form-label']) }}
                                    <div class="row ms-1">
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input currency_preview" type="radio"
                                                name="currency_space" value="withspace"
                                                @if (@$setting['currency_space'] == 'withspace') checked @endif id="withspace">
                                            <label class="form-check-label" for="withspace">
                                                {{ __('With space') }}
                                            </label>
                                        </div>
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input currency_preview" type="radio"
                                                name="currency_space" value="withoutspace"
                                                @if (@$setting['currency_space'] == 'withoutspace') checked @endif id="withoutspace">
                                            <label class="form-check-label" for="withoutspace">
                                                {{ __('Without space') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('currency_symbol', __('Currency Symbol & Name'), ['class' => 'form-label']) }}
                                    <div class="row ms-1">
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input currency_preview" type="radio"
                                                name="currency_symbol" value="withcurrencysymbol"
                                                @if (@$setting['currency_symbol'] == 'withcurrencysymbol') checked @endif id="withcurrencysymbol">
                                            <label class="form-check-label" for="withcurrencysymbol">
                                                {{ __('With Currency Symbol') }}
                                            </label>
                                        </div>
                                        <div class="form-check col-md-6">
                                            <input class="form-check-input currency_preview" type="radio"
                                                name="currency_symbol" value="withcurrencyname"
                                                @if (@$setting['currency_symbol'] == 'withcurrencyname') checked @endif id="withcurrencyname">
                                            <label class="form-check-label" for="withcurrencyname">
                                                {{ __('With Currency Name') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('preview', __('Preview : '), ['class' => 'form-label']) }}
                                    <div class="row">
                                        <div class="col-md-6 preview">
                                            {{ __('$ 10.000,00') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="card-footer text-end">
                            <div class="form-group">
                                <input class="btn btn-print-invoice btn-primary m-r-10" type="submit"
                                    value="{{ __('Save Changes') }}">
                            </div>
                        </div> -->
                        <!-- {{ Form::close() }} -->
                    </div>

                    <!--Currency Settings-->


    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{Form::close()}}

<script>
        $(document).on('keyup change', '.currency_preview', function() {
            var data = $('#currency_setting').serialize();
            $.ajax({
                type: 'POST',
                url: '{{ route('currency.preview') }}',
                data: data,
                success: function(price) {
                    $('.preview').text(price);
                }
            });
        });
    </script>

