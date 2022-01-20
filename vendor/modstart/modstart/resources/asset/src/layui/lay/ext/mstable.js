layui.define([], function (exports) {

    var $ = layui.$,
        tables = {},
        _BODY = $('body');

    String.prototype.width = function (font, isText) {
        var html = this;
        isText = isText || false
        var f = font || _BODY.css('font'),
            o = $('<div></div>');
        if (isText) {
            o.text(html)
        } else {
            o.html(html)
        }
        o.css({
            'position': 'absolute',
            'float': 'left',
            'white-space': 'nowrap',
            'visibility': 'hidden',
            'font': f
        }).appendTo(_BODY);
        var w = o.width();
        o.remove();
        return w;
    }

    var mod = {
        render: function (myTable) {
            tables[myTable.id] = myTable

            var _this = this;
            layui.each(tables, function () {
                innerColumnWidth(_this, this)
            });

            function innerColumnWidth(_this, myTable) {
                var $table = $(myTable.elem),
                    th = $table.next().children('.layui-table-box').children('.layui-table-header').children('table').children('thead').children('tr').children('th'),
                    fixTh = $table.next().children('.layui-table-box').children('.layui-table-fixed').children('.layui-table-header').children('table').children('thead').children('tr').children('th'),
                    $tableBodytr = $table.next().children('.layui-table-box').children('.layui-table-body').children('table').children('tbody').children('tr'),
                    $totalTr = $table.next().children('.layui-table-total').find('tr');
                th.add(fixTh).on('dblclick', function (e) {
                    var othis = $(this),
                        pLeft = e.clientX - othis.offset().left;
                    var colKey = othis.attr('data-key').split('-')
                    var config = myTable.cols[colKey[1]][colKey[2]];
                    computeColumnWidth(myTable, othis, othis.parents('.layui-table-fixed-r').length > 0 ? pLeft <= 10 : othis.width() - pLeft <= 10, config);
                    setColumnWidth(myTable, othis, othis.parents('.layui-table-fixed-r').length > 0 ? pLeft <= 10 : othis.width() - pLeft <= 10, config);
                });
                // 初始化表格后，自动调整所有列宽
                var widthLeft = 0;
                var widthAutoJustCount = 0;
                th.add(fixTh).each(function (e, i) {
                    var colKey = $(this).attr('data-key').split('-')
                    var config = myTable.cols[colKey[1]][colKey[2]];
                    if (config.withAuto === true) {
                        computeColumnWidth(myTable, $(this), true, config);
                        if (('widthAutoSize' in config) && ('widthAutoOld' in config)) {
                            widthLeft += config.widthAutoOld;
                            widthLeft -= config.widthAutoSize;
                            widthAutoJustCount++;
                        }
                    }
                });
                if (widthLeft > 0 && widthAutoJustCount > 0) {
                    th.add(fixTh).each(function (e, i) {
                        var colKey = $(this).attr('data-key').split('-');
                        var config = myTable.cols[colKey[1]][colKey[2]];
                        if (config.withAuto === true) {
                            config.width = config.widthAutoSize + widthLeft / widthAutoJustCount;
                        }
                    });
                }
                th.add(fixTh).each(function (e, i) {
                    var colKey = $(this).attr('data-key').split('-')
                    var config = myTable.cols[colKey[1]][colKey[2]];
                    if (config.withAuto === true) {
                        setColumnWidth(myTable, $(this), true, config);
                    }
                });

                function setColumnWidth(myTable, othis, isHandle, config) {
                    var key = othis.data('key')
                        , keyArray = key.split('-')
                        , curKey = keyArray.length === 3 ? keyArray[1] + '-' + keyArray[2] : ''
                    if (othis.attr('colspan') > 1 || othis.data('unresize')) {
                        return;
                    }
                    if (isHandle) {
                        _this.getCssRule(myTable, key, function (item) {
                            item.style.width = config.width + 'px'
                        });
                    }
                }

                function computeColumnWidth(myTable, othis, isHandle, config) {
                    var key = othis.data('key')
                        , keyArray = key.split('-')
                        , curKey = keyArray.length === 3 ? keyArray[1] + '-' + keyArray[2] : ''
                    if (othis.attr('colspan') > 1 || othis.data('unresize')) {
                        return;
                    }
                    if (isHandle) {
                        config.widthAutoOld = othis.width();
                        var maxWidth = othis.text().width(othis.css('font')) + 21, font = othis.css('font');
                        $tableBodytr.children('td[data-key="' + key + '"]').each(function (index, elem) {
                            var curWidth = 0
                            if ($(this).children().children() && $(this).children().children().length > 0) {
                                curWidth += $(this).children().html().width(font)
                            } else {
                                curWidth = $(this).text().width(font, true);
                            }
                            if (maxWidth < curWidth) {
                                maxWidth = curWidth
                            }
                        })
                        if ($totalTr.length > 0) {
                            var curWidth = $totalTr.children('td[data-key="' + key + '"]').text().width(font)
                            if (maxWidth < curWidth) {
                                maxWidth = curWidth
                            }
                        }
                        maxWidth += 32;
                        maxWidth = Math.min(maxWidth, 500);
                        config.width = maxWidth;
                        config.widthAutoSize = maxWidth;
                    }
                }
            }

        },
        getCssRule: function (that, key, callback) {
            var style = that.elem.next().find('style')[0]
                , sheet = style.sheet || style.styleSheet || {}
                , rules = sheet.cssRules || sheet.rules;
            layui.each(rules, function (i, item) {
                if (item.selectorText === ('.laytable-cell-' + key)) {
                    return callback(item), true;
                }
            });
        }
    };
    exports('mstable', mod);
});