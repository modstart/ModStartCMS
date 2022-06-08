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
                <a href="{{$urlAdd}}" class="btn btn-primary">
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
        var processArea = function (area) {
            if (/^(\d+)px$/.test(area[0])) {
                area[0] = Math.min($(window).width(), parseInt(area[0])) + 'px';
            }
            if (/^(\d+)px$/.test(area[1])) {
                area[1] = Math.min($(window).height(), parseInt(area[1])) + 'px';
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
        layui.use(['table', 'laypage'], function () {
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
                    none: '<div class="ub-text-muted tw-py-4"><i class="iconfont icon-refresh tw-animate-spin tw-inline-block" style="font-size:2rem;"></i><br />{{L('Loading')}}</div>'
                },
                // size: 'sm',
                loading: true,
                cellMinWidth: 100,
                cols: [[]],
                data: [],
                autoColumnWidth: true,
                autoScrollTop: false,
                autoSort: false,
                done: function () {
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
            var isFirst = true;
            var $lister = $('#{{$id}}');
            var first = true;
            var lister = new window.api.lister({
                search: $lister.find('[data-search]'),
                table: $lister.find('[data-table]')
            }, {
                hashUrl: false,
                server: window.location.href,
                showLoading: false,
                param: {
                    pageSize: {!! $defaultPageSize !!}
                },
                customLoading: function(loading){
                    @if(!empty($gridBeforeRequestScript))
                        {!! $gridBeforeRequestScript !!};
                    @endif
                    if(first){
                        first = false;
                        return;
                    }
                    table.loading(loading);
                },
                render: function (data) {
                    listerData = data;
                    @if($canSingleSelectItem)
                    data.head.splice(0, 0, {type: 'radio'});
                    @elseif($canMultiSelectItem)
                    data.head.splice(0, 0, {type: 'checkbox'});
                    @endif
                    $grid.find('[data-addition]').html(data.addition || '');
                    layui.table.reload('{{$id}}Table', {
                        text: {
                            none: '<div class="ub-text-muted"><i class="iconfont icon-empty-box" style="font-size:2rem;"></i><br />{{L('No Records')}}</div>'
                        },
                        cols: [data.head],
                        data: data.records,
                        limit: data.pageSize,
                    });
                    layui.laypage.render({
                        elem: '{{$id}}Pager',
                        curr: data.page,
                        count: data.total,
                        limit: data.pageSize,
                        limits: {!! json_encode($pageSizes) !!},
                        layout: ['limit', 'prev', 'page', 'next', 'count',],
                        jump: function (obj, first) {
                            if (!first) {
                                lister.setPage(obj.curr);
                                lister.setPageSize(obj.limit);
                                lister.load();
                            }
                        }
                    });
                    if(data.script){
                        eval(data.script);
                    }
                }
            });
            lister.realtime = {
                url: {
                    add: '{{$urlAdd}}',
                    edit: '{{$urlEdit}}',
                    delete: '{{$urlDelete}}',
                    show: '{{$urlShow}}',
                    export: '{{$urlExport}}',
                    import: '{{$urlImport}}',
                    sort: '{{$urlSort}}',
                },
                dialog: {
                    add: null,
                    addWindow: null,
                    edit: null,
                    editWindow: null,
                    import: null
                }
            };
            @if($canAdd)
            $lister.find('[data-add-button]').on('click', function () {
                lister.realtime.dialog.add = layer.open({
                    type: 2,
                    title: "{{ empty($titleAdd) ? ($title?L('Add').$title:L('Add')) : $titleAdd }}",
                    shadeClose: false,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: processArea({!! json_encode($addDialogSize) !!}),
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
                    title: "{{ empty($titleEdit) ? ($title?L('Edit').$title:L('Edit')) : $titleEdit }}",
                    shadeClose: false,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: processArea({!! json_encode($editDialogSize) !!}),
                    content: lister.realtime.url.edit + (lister.realtime.url.edit && lister.realtime.url.edit.indexOf('?') >= 0 ? '&' : '?') + '_id=' + id,
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
                    title: "{{ empty($titleShow) ? ($title?L('Show').$title:L('Show')) : $titleShow }}",
                    shadeClose: false,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: {!! json_encode($showDialogSize) !!},
                    content: lister.realtime.url.show + (lister.realtime.url.show && lister.realtime.url.show.indexOf('?') >= 0 ? '&' : '?') + '_id=' + id,
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
            @if($canExport)
            $lister.find('[data-export-button]').on('click', function () {
                lister.prepareSearch();
                var param = JSON.stringify(lister.getParam());
                var url = lister.realtime.url.export + '?_param=' + MS.util.urlencode(param);
                window.open(url, '_blank');
            });
            @endif
            @if($canImport)
            $lister.find('[data-import-button]').on('click', function () {
                lister.realtime.dialog.import = layer.open({
                    type: 2,
                    title: "{{ empty($titleImport) ? ($title?L('Import').$title:L('Import')) : $titleImport }}",
                    shadeClose: false,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: {!! json_encode($importDialogSize) !!},
                    content: lister.realtime.url.import,
                    success: function (layerDom, index) {
                    },
                    end: function () {
                    }
                });
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
        $(function(){
            setTimeout(function () {
                if(window.__dialogFootSubmiting){
                    window.__dialogFootSubmiting(function () {
                        var ids = window.__grids.instances['{{$id}}'].getCheckedIds();
                        var items = window.__grids.instances['{{$id}}'].getCheckedItems();
                        // console.log('itemSelected',ids, items);
                        window.parent.__dialogSelectIds = ids;
                        window.parent.__selectorDialogItems = items;
                        parent.layer.closeAll();
                    });
                }
            }, 0);
        })
        @endif
    })();
</script>
{!! $bodyAppend !!}
