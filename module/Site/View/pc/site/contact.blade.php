@extends($_viewFrame)

@section('pageTitleMain')联系客服@endsection

@section('bodyContent')

    {!! \ModStart\ModStart::js('asset/common/clipboard.js') !!}

    <div class="ub-container narrow margin-top margin-bottom">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    联系客服
                </div>
            </div>
            <div class="body">
                <table class="ub-table">
                    <tbody>
                    @if(modstart_config('Site_ContactEmail'))
                        <tr>
                            <td>
                                <div class="btn btn-sm btn-round">
                                    <i class="iconfont icon-email tw-w-6"></i>
                                </div>
                                <a target="_blank" href="mailto:{{modstart_config('Site_ContactEmail')}}">
                                    {{modstart_config('Site_ContactEmail')}}
                                </a>
                            </td>
                        </tr>
                    @endif
                    @if(modstart_config('Site_ContactPhone'))
                        <tr>
                            <td>
                                <div class="btn btn-sm btn-round">
                                    <i class="iconfont icon-address tw-w-6"></i>
                                </div>
                                <a target="_blank" href="tel:{{modstart_config('Site_ContactPhone')}}">
                                    {{modstart_config('Site_ContactPhone')}}
                                </a>
                            </td>
                        </tr>
                    @endif
                    @if(modstart_config('Site_ContactAddress'))
                        <tr>
                            <td>
                                <div class="btn btn-sm btn-round">
                                    <i class="iconfont icon-address tw-w-6"></i>
                                </div>
                                {{modstart_config('Site_ContactEmail')}}
                            </td>
                        </tr>
                    @endif
                    @if(modstart_config('Site_ContactQrcode'))
                        <tr>
                            <td>
                                <img src="{{modstart_config('Site_ContactQrcode')}}" class="tw-shadow-lg tw-rounded"
                                     style="max-width:200px;"/>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
