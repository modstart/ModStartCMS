@extends($_viewMemberFrame)

@section('pageTitleMain')我的{{\ModStart\Module\ModuleManager::getModuleConfig('Member', 'creditName', '积分')}}@endsection

@section('memberBodyContent')
    <div class="ub-panel transparent">
        <div class="body">
            <div class="ub-dashboard-item-a">
                <div class="icon">
                    <i class="font iconfont icon-credit"></i>
                </div>
                <div class="number-value">
                    <?php $m = \Module\Member\Util\MemberCreditUtil::get($_memberUserId); ?>
                    {{$m?$m['total']:0}}
                </div>
                <div class="number-title">
                    我的{{\ModStart\Module\ModuleManager::getModuleConfig('Member', 'creditName', '积分')}}
                    @if(!empty($m['freezeTotal']))
                        <span class="ub-text-sm ub-text-muted">
                            （冻结{{$m['freezeTotal']}}）
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="ub-panel">
        <div class="head">
            <div class="title">
                <i class="iconfont icon-list"></i>
                {{\ModStart\Module\ModuleManager::getModuleConfig('Member', 'creditName', '积分')}}流水
            </div>
        </div>
        <div class="body">
            {!! $content !!}
        </div>
    </div>
@endsection
