@if(\Module\Member\Config\MemberOauth::hasItems())
    <div class="oauth">
        <div class="title">
            <div class="line">
                OR
            </div>
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
