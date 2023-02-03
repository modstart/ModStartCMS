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
                <a href="{{$urlAdd}}" class="btn btn-primary" data-tab-open>
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
            canBatchSelect: {!! json_encode(boolval($canBatchSelect)) !!},
            canSingleSelectItem: {!! json_encode(boolval($canSingleSelectItem)) !!},
            canMultiSelectItem: {!! json_encode(boolval($canMultiSelectItem)) !!},
            title: {!! json_encode($title) !!},
            titleAdd: {!! json_encode(empty($titleAdd)?null:$titleAdd) !!},
            titleEdit: {!! json_encode(empty($titleEdit)?null:$titleEdit) !!},
            titleShow: {!! json_encode(empty($titleShow)?null:$titleShow) !!},
            titleImport: {!! json_encode(empty($titleImport)?null:$titleImport) !!},
            canAdd: {!! json_encode(boolval($canAdd)) !!},
            canEdit: {!! json_encode(boolval($canEdit)) !!},
            canDelete: {!! json_encode(boolval($canDelete)) !!},
            canShow: {!! json_encode(boolval($canShow)) !!},
            canSort: {!! json_encode(boolval($canSort)) !!},
            canExport: {!! json_encode(boolval($canExport)) !!},
            canImport: {!! json_encode(boolval($canImport)) !!},
            canBatchDelete: {!! json_encode(boolval($canBatchDelete)) !!},
            urlAdd: {!! json_encode($urlAdd) !!},
            urlEdit: {!! json_encode($urlEdit) !!},
            urlDelete: {!! json_encode($urlDelete) !!},
            urlShow: {!! json_encode($urlShow) !!},
            urlExport: {!! json_encode($urlExport) !!},
            urlImport: {!! json_encode($urlImport) !!},
            urlSort: {!! json_encode($urlSort) !!},
            batchOperatePrepend: {!! json_encode($batchOperatePrepend) !!},
            gridToolbar: {!! json_encode($gridToolbar) !!},
            defaultPageSize: {!! json_encode($defaultPageSize) !!},
            gridBeforeRequestScript: {!! json_encode($gridBeforeRequestScript) !!},
            pageSizes: {!! json_encode($pageSizes) !!},
            addDialogSize: {!! json_encode($addDialogSize) !!},
            editDialogSize: {!! json_encode($editDialogSize) !!},
            showDialogSize: {!! json_encode($showDialogSize) !!},
            importDialogSize: {!! json_encode($importDialogSize) !!},
            pageJumpEnable: {!! json_encode(boolval($pageJumpEnable)) !!},
            lang:{
                loading: {!! json_encode(L('Loading')) !!},
                noRecords: {!! json_encode(L('No Records')) !!},
                add: {!! json_encode(L('Add')) !!},
                edit: {!! json_encode(L('Edit')) !!},
                show: {!! json_encode(L('Show')) !!},
                import: {!! json_encode(L('Import')) !!},
                confirmDelete: {!! json_encode(L('Confirm Delete ?')) !!},
                pleaseSelectRecords: {!! json_encode(L('Please Select Records')) !!},
                confirmDeleteRecords: {!! json_encode(L('Confirm Delete %d records ?')) !!},
            },
        });
    })();
</script>
{!! $bodyAppend !!}
