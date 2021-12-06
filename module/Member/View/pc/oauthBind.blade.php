@extends($_viewFrame)

@section('pageTitleMain')用户授权绑定@endsection
@section('pageKeywords')用户授权绑定@endsection
@section('pageDescription')用户授权绑定@endsection

@section('bodyContent')

    <div class="ub-account">

        <div class="box">

            <div class="nav">
                <a href="javascript:;" class="active">用户授权绑定</a>
            </div>

            <div class="ub-form flat">
                <form action="?" method="post" data-ajax-form>
                    <div class="line">
                        <div class="field ub-text-center">
                            <img style="height:4rem;" src="{{$oauthUserInfo['avatar'] or '/asset/image/avatar.png'}}" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <input type="text" class="form-lg ub-text-center" name="username" value="{{$oauthUserInfo['username'] or ''}}" placeholder="输入绑定用户名" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">绑定登录</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>

@endsection
