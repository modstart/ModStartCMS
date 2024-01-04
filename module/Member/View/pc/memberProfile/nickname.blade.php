@extends($_viewMemberFrame)

@section('pageTitleMain')修改昵称@endsection
@section('pageKeywords')修改昵称@endsection
@section('pageDescription')修改昵称@endsection

@section('memberBodyContent')

    <div class="ub-panel">
        <div class="head">
            <div class="title">修改昵称</div>
        </div>
        <div class="body">
            <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" class="ub-form" method="post" style="max-width:40em;" data-ajax-form>

                <div class="line">
                    <div class="label">原昵称</div>
                    <div class="field">
                        {{ $_memberUser['nickname']?$_memberUser['nickname']:'[未设置]' }}
                    </div>
                </div>
                <div class="line">
                    <div class="label">新昵称：</div>
                    <div class="field">
                        <input type="text" class="form" name="nickname" />
                    </div>
                </div>
                <div class="line">
                    <div class="label">图形验证：</div>
                    <div class="field">
                        <div class="row">
                            <div class="col-4">
                                <img data-captcha src="{{$__msRoot}}member_profile/captcha" style="width:100%;height:30px;border:1px solid #CCC;border-radius:3px;cursor:pointer;" alt="刷新验证码" onclick="this.src='{{$__msRoot}}member_profile/captcha?'+Math.random();"/>
                            </div>
                            <div class="col-8">
                                <input class="form" type="text" name="captcha" />
                            </div>
                        </div>
                        <div class="help">
                        </div>
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <button type="submit" class="btn btn-round btn-primary">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
