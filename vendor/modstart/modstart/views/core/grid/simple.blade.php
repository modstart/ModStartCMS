<div id="{{$id}}" data-basic-lister class="ub-lister-table-container">
    <div class="toolbox-container">
        @if($canAdd)
            @if($addBlankPage)
                <a href="{{$urlAdd}}" class="btn btn-primary">
                    <i class="iconfont icon-plus"></i> {{L('Add')}}
                </a>
            @else
                <a href="javascript:;" class="btn btn-primary" data-add-button>
                    <i class="iconfont icon-plus"></i> {{L('Add')}}
                </a>
            @endif
        @endif
    </div>
    <div data-search class="ub-lister-search" style="min-height:2rem;">
        @foreach($filters as $filter)
            {!! $filter->render() !!}
        @endforeach
        <div class="field">
            @if(!count($filters))
                <button class="btn" data-refresh-button>
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
        </div>
    </div>
{{--    <div data-addition class="table-addition-container"></div>--}}
{{--    <div data-table class="table-container">--}}
{{--        <table class="table-container" id="{{$id}}Table" lay-filter="{{$id}}Table"></table>--}}
{{--    </div>--}}
    <div style="overflow:hidden;" data-table></div>
    @if($enablePagination)
        <div id="{{$id}}Pager" class="padding-top"></div>
    @endif
    <script type="text/html" id="{{$id}}pageHtml">
        <div class="ub-paginate-mobile">
            @{{#  if(d.totalPage >= 1){ }}
                @{{#  if(d.page > 1){ }}
                <a class="ub-paginate-mobile__btn ub-paginate-mobile--enabled" href="javascript:;" data-page-action="prev">
                    <span class="ub-paginate-mobile__child-btn">{{L('PrevPage')}}</span>
                </a>
                @{{# }else{  }}
                <a class="ub-paginate-mobile__btn ub-paginate-mobile--disabled" href="javascript:;">
                    <span class="ub-paginate-mobile__child-btn">{{L('PrevPage')}}</span>
                </a>
                @{{# } }}
                <div class="ub-paginate-mobile__num">
                    <div class="ub-paginate-mobile__num-page">
                        <span class="ub-paginate-mobile__num-page-span current">@{{ d.page }}</span>
                        <span class="ub-paginate-mobile__num-page-span">/@{{ d.totalPage }}</span>
                    </div>
                </div>
                @{{#  if(d.page < d.totalPage){ }}
                <a class="ub-paginate-mobile__btn ub-paginate-mobile--enabled" href="javascript:;" data-page-action="next">
                    <span class="ub-paginate-mobile__child-btn">{{L('NextPage')}}</span>
                </a>
                @{{# }else{  }}
                <a class="ub-paginate-mobile__btn ub-paginate-mobile--disabled" href="javascript:;">
                    <span class="ub-paginate-mobile__child-btn">{{L('NextPage')}}</span>
                </a>
                @{{# } }}
            @{{# } }}
        </div>
    </script>
    <script type="text/html" id="{{$id}}emptyHtml">
        <div class="ub-empty">
            <div class="icon">
                <div class="iconfont icon-empty-box"></div>
            </div>
            <div class="text">{{L('No Records')}}</div>
        </div>
    </script>
</div>
<script>
    (function () {
        var $grid = $('#{{$id}}');
        var listerData = {
            page: 1,
            pageSize: 1,
            records: [],
            total: 1,
            head: []
        };
        var pageHtml = $('#{{$id}}pageHtml').html();
        var emptyHtml = $('#{{$id}}emptyHtml').html();
        var getId = function (o) {
            var index = parseInt($(o).closest('[data-index]').attr('data-index'));
            return listerData.records[index]._id;
        };
        {{--var getCheckedIds = function () {--}}
        {{--    var data = layui.table.checkStatus('{{$id}}Table').data;--}}
        {{--    var ids = [];--}}
        {{--    for (var i = 0; i < data.length; i++) {--}}
        {{--        ids.push(data[i]._id);--}}
        {{--    }--}}
        {{--    return ids;--}}
        {{--};--}}
        {{--var getCheckedItems = function () {--}}
        {{--    var data = layui.table.checkStatus('{{$id}}Table').data;--}}
        {{--    var items = [];--}}
        {{--    for (var i = 0; i < data.length; i++) {--}}
        {{--        items.push(data[i]);--}}
        {{--    }--}}
        {{--    return items;--}}
        {{--};--}}
        layui.use(['laytpl'], function () {
            {{--var table = layui.table.render({--}}
            {{--    id: '{{$id}}Table',--}}
            {{--    elem: '#{{$id}}Table',--}}
            {{--    @if($canMultiSelectItem && ( $batchOperatePrepend || ($canDelete && $canBatchDelete)  ))--}}
            {{--        toolbar: '#{{$id}}TableHeadToolbar',--}}
            {{--    @endif--}}
            {{--    defaultToolbar: [],--}}
            {{--    page: false,--}}
            {{--    skin: 'line',--}}
            {{--    text: {--}}
            {{--        none: '{{L('No Records')}}'--}}
            {{--    },--}}
            {{--    // size: 'sm',--}}
            {{--    loading: true,--}}
            {{--    cellMinWidth: 100,--}}
            {{--    cols: [[]],--}}
            {{--    data: []--}}
            {{--});--}}
            {{--layui.table.on('sort({{$id}}Table)', function (obj) {--}}
            {{--    if (null == obj.type) {--}}
            {{--        lister.setParam('order', []);--}}
            {{--    } else {--}}
            {{--        lister.setParam('order', [[obj.field, obj.type]]);--}}
            {{--    }--}}
            {{--    lister.setPage(1);--}}
            {{--    lister.load();--}}
            {{--})--}}

            $('#{{$id}}Pager').on('click','[data-page-action]',function(){
                var action = $(this).attr('data-page-action');
                switch(action){
                    case 'prev':
                        lister.setPage(listerData.page-1);
                        break;
                    case 'next':
                        lister.setPage(listerData.page+1);
                        break;
                }
                lister.load();
            });
            var $lister = $('#{{$id}}');
            var lister = new window.api.lister({
                lister: $lister,
                search: $lister.find('[data-search]'),
                table: $lister.find('[data-table]')
            }, {
                hashUrl: false,
                server: window.location.href,
                render: function (data) {
                    listerData = data;
                    data.totalPage = Math.ceil(data.total / data.pageSize);
                    layui.laytpl(pageHtml).render(data, function(html){
                        $('#{{$id}}Pager').html(html);
                    });
                    var html = [];
                    @if(!empty($gridRowCols))
                        html.push('<div class="row">');
                        for(var i =0;i<data.records.length;i++){
                            html.push('<div class="col-md-{{$gridRowCols[0]}} col-{{$gridRowCols[1]}}" data-index="'+i+'">'+data.records[i].html+'</div>');
                        }
                        html.push('</div>');
                    @else
                        for(var i =0;i<data.records.length;i++){
                            html.push('<div data-index="'+i+'">'+data.records[i].html+'</div>');
                        }
                    @endif
                    $grid.find('[data-table]').html(html.join(''));
                    if(!data.records.length){
                        $grid.find('[data-table]').html(emptyHtml);
                    }
                    // console.log('render', html, data);
{{--                    @if($canSingleSelectItem)--}}
{{--                        data.head.splice(0, 0, {type: 'radio'});--}}
{{--                    @elseif($canMultiSelectItem)--}}
{{--                        data.head.splice(0, 0, {type: 'checkbox'});--}}
{{--                    @endif--}}
{{--                    $grid.find('[data-addition]').html(data.addition || '');--}}
{{--                    layui.table.reload('{{$id}}Table', {--}}
{{--                        cols: [data.head],--}}
{{--                        data: data.records,--}}
{{--                        limit: data.pageSize,--}}
{{--                    });--}}
                }
            });
            lister.realtime = {
                url: {
                    add: '{{$urlAdd}}',
                    edit: '{{$urlEdit}}',
                    delete: '{{$urlDelete}}',
                    show: '{{$urlShow}}',
                    export: '{{$urlExport}}',
                    sort: '{{$urlSort}}',
                },
                dialog: {
                    add: null,
                    addWindow: null,
                    edit: null,
                    editWindow: null,
                }
            };
            @if($canAdd)
                $lister.find('[data-add-button]').on('click', function () {
                    lister.realtime.dialog.add = layer.open({
                        type: 2,
                        title: "{{ empty($titleAdd) ? ($title?$title.' '.L('Add'):L('Add')) : $titleAdd }}",
                        shadeClose: true,
                        shade: 0.8,
                        maxmin: false,
                        scrollbar: false,
                        area: {!! json_encode($addDialogSize) !!},
                        content: lister.realtime.url.add,
                        success: function (layerDom, index) {
                            lister.realtime.dialog.addWindow = $(layerDom).find('iframe').get(0).contentWindow;
                            lister.realtime.dialog.addWindow.addEventListener('modstart:form.submitted', function (e) {
                                if (0 === e.detail.res.code) {
                                    layer.close(lister.realtime.dialog.add);
                                }
                            });
                        },
                        end: function () {
                            lister.refresh();
                        }
                    });
                });
            @endif
            @if($canEdit)
                $lister.find('[data-table]').on('click', '[data-edit]', function () {
                    var id = getId(this);
                    lister.realtime.dialog.edit = layer.open({
                        type: 2,
                        title: "{{ empty($titleEdit) ? ($title?$title.' '.L('Edit'):L('Edit')) : $titleEdit }}",
                        shadeClose: true,
                        shade: 0.8,
                        maxmin: false,
                        scrollbar: false,
                        area: ['95%', '95%'],
                        content: lister.realtime.url.edit + '?_id=' + id,
                        success: function (layerDom, index) {
                            lister.realtime.dialog.editWindow = $(layerDom).find('iframe').get(0).contentWindow;
                            lister.realtime.dialog.editWindow.addEventListener('modstart:form.submitted', function (e) {
                                if (0 === e.detail.res.code) {
                                    layer.close(lister.realtime.dialog.edit);
                                }
                            });
                        },
                        end: function () {
                            lister.refresh();
                        }
                    });
                });
            @endif
            @if($canDelete)
                $lister.find('[data-table]').on('click', '[data-delete]', function () {
                    var id = getId(this);
                    window.api.dialog.confirm("{{L('Confirm Delete ?')}}", function () {
                        window.api.dialog.loadingOn();
                        window.api.base.post(lister.realtime.url.delete, {_id: id}, function (res) {
                            window.api.dialog.loadingOff();
                            window.api.base.defaultFormCallback(res, {
                                success: function (res) {
                                    lister.refresh();
                                }
                            });
                        })
                    });
                });
            @endif
            @if($canShow)
                $lister.find('[data-table]').on('click', '[data-show]', function () {
                    var id = getId(this);
                    lister.realtime.dialog.show = layer.open({
                        type: 2,
                        title: "{{ empty($titleShow) ? ($title?$title.' '.L('Show'):L('Show')) : $titleShow }}",
                        shadeClose: true,
                        shade: 0.8,
                        maxmin: false,
                        scrollbar: false,
                        area: ['95%', '95%'],
                        content: lister.realtime.url.show + '?_id=' + id,
                        success: function (layerDom, index) {
                        },
                        end: function () {
                        }
                    });
                });
            @endif
            {{--@if($canDelete && $canBatchDelete)--}}
            {{--$lister.find('[data-table]').on('click', '[data-batch-delete]', function () {--}}
            {{--    var ids = getCheckedIds();--}}
            {{--    if (!ids.length) {--}}
            {{--        window.api.dialog.tipError("{{L('Please Select Records')}}");--}}
            {{--        return;--}}
            {{--    }--}}
            {{--    window.api.dialog.confirm("{{L('Confirm Delete %d records ?')}}".replace('%d', ids.length), function () {--}}
            {{--        window.api.dialog.loadingOn();--}}
            {{--        window.api.base.post(lister.realtime.url.delete, {_id: ids.join(',')}, function (res) {--}}
            {{--            window.api.dialog.loadingOff();--}}
            {{--            window.api.base.defaultFormCallback(res, {--}}
            {{--                success: function (res) {--}}
            {{--                    lister.refresh();--}}
            {{--                }--}}
            {{--            });--}}
            {{--        })--}}
            {{--    });--}}
            {{--});--}}
            {{--@endif--}}
            {{--@if($canSort)--}}
            {{--$lister.find('[data-table]').on('click', '[data-sort]', function () {--}}
            {{--    var id = getId(this);--}}
            {{--    var direction = $(this).attr('data-sort');--}}
            {{--    window.api.dialog.loadingOn();--}}
            {{--    window.api.base.post(lister.realtime.url.sort, {_id: id, direction: direction}, function (res) {--}}
            {{--        window.api.dialog.loadingOff();--}}
            {{--        window.api.base.defaultFormCallback(res, {--}}
            {{--            success: function (res) {--}}
            {{--                lister.refresh();--}}
            {{--            }--}}
            {{--        });--}}
            {{--    })--}}
            {{--});--}}
            {{--@endif--}}
            {{--$lister.data('lister', lister);--}}
            window.__grids = window.__grids || {
                instances: {},
                get: function (key) {
                    if (typeof key === 'number') {
                        var count = 0;
                        for (var k in window.__grids.instances) {
                            if (count === key) {
                                return window.__grids.instances[k];
                            }
                            count++;
                        }
                    }
                    return window.__grids.instances[key];
                }
            };
            window.__grids.instances['{{$id}}'] = {
                $lister: $lister,
                lister: lister //,
                // getCheckedIds: getCheckedIds,
                // getCheckedItems: getCheckedItems,
                // getId: getId
            };
        });
{{--        @if($canBatchSelect || $canSingleSelectItem || $canMultiSelectItem)--}}
{{--            setTimeout(function () {--}}
{{--                $('body > .ub-panel-dialog .panel-dialog-foot [data-submit]').show().on('click',function(){--}}
{{--                    var ids = window.__grids.instances['{{$id}}'].getCheckedIds();--}}
{{--                    var items = window.__grids.instances['{{$id}}'].getCheckedItems();--}}
{{--                    // console.log('itemSelected',ids, items);--}}
{{--                    window.parent.__dialogSelectIds = ids;--}}
{{--                    window.parent.__dialogSelectItems = items;--}}
{{--                    parent.layer.closeAll();--}}
{{--                });--}}
{{--            },0);--}}
{{--        @endif--}}
    })();
</script>
