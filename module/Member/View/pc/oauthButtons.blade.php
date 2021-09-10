@if(\Module\Member\Config\MemberOauth::hasItems())
    <div class="oauth">
        <div class="title">
            您还可以使用以下方式登录
        </div>
        <div class="body">
            @foreach(\Module\Member\Config\MemberOauth::get() as $oauth)
                @if($oauth->isSupport())
                    {!! $oauth->render() !!}
                @endif
            @endforeach
        </div>
    </div>
@endif
