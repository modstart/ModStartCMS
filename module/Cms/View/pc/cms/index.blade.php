@extends($_viewFrame)

@section('pageTitle'){{modstart_config('siteName').' - '.modstart_config('siteSlogan')}}@endsection

{!! \ModStart\ModStart::js('asset/common/timeago.js') !!}

@section('bodyContent')

    <div style="background:#FFF;">
        <div class="ub-container">
            @include('module::Banner.View.pc.public.banner',['position'=>'home','bannerRatio'=>'5-2'])
        </div>
    </div>

    <div class="ub-container">
        @include('module::Partner.View.pc.public.partner',['position'=>'home'])
    </div>

@endsection
