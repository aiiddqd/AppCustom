<form class="form-horizontal margin-top margin-bottom" method="POST" action="">
    {{ csrf_field() }}

    <div class="form-group{{ $errors->has('settings.woocommerce->url') ? ' has-error' : '' }}">
        <label class="col-sm-2 control-label">{{ __('Store URL') }}</label>

        <div class="col-sm-6">
            <div class="input-group input-sized-lg">
                <span class="input-group-addon input-group-addon-grey">https://</span>
                <input type="text" class="form-control input-sized-lg" name="settings[woocommerce.url]" value="{{ old('settings') ? old('settings')['woocommerce.url'] : $settings['woocommerce.url'] }}">
            </div>

            @include('partials/field_error', ['field'=>'settings.woocommerce->url'])

            <p class="form-help">
                {{ __('Example') }}: example.org/shop/
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('API Consumer Key') }}</label>

        <div class="col-sm-6">
            <input type="text" class="form-control input-sized-lg" name="settings[woocommerce.key]" value="{{ $settings['woocommerce.key'] }}">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('API Consumer Secret') }}</label>

        <div class="col-sm-6">
            <input type="text" class="form-control input-sized-lg" name="settings[woocommerce.secret]" value="{{ $settings['woocommerce.secret'] }}">

            <p class="form-help">
                {{ __('You can generate WooCommerce API credentials in your WordPress installation under "WooCommerce » Settings » Advanced » REST API"') }} (<a href="http://docs.woocommerce.com/document/woocommerce-rest-api/" target="_blank">Instruction</a>)
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('API Version') }}</label>

        <div class="col-sm-6">
            
            <div class="input-group input-sized-lg">
                <span class="input-group-addon input-group-addon-grey">v</span>
                <input type="number" class="form-control input-sized-lg" name="settings[woocommerce.version]" value="{{ $settings['woocommerce.version'] }}">
            </div>

            <p class="form-help">
                {!! __('You can find your WC API Version :%a_begin%here:%a_end%.', ['%a_begin%' => '<a href="https://woocommerce.github.io/woocommerce-rest-api-docs/#introduction" target="_blank">', '%a_end%' => '</a>']) !!}
            </p>
        </div>
    </div>

    <div class="form-group margin-top margin-bottom">
        <div class="col-sm-6 col-sm-offset-2">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>