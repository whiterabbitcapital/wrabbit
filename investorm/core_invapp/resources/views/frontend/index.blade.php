@extends('frontend.layouts.master')

@section('title', __(gss('front_page_title', "Welcome")))
@section('desc', gss('seo_description_home', gss('seo_description', '')))
@section('keyword', gss('seo_keyword_home', gss('seo_keyword', '')))

@section('content')
@if(!empty($schemes))
<section class="section {{ (gss('ui_page_skin', 'dark') == 'dark') ? 'bg-grad-stripe-botttom' : 'bg-lighter pt-5' }} py-1">
    <div class="container wide-lg">
        <div class="row justify-content-center g-gs">

            <div class="col-lg-4 order-lg-2">
                {{ Panel::scheme_card('home', ['class' => 'is-dark', 'button' => 'btn-primary', 'scheme' => $schemes['highlight']]) }}
            </div>

            <div class="col-lg-4 col-md-6">
                {{ Panel::scheme_card('home', ['scheme' => $schemes['one']]) }}
            </div>

            <div class="col-lg-4 col-md-6">
                {{ Panel::scheme_card('home', ['scheme' => $schemes['two']]) }}
            </div>

        </div>{{-- .row --}}
    </div>{{-- .container --}}
</section>
@endif

@if(gss('front_page_extra', 'on')=='on' && (!auth()->check() || (auth()->check() && auth()->user()->role=='user')))
<section class="section">
    <div class="container wide-lg">
        <div class="row g-gs">

            <div class="col-lg-8">
                <div class="row g-gs">
                    @if(!auth()->check())
                    <div class="col-sm-6 col-md-4">
                        <div class="card card-shadow text-center h-100">
                            <div class="card-inner">
                                <div class="card-image">
                                    <img src="{{ asset('images/icon-a.png') }}" alt="">
                                </div>
                                <div class="card-text mt-4">
                                    <h6 class="title fs-14px">{{ (gss('extra_step1_title')) ? __(gss('extra_step1_title')) : __("Register your free account") }}</h6>
                                    <p>{{ (gss('extra_step1_text')) ? __(gss('extra_step1_text')) : __("Sign up with your email and get started!") }}</p>
                                </div>
                            </div>
                            <div class="card-inner py-2 border-top mt-auto">
                                <a class="link" href="{{ route('auth.register.form') }}">{{ __("Create an account") }}</a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="{{ (auth()->check()) ? 'col-md-6' : 'col-md-4 col-sm-6' }}">
                        <div class="card card-shadow text-center h-100">
                            <div class="card-inner">
                                <div class="card-image">
                                    <img src="{{ asset('/images/icon-b.png') }}" alt="">
                                </div>
                                <div class="card-text mt-4">
                                    <h6 class="title fs-14px">{{ (gss('extra_step2_title')) ? __(gss('extra_step2_title')) : __("Deposit fund and invest") }}</h6>
                                    <p>{{ (gss('extra_step2_text')) ? __(gss('extra_step2_text')) : __("Just top up your balance & select your desired plan.") }}</p>
                                </div>
                            </div>
                            <div class="card-inner py-2 border-top mt-auto">
                                @if (!auth()->check())
                                <a class="link" href="{{ route('auth.register.form') }}">{{ __("Make a deposit") }}</a>
                                @else
                                <a class="link" href="{{ route('deposit') }}">{{ __("Make a deposit") }}</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="{{ (auth()->check()) ? 'col-md-6' : 'col-md-4' }}">
                        <div class="card card-shadow text-center h-100">
                            <div class="card-inner">
                                <div class="card-image">
                                    <img src="{{ asset('/images/icon-c.png') }}" alt="">
                                </div>
                                <div class="card-text mt-4">
                                    <h6 class="title fs-14px">{{ (gss('extra_step3_title')) ? __(gss('extra_step3_title')) : __("Payout your profits") }}</h6>
                                    <p>{{ (gss('extra_step3_text')) ? __(gss('extra_step3_text')) : __("Withdraw your funds to your account once earn profit.") }}</p>
                                </div>
                            </div>
                            <div class="card-inner py-2 border-top mt-auto">
                                @if (!auth()->check())
                                <a class="link" href="{{ route('auth.register.form') }}">{{ __("Withdraw profits") }}</a>
                                @else
                                <a class="link" href="{{ route('withdraw') }}">{{ __("Withdraw profits") }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-shadow text-center h-100">
                    <div class="card-inner card-inner-lg my-auto">
                        <div class="card-text my-lg-n2">
                            <h6 class="title fs-14px">{{ (gss('extra_step4_title')) ? __(gss('extra_step4_title')) : __("Payment processors we accept") }}</h6>
                            <p>{{ (gss('extra_step4_text')) ? __(gss('extra_step4_text')) : __("We accept paypal, cryptocurrencies such as Bitcoin, Litecoin, Ethereum more.") }}</p>
                            @php
                            $accepted_icons = gss('extra_step4_icons', ['paypal-alt', 'sign-btc', 'sign-eth', 'sign-ltc']);
                            @endphp

                            @if (!empty($accepted_icons) && is_array($accepted_icons))
                            <ul class="icon-list icon-bordered icon-rounded mb-3">
                                @foreach ($accepted_icons as $icon)
                                <li><em class="icon ni ni-{{ $icon }}"></em></li>
                                @endforeach
                            </ul>
                            @endif

                            <div class="payment-action">
                                @if (!auth()->check())
                                <a href="{{ route('auth.register.form') }}" class="btn btn-lg btn-primary btn-block"><span class="text-wrap">{{ __("Join now") }} {{ __("and") }} {{ __("make deposit") }}</span></a>
                                @else
                                <a class="btn btn-lg btn-primary btn-block" href="{{ route('deposit') }}">{{ __("Make a deposit") }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<div class="gap gap-lg"></div>
@endif

@endsection
