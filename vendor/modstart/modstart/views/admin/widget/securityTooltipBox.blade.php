<div {!! $attributes !!}>
    <div class="ub-alert ub-alert-danger tw-hidden" data-system-notice></div>
    @if (\ModStart\Admin\Auth\AdminPermission::isDemo())
        <div class="ub-alert ub-alert-danger">
            <i class="iconfont icon-warning"></i>
            {{ L('You are a demo user, ADD/EDIT/DELETE is forbidden.') }}
        </div>
    @endif
    @if(!file_exists(storage_path('install.lock')))
        <div class="ub-alert ub-alert-warning">
            <i class="iconfont icon-warning"></i>
            {{L('Security Warning')}}: {{ L('System has been installed, bug storage/install.lock is missing.') }}
            <a href="javascript:;" data-ajax-request-loading data-ajax-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'installLock'])}}">{{L('Process Now')}}</a>
        </div>
    @endif
    @if(file_exists(public_path('install.php')))
        <div class="ub-alert ub-alert-warning">
            <i class="iconfont icon-warning"></i>
            {{L('Security Warning')}}: {{ L('install.php script not deleted, may expose sensitive data') }}
            <a href="javascript:;" data-ajax-request-loading data-ajax-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'installScript'])}}">{{L('Process Now')}}</a>
        </div>
    @endif
    @if(config('env.APP_DEBUG',false))
        <div class="ub-alert ub-alert-warning">
            <i class="iconfont icon-warning"></i>
            {{L('Security Warning')}}: {{L('System in debug mode ( APP_DEBUG=true ), error messages may expose sensitive data.')}}
            <a href="javascript:;" data-ajax-request-loading data-ajax-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'appDebug'])}}">{{L('Process Now')}}</a>
        </div>
    @endif
    @if(in_array(config('env.ADMIN_PATH'),['/admin/']))
        <div class="ub-alert ub-alert-warning">
            <i class="iconfont icon-warning"></i>
            {{L('Security Warning')}}: {{L('Admin url is /admin, it is easy to be attacked by hackers, please change to complex one.')}}
            <a href="javascript:;" data-dialog-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'adminPath'])}}">{{L('Process Now')}}</a>
        </div>
    @endif
    @if(\Illuminate\Support\Facades\Session::get('_adminUserPasswordWeak',false))
        <div class="ub-alert ub-alert-warning">
            <i class="iconfont icon-warning"></i>
            {{L('Security Warning')}}: {{L('Your password is weak, please change your password.')}}
            <a href="{{action('\ModStart\Admin\Controller\AdminUserController@changePassword')}}">{{L('Process Now')}}</a>
        </div>
    @endif
</div>
