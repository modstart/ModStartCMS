var GridManager = function (opt) {
    var option = $.extend({
        id: '',
        canBatchSelect: false,
        canSingleSelectItem: false,
        canMultiSelectItem: false,
        title: null,
        titleAdd: null,
        titleEdit: null,
        titleShow: null,
        ttileImport: null,
        canAdd: false,
        canEdit: false,
        canDelete: false,
        canShow: false,
        canBatchDelete: false,
        canSort: false,
        canExport: false,
        canImport: false,
        urlGrid: null,
        urlAdd: null,
        urlEdit: null,
        urlDelete: null,
        urlShow: null,
        urlExport: null,
        urlImport: null,
        urlSort: null,
        batchOperatePrepend: '',
        gridToolbar: '',
        defaultPageSize: 10,
        gridBeforeRequestScript: null,
        pageSizes: [],
        addDialogSize: ['90%', '90%'],
        editDialogSize: ['90%', '90%'],
        showDialogSize: ['90%', '90%'],
        importDialogSize: ['90%', '90%'],
        pageJumpEnable: false,
        lang: {
            loading: 'Loading',
            noRecords: 'No Records',
            add: 'Add',
            edit: 'Edit',
            show: 'Show',
            import: 'Import',
            confirmDelete: 'Confirm Delete ?',
            pleaseSelectRecords: 'Please Select Records',
            confirmDeleteRecords: 'Confirm Delete %d records ?',
        }
    }, opt);

    var $grid = $('#' + option.id);

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
        var data = layui.table.checkStatus(option.id + 'Table').data;
        var ids = [];
        for (var i = 0; i < data.length; i++) {
            ids.push(data[i]._id);
        }
        return ids;
    };
    var getCheckedItems = function () {
        var data = layui.table.checkStatus(option.id + 'Table').data;
        var items = [];
        for (var i = 0; i < data.length; i++) {
            items.push(data[i]);
        }
        return items;
    };

    layui.use(['table', 'laypage'], function () {

        var tableOption = {
            id: option.id + 'Table',
            elem: '#' + option.id + 'Table',
            defaultToolbar: option.gridToolbar,
            page: false,
            skin: 'line',
            text: {
                none: '<div class="ub-text-muted tw-py-4"><i class="iconfont icon-refresh tw-animate-spin tw-inline-block" style="font-size:2rem;"></i><br />'
                    + option.lang.loading + '</div>'
            },
            escape: false,
            // size: 'lg',
            loading: true,
            cellMinWidth: 100,
            cols: [[]],
            data: [],
            autoColumnWidth: true,
            autoScrollTop: false,
            autoSort: false,
            done: function () {
            }
        };
        if (option.canMultiSelectItem && (option.batchOperatePrepend || (option.canDelete && option.canBatchDelete))) {
            tableOption.toolbar = '#' + option.id + 'TableHeadToolbar';
        }

        var table = layui.table.render(tableOption);

        layui.table.on('sort(' + option.id + 'Table)', function (obj) {
            if (null == obj.type) {
                lister.setParam('order', []);
            } else {
                lister.setParam('order', [[obj.field, obj.type]]);
            }
            lister.setPage(1);
            lister.load();
        })

        var isFirst = true;
        var $lister = $('#' + option.id);
        var first = true;
        var lister = new window.api.lister(
            {
                search: $lister.find('[data-search]'),
                table: $lister.find('[data-table]')
            },
            {
                hashUrl: false,
                server: option.urlGrid,
                showLoading: false,
                param: {
                    pageSize: option.defaultPageSize,
                },
                customLoading: function (loading) {
                    if (option.gridBeforeRequestScript) {
                        eval(option.gridBeforeRequestScript);
                    }
                    if (first) {
                        first = false;
                        return;
                    }
                    table.loading(loading);
                },
                render: function (data) {
                    listerData = data;
                    if (option.canSingleSelectItem) {
                        data.head.splice(0, 0, {type: 'radio'});
                    } else if (option.canMultiSelectItem) {
                        data.head.splice(0, 0, {type: 'checkbox'});
                    }
                    $grid.find('[data-addition]').html(data.addition || '');
                    layui.table.reload(option.id + 'Table', {
                        text: {
                            none: '<div class="ub-text-muted"><i class="iconfont icon-empty-box" style="font-size:2rem;"></i><br />' + option.lang.noRecords + '</div>'
                        },
                        cols: [data.head],
                        data: data.records,
                        limit: data.pageSize,
                    });
                    var pageLayout = ['limit', 'prev', 'page', 'next', 'count'];
                    if (option.pageJumpEnable) {
                        pageLayout.push('skip');
                    }
                    layui.laypage.render({
                        elem: option.id + 'Pager',
                        curr: data.page,
                        count: data.total,
                        limit: data.pageSize,
                        limits: option.pageSizes,
                        layout: pageLayout,
                        jump: function (obj, first) {
                            if (!first) {
                                lister.setPage(obj.curr);
                                lister.setPageSize(obj.limit);
                                lister.load();
                            }
                        }
                    });
                    if (data.script) {
                        eval(data.script);
                    }
                }
            });

        lister.realtime = {
            url: {
                add: option.urlAdd,
                edit: option.urlEdit,
                delete: option.urlDelete,
                show: option.urlShow,
                export: option.urlExport,
                import: option.urlImport,
                sort: option.urlSort,
            },
            dialog: {
                add: null,
                addWindow: null,
                edit: null,
                editWindow: null,
                import: null
            }
        };

        if (option.canAdd) {
            $grid.on('click', '[data-add-button]', function () {
                var addUrl = lister.realtime.url.add;
                if ($(this).is('[data-add-copy-button]')) {
                    var id = getId(this);
                    addUrl += (addUrl.indexOf('?') > 0 ? '&' : '?') + '_copyId=' + id;
                }
                // console.log(addUrl);
                lister.realtime.dialog.add = layer.open({
                    type: 2,
                    title: option.titleAdd ? option.titleAdd : (option.title ? option.lang.add + option.title : option.lang.add),
                    shadeClose: false,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: processArea(option.addDialogSize),
                    content: addUrl,
                    success: function (layerDom, index) {
                        lister.realtime.dialog.addWindow = $(layerDom).find('iframe').get(0).contentWindow;
                        lister.realtime.dialog.addWindow.__dialogClose = function () {
                            layer.close(lister.realtime.dialog.add);
                        };
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
        }

        function doEdit($item) {
            var id = getId($item);
            lister.realtime.dialog.edit = layer.open({
                type: 2,
                title: option.titleEdit ? option.titleEdit : (option.title ? option.lang.edit + option.title : option.lang.edit),
                shadeClose: false,
                shade: 0.5,
                maxmin: false,
                scrollbar: false,
                area: processArea(option.editDialogSize),
                content: lister.realtime.url.edit + (lister.realtime.url.edit && lister.realtime.url.edit.indexOf('?') >= 0 ? '&' : '?') + '_id=' + id,
                success: function (layerDom, index) {
                    lister.realtime.dialog.editWindow = $(layerDom).find('iframe').get(0).contentWindow;
                    lister.realtime.dialog.editWindow.__dialogClose = function () {
                        layer.close(lister.realtime.dialog.edit);
                    };
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
        }

        if (option.canEdit) {
            $lister.on('click', '[data-tab-open][data-refresh-grid-on-close]', function () {
                window._pageTabManager.runsOnFocus.push(function () {
                    lister.refresh();
                });
            });
            $lister.find('[data-table]').on('click', '[data-edit]', function () {
                doEdit(this);
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
        }

        function doDelete($item) {
            var id = getId($item);
            window.api.dialog.confirm(option.lang.confirmDelete, function () {
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
        };
        if (option.canDelete) {
            $lister.find('[data-table]').on('click', '[data-delete]', function () {
                doDelete(this);
            });
        }

        function doShow($item) {
            var id = getId($item);
            lister.realtime.dialog.show = layer.open({
                type: 2,
                title: option.titleShow ? option.titleShow : (option.title ? option.lang.show + option.title : option.lang.show),
                shadeClose: false,
                shade: 0.5,
                maxmin: false,
                scrollbar: false,
                area: processArea(option.showDialogSize),
                content: lister.realtime.url.show + (lister.realtime.url.show && lister.realtime.url.show.indexOf('?') >= 0 ? '&' : '?') + '_id=' + id,
                success: function (layerDom, index) {
                },
                end: function () {
                }
            });
        }

        if (option.canShow) {
            $lister.find('[data-table]').on('click', '[data-show]', function () {
                doShow(this);
            });
        }

        // 操作栏浮动时不能响应操作
        $(document).on('click', '.layui-table-tips .layui-layer-content [data-delete], .layui-table-tips .layui-layer-content [data-edit], .layui-table-tips .layui-layer-content [data-show]', function () {
            // console.log('click');
            var $this = $(this), $tip = $(this).closest('.layui-layer-content');
            var tip = $tip.offset();
            tip.width = $tip.width();
            tip.height = $tip.height();
            tip.centerY = tip.top + tip.height / 2;
            var grid = $grid.offset();
            grid.width = $grid.width();
            grid.height = $grid.height();
            // ensure tip position in grid
            if (tip.left < grid.left || tip.left > grid.left + grid.width || tip.top < grid.top || tip.top > grid.top + grid.height) {
                return;
            }
            $grid.find('.layui-table-main [data-index]').each(function (i, o) {
                var $item = $(o);
                var item = $item.offset();
                // console.log('item.check', item.top, $item.height(), tip.centerY);
                if (item.top < tip.centerY && item.top + $item.height() > tip.centerY) {
                    // console.log('item.pass', item.top, $item.height(), tip.centerY);
                    if ($this.is('[data-delete]')) {
                        doDelete($item);
                    } else if ($this.is('[data-show]')) {
                        doShow($item);
                    } else if ($this.is('[data-edit]')) {
                        doEdit($item);
                    }
                }
            })
        });

        if (option.canDelete && option.canBatchDelete) {
            $lister.find('[data-table]').on('click', '[data-batch-delete]', function () {
                var ids = getCheckedIds();
                if (!ids.length) {
                    window.api.dialog.tipError(option.lang.pleaseSelectRecords);
                    return;
                }
                window.api.dialog.confirm(option.lang.confirmDeleteRecords.replace('%d', ids.length), function () {
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
        }

        if (option.canSort) {
            $lister.find('[data-table]').on('click', '[data-sort]', function () {
                var id = getId(this);
                var direction = $(this).attr('data-sort');
                window.api.dialog.loadingOn();
                window.api.base.post(lister.realtime.url.sort, {
                    _id: id,
                    direction: direction,
                    param: JSON.stringify(lister.getParam()),
                }, function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res, {
                        success: function (res) {
                            lister.refresh();
                        }
                    });
                })
            });
        }

        if (option.canExport) {
            $lister.find('[data-export-button]').on('click', function () {
                lister.prepareSearch();
                var param = JSON.stringify(lister.getParam());
                var url = lister.realtime.url.export + '?_param=' + MS.util.urlencode(param);
                window.open(url, '_blank');
            });
        }

        if (option.canImport) {
            $lister.find('[data-import-button]').on('click', function () {
                lister.realtime.dialog.import = layer.open({
                    type: 2,
                    title: option.titleImport ? option.titleImport : (option.title ? option.lang.import + option.title : option.lang.import),
                    shadeClose: false,
                    shade: 0.5,
                    maxmin: false,
                    scrollbar: false,
                    area: processArea(option.importDialogSize),
                    content: lister.realtime.url.import,
                    success: function (layerDom, index) {
                    },
                    end: function () {
                    }
                });
            });
        }

        $lister.find('[data-table]').on('click', '[data-batch-operate]', function () {
            var ids = getCheckedIds();
            var url = $(this).attr('data-batch-operate');
            if (!ids.length) {
                window.api.dialog.tipError(option.lang.pleaseSelectRecords);
                return;
            }
            var callback = function () {
                window.api.dialog.loadingOn();
                window.api.base.post(url, {_id: ids.join(',')}, function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res, {
                        success: function (res) {
                            lister.refresh();
                        }
                    });
                });
            };
            if ($(this).attr('data-batch-confirm')) {
                window.api.dialog.confirm($(this).attr('data-batch-confirm').replace('%d', ids.length), function () {
                    callback();
                });
            } else {
                callback();
            }
        });
        $lister.find('[data-table]').on('click', '[data-batch-dialog-operate]', function () {
            var ids = getCheckedIds();
            var url = $(this).attr('data-batch-dialog-operate');
            if (!ids.length) {
                window.api.dialog.tipError(option.lang.pleaseSelectRecords);
                return;
            }
            MS.dialog.dialog(url + '?_id=' + ids.join(','));
        });
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
        window.__grids.instances[option.id] = {
            $lister: $lister,
            lister: lister,
            getCheckedIds: getCheckedIds,
            getCheckedItems: getCheckedItems,
            getId: getId
        };
    });

    if (option.canBatchSelect || option.canSingleSelectItem || option.canMultiSelectItem) {
        $(function () {
            setTimeout(function () {
                if (window.__dialogFootSubmiting) {
                    window.__dialogFootSubmiting(function () {
                        var ids = window.__grids.instances[option.id].getCheckedIds();
                        var items = window.__grids.instances[option.id].getCheckedItems();
                        // console.log('itemSelected',ids, items);
                        window.parent.__dialogSelectIds = ids;
                        window.parent.__selectorDialogItems = items;
                        parent.layer.closeAll();
                    });
                }
            }, 0);
        })
    }

};

MS.GridManager = GridManager;
