let Form = require('./form.js');
let Dialog = require('./dialog.js');

let Lister = function (container, option) {

    let $listerContainer = $(container.lister || null);
    let $searchContainer = $(container.search || null);

    let me = this;

    let param = {

        page: 1,
        pageSize: 10,
        // [field1,field2]
        field: [],
        // [ [field1,asc],[field2,desc] ]
        order: [],
        /*
        // Eq              -> { field : { eq:'keyword'} }
        // Like            -> { field : { like:'keyword' } }
        // DatetimeRange   -> { field : { min:value, max:value, is:null } }

        //         ///// -> { field : { exp:'and', leftLike:'keyword', rightLike:'keyword' } }
        // 限定取值 -> { field : { in:[1,2,3] } }
         */
        search: []
    };

    let data = null;

    if (option && ('param' in option)) {
        param = $.extend(param, option.param)
    }

    let opt = $.extend({
        server: '/path/to/server',
        editQuickServer: '/path/to/edit/quick',
        hashUrl: true,
        showLoading: true,
        customLoading: function (loading) {

        },
        render: function (data) {

        }
    }, option);

    this.getData = function () {
        return data;
    };

    this.init = function () {
        let hash = window.location.hash;
        if (hash.indexOf('#') === 0) {
            hash = hash.substring(1);
        }
        try {
            hash = decodeURIComponent(hash);
            var initParam = JSON.parse(hash);
            for (let i in initParam) {
                if (i in param) {
                    param[i] = initParam[i];
                }
            }
        } catch (e) {
        }
        if ($listerContainer) {
            $listerContainer.on('click', '[data-refresh-button]', function () {
                me.load(true);
                return false;
            });
        }
    };

    this.initSearch = function () {
        $searchContainer.on('click', '[data-search-button]', function () {
            me.search();
            return false;
        });
        $searchContainer.on('click', '[data-reset-search-button]', function () {
            me.resetSearch();
            me.resetOrder();
            me.load(true);
            return false;
        });
        $searchContainer.on('click', '[data-expand-search-button]', function () {
            $searchContainer.find('.field-more-expand').toggleClass('field-more-expand__active');
            return false;
        });
        $searchContainer.find('[data-grid-filter-field]').each(function (i, o) {
            if ($(o).data('init')) {
                $(o).data('init')(param.search)
            }
        });
    };

    this.initTable = function () {
        // console.log('initTable');
    };

    this.prepareSearch = function () {
        param.search = [];
        $searchContainer.find('[data-grid-filter-field]').each(function (i, o) {
            if ($(o).data('get')) {
                var v = $(o).data('get')();
                if (v) {
                    param.search.push(v);
                }
            }
        });
    };

    this.resetSearch = function () {
        param.search = [];
        $searchContainer.find('[data-grid-filter-field]').each(function (i, o) {
            if ($(o).data('reset')) {
                var v = $(o).data('reset')();
                if (v) {
                    param.search.push(v);
                }
            }
        });
    };

    this.resetOrder = function () {
        param.order = [];
    };

    this.setPageSize = function (pageSize) {
        param.pageSize = pageSize;
    };

    this.setPage = function (page) {
        param.page = page;
    };

    this.setParam = function (key, value) {
        if (key in param) {
            param[key] = value;
        }
    };

    this.getParam = function () {
        return param;
    };

    this.setOption = function (name, value) {
        opt[name] = value;
    };

    this.refresh = function () {
        me.load();
    };
    this.search = function () {
        param.page = 1;
        me.prepareSearch();
        me.load(true);
    };
    this.load = function () {
        if (opt.showLoading) {
            Dialog.loadingOn();
        }
        opt.customLoading(true)
        data = null;
        $.post(opt.server, param)
            .done(function (res) {
                if (opt.hashUrl) {
                    window.location.replace('#' + JSON.stringify(param));
                }
                if (opt.showLoading) {
                    Dialog.loadingOff();
                }
                opt.customLoading(false)
                Form.defaultCallback(res, {
                    success: function (res) {
                        opt.render(res.data);
                    }
                });
            })
            .fail(function (res) {
                try {
                    if (opt.showLoading) {
                        Dialog.loadingOff();
                    }
                    opt.customLoading(false)
                    Form.defaultCallback(res);
                } catch (e) {
                }
            });
    };

    this.init();
    this.initSearch();
    this.initTable();
    this.prepareSearch();
    this.load();

};

module.exports = Lister;

