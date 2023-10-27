<div id="{{$id}}" data-grid data-basic-lister class="ub-lister-table-container">
    @if(!empty($scopes))
        <div class="tw-pb-3">
            <div class="ub-nav-tab mini">
                @foreach($scopes as $scope)
                    <a class="{{$scopeCurrent==$scope['name']?'active':''}}"
                       href="?{{\ModStart\Core\Input\Request::mergeQueries(['_scope'=>$scope['name']])}}">
                        {{$scope['title']}}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
    <div class="toolbox-container">
        @if($canAdd)
            @if($addBlankPage)
                <a href="{{$urlAdd}}" class="btn btn-primary" data-tab-open data-refresh-grid-on-close>
                    <i class="iconfont icon-plus"></i> {{L('Add')}}
                </a>
            @else
                <a href="javascript:;" class="btn btn-primary" data-add-button>
                    <i class="iconfont icon-plus"></i> {{L('Add')}}
                </a>
            @endif
        @endif
        @if($canImport)
            <a href="javascript:;" class="btn btn-primary" data-import-button>
                <i class="iconfont icon-upload"></i> {{L('Import')}}
            </a>
        @endif
        {!! $gridOperateAppend !!}
    </div>
    <div data-search class="ub-lister-search">
        @foreach($filters as $filter)
            @if(!$filter->autoHide())
                {!! $filter->render() !!}
            @endif
        @endforeach
        <div class="field">
            @if(!count($filters))
                <button class="btn" data-search-button>
                    <i class="iconfont icon-refresh"></i> {{L('Refresh')}}
                </button>
            @endif
            @if(count($filters)>0)
                <button class="btn btn-primary" data-search-button>
                    <i class="iconfont icon-search"></i> {{L('Search')}}
                </button>
                <button class="btn" data-reset-search-button>
                    <i class="iconfont icon-refresh"></i> {{L('Reset')}}
                </button>
            @endif
            @if($canExport)
                <button class="btn" data-export-button>
                    <i class="iconfont icon-download"></i> {{L('Export')}}
                </button>
            @endif
            @if($hasAutoHideFilters)
                <button class="btn" data-expand-search-button>
                    <i class="iconfont icon-filter"></i>
                    {{L('More')}}
                </button>
            @endif
        </div>
        <div class="field-more-expand">
            @foreach($filters as $filter)
                @if($filter->autoHide())
                    {!! $filter->render() !!}
                @endif
            @endforeach
        </div>
    </div>
    @if(!empty($gridTableTops))
        <div data-table-top>
            @foreach($gridTableTops as $c)
                {!! $c !!}
            @endforeach
        </div>
    @endif
    <div data-addition class="table-addition-container"></div>
    <div data-table class="table-container">
        <table class="table-container" id="{{$id}}Table" lay-filter="{{$id}}Table"></table>
    </div>
    @if($enablePagination)
        <div class="page-container" id="{{$id}}Pager"></div>
    @endif
    <script type="text/html" id="{{$id}}TableHeadToolbar">
        <div class="layui-btn-container">
            {!! $batchOperatePrepend !!}
            @if($canDelete && $canBatchDelete)
                <button class="btn" data-batch-delete><i class="iconfont icon-trash"></i> {{L('Batch Delete')}}</button>
            @endif
        </div>
    </script>
</div>
<script src="@asset('asset/common/gridManager.js')"></script>
<script>
    (function () {
        new MS.GridManager({
            id: '{{$id}}',
            canBatchSelect: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canBatchSelect)) !!},
            canSingleSelectItem: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canSingleSelectItem)) !!},
            canMultiSelectItem: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canMultiSelectItem)) !!},
            title: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($title) !!},
            titleAdd: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(empty($titleAdd)?null:$titleAdd) !!},
            titleEdit: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(empty($titleEdit)?null:$titleEdit) !!},
            titleShow: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(empty($titleShow)?null:$titleShow) !!},
            titleImport: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(empty($titleImport)?null:$titleImport) !!},
            canAdd: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canAdd)) !!},
            canEdit: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canEdit)) !!},
            canDelete: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canDelete)) !!},
            canShow: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canShow)) !!},
            canSort: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canSort)) !!},
            canExport: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canExport)) !!},
            canImport: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canImport)) !!},
            canBatchDelete: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($canBatchDelete)) !!},
            urlGrid: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlGrid) !!} || window.location.href,
            urlAdd: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlAdd) !!},
            urlEdit: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlEdit) !!},
            urlDelete: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlDelete) !!},
            urlShow: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlShow) !!},
            urlExport: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlExport) !!},
            urlImport: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlImport) !!},
            urlSort: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($urlSort) !!},
            batchOperatePrepend: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($batchOperatePrepend) !!},
            gridToolbar: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($gridToolbar) !!},
            defaultPageSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($defaultPageSize) !!},
            gridBeforeRequestScript: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($gridBeforeRequestScript) !!},
            pageSizes: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($pageSizes) !!},
            addDialogSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($addDialogSize) !!},
            editDialogSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($editDialogSize) !!},
            showDialogSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($showDialogSize) !!},
            importDialogSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($importDialogSize) !!},
            pageJumpEnable: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(boolval($pageJumpEnable)) !!},
            lang:{
                loading: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Loading')) !!},
                noRecords: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('No Records')) !!},
                add: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Add')) !!},
                edit: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Edit')) !!},
                show: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Show')) !!},
                import: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Import')) !!},
                confirmDelete: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Confirm Delete ?')) !!},
                pleaseSelectRecords: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Please Select Records')) !!},
                confirmDeleteRecords: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(L('Confirm Delete %d records ?')) !!},
            },
        });
    })();
</script>
{!! $bodyAppend !!}
