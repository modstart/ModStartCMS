;(function () {

    var globalIndex = 0;

    var UBPagePreview = function (option) {
        if (typeof $ == "undefined") {
            alert("UBPagePreview require jQuery");
            return;
        }
        globalIndex++;
        var defaultOption = {
            container: null,
            callback: {
                change: function (change) {

                }
            }
        };
        this.data = {
            $container: null
        };
        this.opt = $.extend(defaultOption, option);
        this.init();
    };

    UBPagePreview.prototype.init = function () {

    };

    if (typeof module !== 'undefined' && typeof exports === 'object' && define.cmd) {
        module.exports = UBPagePreview;
    } else if (typeof define === 'function' && define.amd) {
        define(function () {
            return UBPagePreview;
        });
    } else {
        this.UBPagePreview = UBPagePreview;
    }

}).call(function () {
    return this || (typeof window !== 'undefined' ? window : global);
}());

