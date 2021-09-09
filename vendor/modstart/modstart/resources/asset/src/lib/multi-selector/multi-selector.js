/**
 * html multi selector
 *
 * version : beta
 * author  : server
 *
 * Licensed under MIT
 */

;(function () {

    var globalIndex = 0;

    var MultiSelector = function (option) {

        if (typeof $ == "undefined") {
            alert("MultiSelector require jQuery");
            return;
        }

        var defaultOption = {
            container: null,
            seperator: ",",
            dynamic: false,
            server: "/path/to/data",
            data: [],
            maxLevel: 0,
            fixedLevel: 0,
            lang: {
                loading: '正在加载...',
                pleaseSelect: '请选择'
            },
            onChange: function (values, titles) {

            },

            // build in params do not modify
            selectorValue: "[data-value]",
            selectorTitle: "[data-title]",
            selectorSelect: "[data-select]",
            valueKey: "id",
            parentValueKey: "pid",
            titleKey: "title",
            sortKey: "sort",
            rootParentValue: 0,
            serverMethod: "get",
            serverDataType: "json",
            serverResponseHandle: function (res) {
                if (typeof res !== 'object') {
                    alert("ErrorResponse:" + res);
                    return [];
                }
                if (!("code" in res) || !("data" in res)) {
                    alert("ErrorResponseObject:" + res.toString());
                    return [];
                }
                if (res.code != 0) {
                    alert("ErrorResponseCode:" + res.code);
                    return [];
                }
                return res.data;
            }
        };

        this.opt = $.extend(defaultOption, option);

        var me = this;
        var $container = $(this.opt.container);
        var $selectContainer = $container.find(this.opt.selectorSelect);

        this.value = null;
        this.title = null;

        this.server = null;
        this.seperator = ',';
        this.maxLevel = 0;
        this.data = [];

        this.initValues = [];
        this.initTitles = [];

        var init = function () {

            initValues();

            $selectContainer.html(me.opt.lang.loading);

            if (me.opt.dynamic && me.initValues.length) {
                me.val(me.initValues);
            } else if (me.opt.dynamic && me.initTitles.length) {
                me.titleVal(me.initTitles);
            } else if (me.opt.dynamic) {
                sendAsyncRequest(me.opt.rootParentValue, function (data) {
                    me.data = data;
                    $selectContainer.html('');
                    render(1, me.opt.rootParentValue);
                    if (me.initValues.length) {
                        me.val(me.initValues);
                    } else if (me.initTitles.length) {
                        me.titleVal(me.initTitles);
                    }
                });
            } else {
                me.data = dataConvert(me.opt.data);
                $selectContainer.html('');
                if (me.initValues.length) {
                    me.val(me.initValues);
                } else if (me.initTitles.length) {
                    me.titleVal(me.initTitles);
                } else {
                    render(1, me.opt.rootParentValue);
                }
            }

        };

        var sendAsyncRequest = function (parentValue, callback, title) {
            title = title || null;
            var data = {};
            var sortKey = me.opt.sortKey;
            data[me.opt.parentValueKey] = parentValue;
            data[me.opt.titleKey] = title;
            $.ajax({
                type: me.opt.serverMethod,
                url: me.opt.server,
                dataType: me.opt.serverDataType,
                timeout: 30000,
                data: data,
                success: function (res) {
                    var data = me.opt.serverResponseHandle(res);
                    data.sort(function (x, y) {
                        return x[sortKey] - y[sortKey];
                    });
                    var options = dataConvert(res.data);
                    callback(options);
                },
                error: function () {
                    alert('请求出现错误 T_T');
                }
            });
        };

        var dataConvert = function (data) {
            var options = [];
            for (var i = 0; i < data.length; i++) {
                options.push({
                    parentValue: data[i][me.opt.parentValueKey],
                    value: data[i][me.opt.valueKey],
                    title: data[i][me.opt.titleKey]
                });
            }
            return options;
        };

        var initValues = function () {
            var initValues = $container.find(me.opt.selectorValue).val();
            var initTitles = $container.find(me.opt.selectorTitle).val();
            if (initValues) {
                var values = initValues.split(me.seperator);
                for (var i = 0; i < values.length; i++) {
                    if (values[i]) {
                        me.initValues.push(values[i]);
                    }
                }
            } else if (initTitles) {
                var titles = initTitles.split(me.seperator);
                for (var i = 0; i < titles.length; i++) {
                    if (titles[i]) {
                        me.initTitles.push(titles[i]);
                    }
                }
            }
        };

        var render = function (level, pid) {
            var options = [];
            var data = me.data;
            for (var i = 0; i < data.length; i++) {
                if (data[i].parentValue == pid) {
                    options.push({value: data[i].value, title: data[i].title});
                }
            }
            $selectContainer.find('select').each(function (i, o) {
                var lev = parseInt($(o).attr('data-level'));
                if (lev >= level) {
                    $(o).remove();
                }
            });
            if (!options.length) {
                return;
            }
            var html = [];
            html.push('<select data-level="' + level + '" class="select">');
            html.push('<option value="">' + me.opt.lang.pleaseSelect + '</option>');
            for (var i = 0; i < options.length; i++) {
                html.push('<option value="' + options[i].value + '">' + options[i].title + '</option>');
            }
            html.push('</select>');
            $selectContainer.append(html.join(''));
            $container.trigger('widget.category.rendered', [me]);
        };

        var renderClear = function (level) {
            $selectContainer.find('select').each(function (i, o) {
                var lev = parseInt($(o).attr('data-level'));
                if (lev >= level) {
                    $(o).remove();
                }
            });
        };

        var refreshVal = function () {
            $container.find(me.opt.selectorValue).val('');
            $container.find(me.opt.selectorTitle).val('');
            var values = [];
            var titles = [];
            $selectContainer.find('select').each(function (i, o) {
                var val = $(o).val();
                if (!val) {
                    return;
                }
                values.push(val);
                titles.push($(o).find('option:selected').text());
            });
            $container.find(me.opt.selectorValue).each(function (i, o) {
                var lev = $(o).attr('data-level');
                if (lev) {
                    lev = parseInt(lev);
                    if (lev <= values.length) {
                        $(o).val(values[lev - 1]);
                    }
                } else {
                    $(o).val(values.join(me.seperator));
                }
            });
            $container.find(me.opt.selectorTitle).each(function (i, o) {
                var lev = $(o).attr('data-level');
                if (lev) {
                    lev = parseInt(lev);
                    if (lev <= titles.length) {
                        $(o).val(titles[lev - 1]);
                    }
                } else {
                    $(o).val(titles.join(me.seperator));
                }
            });
            me.value = values;
            me.title = titles;
            $container.trigger('widget.category.change', [me]);
            me.opt.onChange(values, titles);
        };

        this.val = function (value) {
            if (undefined == value) {
                return me.value;
            }
            me.initValues = value;

            if (!me.initValues.length) {
                renderClear(1);
                return;
            }

            if (me.opt.dynamic) {

                sendAsyncRequest(me.initValues.join(','), function (data) {
                    me.data = data;

                    $selectContainer.html('');
                    render(1, 0);
                    for (var i = 0; i < me.initValues.length - 1; i++) {
                        var lev = i + 2;
                        var val = me.initValues[i];
                        if (me.maxLevel == 0 || lev <= me.maxLevel) {
                            render(lev, val);
                        }
                    }

                    $selectContainer.find('select').each(function (i, o) {
                        var lev = $(o).attr('data-level');
                        if (!lev) {
                            return;
                        }
                        lev = parseInt(lev);
                        if (lev <= me.initValues.length) {
                            $(o).val(me.initValues[lev - 1]);
                        }
                    });

                    me.initValues = [];

                    refreshVal();

                });

            } else {

                render(1, me.opt.rootParentValue);

                for (var i = 0; i < me.initValues.length; i++) {
                    var lev = i + 1;
                    var val = me.initValues[i];
                    var $selects = $selectContainer.find('select');
                    if ($selects.length >= lev) {
                        var $sel = $($selects.get(lev - 1));
                        $sel.val(val).trigger('change');
                    }
                }
                me.initValues = [];
            }
        };

        this.titleVal = function (title) {
            if (undefined == title) {
                return me.title;
            }
            me.initTitles = title;

            if (!me.initTitles.length) {
                renderClear(1);
                return;
            }

            if (me.opt.dynamic) {

                sendAsyncRequest(null, function (data) {
                    me.data = data;

                    $selectContainer.html('');
                    render(1, 0);
                    for (var i = 0; i < me.initTitles.length - 1; i++) {
                        var lev = i + 2;
                        var title = me.initTitles[i];
                        var $sel = $selectContainer.find('select[data-level=' + (lev - 1) + ']');
                        var val = null;
                        $sel.find('option').each(function (i, o) {
                            if ($(o).text() == title) {
                                val = $(o).attr('value');
                            }
                        });
                        if (null !== val && (me.maxLevel == 0 || lev <= me.maxLevel)) {
                            render(lev, val);
                        }
                    }

                    $selectContainer.find('select').each(function (i, o) {
                        var lev = $(o).attr('data-level');
                        if (!lev) {
                            return;
                        }
                        lev = parseInt(lev);
                        if (lev <= me.initTitles.length) {
                            var title = me.initTitles[lev - 1];
                            $(o).find('option').each(function (i, o) {
                                if ($(o).text() == title) {
                                    $(o).prop('selected', true);
                                }
                            });
                        }
                    });

                    refreshVal();

                }, me.initTitles.join(','));

            } else {

                render(1, me.opt.rootParentValue);

                for (var i = 0; i < me.initTitles.length; i++) {
                    var lev = i + 1;
                    var tlt = me.initTitles[i];
                    var $selects = $selectContainer.find('select');
                    if ($selects.length >= lev) {
                        var $sel = $($selects.get(lev - 1));
                        $sel.find('option').each(function (i, o) {
                            if ($(o).text() == tlt) {
                                $sel.val($(o).attr('value')).trigger('change');
                            }
                        });
                    }
                }
            }

        };


        $selectContainer.on('change', 'select', function () {
            var level = parseInt($(this).attr('data-level'));
            var id = $(this).val();
            if (me.opt.dynamic) {

                if (0 == me.maxLevel || level < me.maxLevel) {
                    if (id > 0) {

                        sendAsyncRequest(id, function (data) {
                            me.data = data;
                            render(level + 1, id);

                            if (me.initValues && me.initValues.length) {
                                var $selects = $selectContainer.find('select');
                                if (!$selects.length) {
                                    return;
                                }
                                var $sel = $($selects.get($selects.length - 1));
                                $sel.val(me.initValues.splice(0, 1)[0]).trigger('change');
                            } else if (me.initTitles && me.initTitles.length) {
                                var $selects = $selectContainer.find('select');
                                if (!$selects.length) {
                                    return;
                                }
                                var $sel = $($selects.get($selects.length - 1));
                                var tlt = me.initTitles.splice(0, 1)[0];
                                $sel.find('option').each(function (i, o) {
                                    if ($(o).text() == tlt) {
                                        $sel.val($(o).attr('value')).trigger('change');
                                    }
                                });
                            }


                            refreshVal();
                        });

                    } else {
                        renderClear(level + 1);
                        refreshVal();
                    }
                }


            } else {

                if (0 == me.maxLevel || level < me.maxLevel) {
                    if (id > 0) {
                        render(level + 1, id);
                    } else {
                        renderClear(level + 1);
                    }
                    refreshVal();
                }
            }
            return false;
        });

        init();


    };

    if (typeof module !== 'undefined' && typeof exports === 'object' && define.cmd) {
        module.exports = MultiSelector;
    } else if (typeof define === 'function' && define.amd) {
        define(function () {
            return MultiSelector;
        });
    } else {
        this.MultiSelector = MultiSelector;
    }

}).call(function () {
    return this || (typeof window !== 'undefined' ? window : global);
}());

