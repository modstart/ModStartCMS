@extends($_viewFrame)

@section('pageTitleMain')设置新密码@endsection
@section('pageKeywords')设置新密码@endsection
@section('pageDescription')设置新密码@endsection

@section('bodyContent')

    <div class="ub-account">

        <div class="box">

            <div class="nav">
                <a href="javascript:;" class="active">设置新密码</a>
            </div>

            @include('module::Member.View.pc.inc.retrieveNav',['current'=>2])

            <div class="ub-form flat">
                <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" method="post" data-ajax-form>
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
                            <button type="submit" class="btn btn-round btn-primary btn-lg btn-block">完成设置</button>
                        </div>
                    </div>
                    <input type="hidden" name="redirect" value="{{empty($redirect)?'':$redirect}}" />
                </form>
            </div>

            @include('module::Member.View.pc.oauthButtons')

            <div class="retrieve">
                忘记密码?
                <a target="_parent" href="{{$__msRoot}}retrieve?redirect={{urlencode($redirect)}}">找回密码</a>
            </div>

        </div>

    </div>

@endsection
