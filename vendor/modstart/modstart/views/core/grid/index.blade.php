<div id="{{$id}}" data-grid data-basic-lister class="ub-lister-table-container">
    @if(!empty($scopes))
        <div class="tw-pb-3">
            <div class="ub-nav-tab">
                @foreach($scopes as $scope)
                    <a class="{{$scopeCurrent==$scope['name']?'active':''}}" href="?{{\ModStart\Core\Input\Request::mergeQueries(['_scope'=>$scope['name']])}}">
                        {{$scope['title']}}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
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
            {!! $gridOperateAppend !!}
    </div>
    <div data-search class="ub-lister-search">
        @foreach($filters as $filter)
            {!! $filter->render() !!}
        @endforeach
        <div class="field">
            @if(!count($filters))
                <button class="btn btn-primary" data-search-button>
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
    <div data-addition class="table-addition-container"></div>
    <div data-table class="table-container">
        <table class="table-container" id="{{$id}}Table" lay-filter="{{$id}}Table"></table>
    </div>
    @if($enablePagination)
        <div class="page-container tw-px-2" id="{{$id}}Pager"></div>
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
        var processArea = function(area) {
            if(/^(\d+)px$/.test(area[0])){
                area[0] = Math.min($(window).width(),parseInt(area[0]))+'px';
            }
            if(/^(\d+)px$/.test(area[1])){
                area[1] = Math.min($(window).height(),parseInt(area[1]))+'px';
            }
            return area;
        };
        var getId = function (o) {
            var index = parseInt($(o).closest('[data-index]').attr('data-index'));
            return listerData.records[index]._id;
        };
        var getCheckedIds = function () {
            var data = layui.table.checkStatus('{{$id}}Table').data;
            var ids = [];
            for (var i = 0; i < data.length; i++) {
                ids.push(data[i]._id);
            }
            return ids;
        };
        var getCheckedItems = function () {
            var data = layui.table.checkStatus('{{$id}}Table').data;
            var items = [];
            for (var i = 0; i < data.length; i++) {
                items.push(data[i]);
            }
            return items;
        };
        layui.extend({
            mstable: window.__msCDN + 'asset/layui/lay/ext/mstable.js?v20220119'
        });
        layui.use(['table', 'laypage','mstable'], function () {
            var table = layui.table.render({
                id: '{{$id}}Table',
                elem: '#{{$id}}Table',
                @if($canMultiSelectItem && ( $batchOperatePrepend || ($canDelete && $canBatchDelete)  ))
                    toolbar: '#{{$id}}TableHeadToolbar',
                @endif
                defaultToolbar: [],
                page: false,
                skin: 'line',
                text: {
                    none: '{{L('No Records')}}'
                },
                // size: 'sm',
                loading: true,
                cellMinWidth: 100,
                cols: [[]],
                data: [],
                done: function() {
                    layui.mstable.render(this);
                }
            });
            layui.table.on('sort({{$id}}Table)', function (obj) {
                if (null == obj.type) {
                    lister.setParam('order', []);
                } else {
                    lister.setParam('order', [[obj.field, obj.type]]);
                }
                lister.setPage(1);
                lister.load();
            })
            var $lister = $('#{{$id}}');
            var lister = new window.api.lister({
                search: $lister.find('[data-search]'),
                table: $lister.find('[data-table]')
            }, {
                hashUrl: false,
                server: window.location.href,
                render: function (data) {
                    listerData = data;
                    @if($canSingleSelectItem)
                        data.head.splice(0, 0, {type: 'radio'});
                    @elseif($canMultiSelectItem)
                        data.head.splice(0, 0, {type: 'checkbox'});
                    @endif
                    $grid.find('[data-addition]').html(data.addition || '');
                    layui.table.reload('{{$id}}Table', {
                        cols: [data.head],
                        data: data.records,
                        limit: data.pageSize,
                    });
                    layui.laypage.render({
                        elem: '{{$id}}Pager',
                        curr: data.page,
                        count: data.total,
                        limit: data.pageSize,
                        limits: [10,20,50,100],
                        layout: [ 'limit', 'prev', 'page', 'next','count',],
                        jump: function (obj, first) {
                            if (!first) {
                                lister.setPage(obj.curr);
                                lister.setPageSize(obj.limit);
                                lister.load();
                            }
                        }
                    });
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
                    title: "{{ $titleAdd or ($title?L('Add').$title:L('Add')) }}",
                    shadeClose: true,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: processArea( {!! json_encode($addDialogSize) !!} ),
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
                    title: "{{ $titleEdit or ($title?L('Edit').$title:L('Edit')) }}",
                    shadeClose: true,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: processArea( {!! json_encode($editDialogSize) !!} ),
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
            $lister.find('[data-table]').on('click', '[data-edit-quick]', function () {
                var pcs = $(this).attr('data-edit-quick').split(':');
                var column = pcs.shift();
                var value = pcs.join(':');
                var post = {
                    _id: getId(this),
                    _action: 'itemCellEdit',
                    column: column,
                    value: value
                };
                window.api.dialog.loadingOn();
                window.api.base.post(lister.realtime.url.edit, post, function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res, {
                        success: function (res) {
                        }
                    });
                    lister.refresh();
                });
            });
            $grid.on('grid-item-cell-change', function (e, data) {
                var post = {
                    _id: getId(data.ele),
                    _action: 'itemCellEdit',
                    column: data.column,
                    value: data.value
                };
                window.api.dialog.loadingOn();
                window.api.base.post(lister.realtime.url.edit, post, function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res, {
                        success: function (res) {
                        }
                    });
                    lister.refresh();
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
                    title: "{{ $titleShow or ($title?L('Show').$title:L('Show')) }}",
                    shadeClose: true,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: {!! json_encode($showDialogSize) !!},
                    content: lister.realtime.url.show + '?_id=' + id,
                    success: function (layerDom, index) {
                    },
                    end: function () {
                    }
                });
            });
            @endif
            @if($canDelete && $canBatchDelete)
            $lister.find('[data-table]').on('click', '[data-batch-delete]', function () {
                var ids = getCheckedIds();
                if (!ids.length) {
                    window.api.dialog.tipError("{{L('Please Select Records')}}");
                    return;
                }
                window.api.dialog.confirm("{{L('Confirm Delete %d records ?')}}".replace('%d', ids.length), function () {
                    window.api.dialog.loadingOn();
                    window.api.base.post(lister.realtime.url.delete, {_id: ids.join(',')}, function (res) {
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
            @if($canSort)
            $lister.find('[data-table]').on('click', '[data-sort]', function () {
                var id = getId(this);
                var direction = $(this).attr('data-sort');
                window.api.dialog.loadingOn();
                window.api.base.post(lister.realtime.url.sort, {_id: id, direction: direction}, function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res, {
                        success: function (res) {
                            lister.refresh();
                        }
                    });
                })
            });
            @endif
            $lister.data('lister', lister);
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
                lister: lister,
                getCheckedIds: getCheckedIds,
                getCheckedItems: getCheckedItems,
                getId: getId
            };
        });
        @if($canBatchSelect || $canSingleSelectItem || $canMultiSelectItem)
            setTimeout(function () {
                $('body > .ub-panel-dialog .panel-dialog-foot [data-submit]').show().on('click',function(){
                    var ids = window.__grids.instances['{{$id}}'].getCheckedIds();
                    var items = window.__grids.instances['{{$id}}'].getCheckedItems();
                    // console.log('itemSelected',ids, items);
                    window.parent.__dialogSelectIds = ids;
                    window.parent.__dialogSelectItems = items;
                    parent.layer.closeAll();
                });
            },0);
        @endif
    })();
</script>
