@extends($_viewFrame)

@section('pageTitle','设置新密码 - '.modstart_config('siteName'))

@section('bodyContent')

    <div class="ub-account">

        <div class="box">

            <div class="nav">
                <a href="javascript:;" class="active">设置新密码</a>
            </div>

            <div class="ub-form flat">
                <form action="?" method="post" data-ajax-form>
                    <div class="line">
                        <div class="label">
                            账户
                        </div>
                        <div class="field">
                            @if(!empty($memberUser['username']))
                                {{\ModStart\Core\Util\StrUtil::mask($memberUser['username'])}}
                            @elseif(!empty($memberUser['email']))
                                {{\ModStart\Core\Util\StrUtil::mask($memberUser['email'])}}
                            @elseif(!empty($memberUser['phone']))
                                {{\ModStart\Core\Util\StrUtil::mask($memberUser['phone'])}}
                            @endif
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <input type="password" class="form-lg" name="password" placeholder="新密码" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <input type="password" class="form-lg" name="passwordRepeat" placeholder="重复密码" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">完成设置</button>
                        </div>
                    </div>
                    <input type="hidden" name="redirect" value="{{$redirect or ''}}" />
                </form>
            </div>

            @include('module::Member.View.pc.oauthButtons')

            <div class="retrieve">
                忘记密码?
                <a href="{{$__msRoot}}retrieve?redirect={{urlencode($redirect)}}">找回密码</a>
            </div>

        </div>

    </div>

@endsection
