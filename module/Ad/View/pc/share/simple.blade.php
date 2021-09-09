@if(\ModStart\Module\ModuleManager::isModuleInstalled('Ad'))
    <?php $ad = \Module\Ad\Util\AdUtil::randomByPositionWithCache($position); ?>
    <div class="tw-rounded tw-bg-white tw-p-1">
        <a href="{{$ad['link']}}" target="_blank">
            <img style="width:100%;" src="{{\ModStart\Core\Assets\AssetsUtil::fix($ad['image'])}}" />
        </a>
    </div>
@endif
