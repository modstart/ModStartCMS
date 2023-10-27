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
        {!! $batchOperatePrepend !!}
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
        layui.use(['laytpl'], function () {
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
                        shadeClose: false,
                        shade: 0.8,
                        maxmin: false,
                        scrollbar: false,
                        area: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($addDialogSize) !!},
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
                        shadeClose: false,
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
                        shadeClose: false,
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
                lister: lister
            };
        });
    })();
</script>
