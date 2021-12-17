<div class="panel-heading">
    <h4 class="panel-title">
        <a data-toggle="collapse" href=".wc-collapse-orders">
            {{ __("Recent Orders") }}
            <b class="caret"></b>
        </a>
    </h4>
</div>
<div class="wc-collapse-orders panel-collapse collapse in">
    <div class="panel-body">
        <div class="sidebar-block-header2"><strong>{{ __("Recent Orders") }}</strong> (<a data-toggle="collapse" href=".wc-collapse-orders">{{ __('close') }}</a>)</div>
       	<div id="wc-loader">
        	<img src="{{ asset('img/loader-tiny.gif') }}" />
        </div>
        	
        @if (!$load)
            @if (count($orders)) 
			    <ul class="sidebar-block-list wc-orders-list">
                    @foreach($orders as $order)
                        <li>
                            <div>
                                <a href="{{ $url }}wp-admin/post.php?post={{ $order['id'] }}&amp;action=edit" target="_blank">#{{ $order['number'] }}</a>
                                <span class="pull-right">{{ $order['currency'] }} {{ $order['total'] }}</span>
                            </div>
                            <div>
                                <small class="text-help">{{ \WooCommerce::formatDate($order['date_created']) }}</small>
                                <small class="pull-right @if ($order['status'] == 'completed') text-success @else text-warning @endif ">
                                    {{ __(ucfirst($order['status'])) }}
                                </small>
                            </div>
                        </li>
                    @endforeach
                </ul>
			@else
			    <div class="text-help margin-top-10 wc-no-orders">{{ __("No orders found") }}</div>
			@endif
        @endif
   
        <div class="margin-top-10 wc-refresh small">
            <a href="#" class="sidebar-block-link"><i class="glyphicon glyphicon-refresh"></i> {{ __("Refresh") }}</a>
        </div>
	   
    </div>
</div>
