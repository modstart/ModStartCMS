<div {!! $attributes !!}>
    <div class="ub-alert danger tw-hidden" data-system-notice></div>
    @if($queueDelaySize>0)
        <div class="ub-alert danger">
            <i class="iconfont icon-warning"></i>
            {{L('SystemWarning')}}: {{ L('QueuePendingJobs',$queueDelaySize) }}
            <a href="https://modstart.com/doc" target="_blank" rel="noreferrer">{{L('ViewNow')}}</a>
        </div>
    @endif
    @if($scheduleRunLastRun < time() - 24*3600)
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SystemWarning')}}: {{ L('ScheduleTasksNotRun') }}
            ( {{date('Y-m-d H:i:s',$scheduleRunLastRun)}} )
            <a href="https://modstart.com/doc" target="_blank" rel="noreferrer">{{L('ViewNow')}}</a>
        </div>
    @endif
    @if (\ModStart\Admin\Auth\AdminPermission::isDemo())
        <div class="ub-alert danger">
            <i class="iconfont icon-warning"></i>
            {{ L('DemoUserForbidden') }}
        </div>
    @endif
    @if(!file_exists(storage_path('install.lock')))
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{ L('SystemInstallLockMissing') }}
            <a href="javascript:;" data-ajax-request-loading data-ajax-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'installLock'])}}">{{L('ProcessNow')}}</a>
        </div>
    @endif
    @if(file_exists(public_path('install.php')))
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{ L('InstallPhpNotDeleted') }}
            <a href="javascript:;" data-ajax-request-loading data-ajax-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'installScript'])}}">{{L('ProcessNow')}}</a>
        </div>
    @endif
    @if(config('env.APP_DEBUG',false))
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{L('SystemInDebugMode')}}
            <a href="javascript:;" data-ajax-request-loading data-ajax-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'appDebug'])}}">{{L('ProcessNow')}}</a>
        </div>
    @endif
    @if(in_array(config('env.ADMIN_PATH'),['/admin/']))
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{L('AdminUrlEasyToAttack')}}
            <a href="javascript:;" data-dialog-request="{{action('\ModStart\Admin\Controller\SystemController@securityFix',['type'=>'adminPath'])}}">{{L('ProcessNow')}}</a>
        </div>
    @endif
    @if(\Illuminate\Support\Facades\Session::get('_adminUserPasswordWeak',false))
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{L('PasswordIsWeak')}}
            <a href="{{action('\ModStart\Admin\Controller\ProfileController@changePassword')}}">{{L('ProcessNow')}}</a>
        </div>
    @endif
    @if(config('env.APP_KEY')=='AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA')
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{L('IsDefault','APP_KEY')}}
        </div>
    @endif
    @if(config('env.ENCRYPT_KEY')=='AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA')
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            {{L('SecurityWarning')}}: {{L('IsDefault','ENCRYPT_KEY')}}
        </div>
    @endif
</div>
