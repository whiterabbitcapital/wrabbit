@php
    $class = (isset($class) && !empty($class)) ? ' '.$class : '';
    $btnClass = (isset($button) && !empty($button)) ? $button : 'btn-light';
@endphp

@if (is_null(data_get($scheme, 'type')))
<div class="pricing card card-shadow h-100 round-lg text-center{{ $class }}">
    <div class="card-inner card-inner-lg h-100 d-flex flex-column">
        <h5 class="pricing-title">{{ data_get($scheme, 'name') }}</h5>
        <span class="fs-20px">{{ data_get($scheme, 'total_return') }}% {{ __("ROI") }}</span>
        <div class="pricing-parcent">
            <h3 class="percent">{{ data_get($scheme, 'rate_text') }}</h3>
            <h5 class="text">{{ __(data_get($scheme, 'calc_period')) }}</h5>
        </div>
        <ul class="pricing-feature">
            <li>
                <span>{{ __("Investment Period") }}</span>
                <span>{{ __(data_get($scheme, 'term_text_alter')) }}</span>
            </li>
            <li>
                <span>{{ __("Investments") }}</span>
                @if(data_get($scheme, 'is_fixed'))
                <span>{{ money(data_get($scheme, 'amount'), base_currency()) }}</span>
                @else
                <span>{{ money(data_get($scheme, 'amount'),  base_currency()) }} - {{ data_get($scheme, 'maximum') ? money(data_get($scheme, 'maximum'),  base_currency()) : __("Unlimited") }}</span>
                @endif
            </li>
            @if(sys_settings('iv_plan_terms_show') == 'yes')
            <li>
                <span class="label">{{ __('Term Duration') }}</span>
                <span class="data">{{ data_get($scheme, 'term_text_alter') }}</span>
            </li>
            @endif
            @if(sys_settings('iv_plan_payout_show') == 'yes')
            <li>
                <span class="label">{{ __('Payout Term') }}</span>
                <span class="data">{{ data_get($scheme, 'payout') == 'after_matured' ? __("After matured") : __("Term basis") }}</span>
            </li>
            @endif
            @if(sys_settings('iv_plan_capital_show') == 'yes')
            <li>
                <span>{{ __("Capital Return") }}</span>
                <span>{{ (data_get($scheme, 'capital') == 1) ? __("End of Term") : __("Each Term") }}</span>
            </li>
            @endif
        </ul>
        <div class="pricing-action mt-auto">
            @if (!auth()->check())
            <a class="btn {{ $btnClass }} btn-lg btn-block" href="{{ route('auth.register.form') }}">{{ __("Make a deposit") }}</a>
            @else
            <a class="btn {{ $btnClass }} btn-lg btn-block" href="{{ route('user.investment.invest', data_get($scheme, 'uid_code')) }}">{{ __("Invest Now") }}</a>
            @endif
        </div>
    </div>
</div>
@else
    @if (view()->exists('ExtInvest::frontend.plan-home'))
        @include('ExtInvest::frontend.plan-home')
    @endif
@endif
