var Fullscreen = {
    enter: function (callback) {
        var docElm = document.documentElement;
        //W3C
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
        //FireFox
        else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
        //Chrome等
        else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
        //IE11
        else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
    },
    exit: function (callback) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
            setTimeout(function () {
                callback && callback();
            }, 1000);
        }
    },
    isFullScreen: function () {
        if (document.exitFullscreen) {
            return document.fullscreen;
        } else if (document.mozCancelFullScreen) {
            return document.mozFullScreen;
        } else if (document.webkitCancelFullScreen) {
            return document.webkitIsFullScreen;
        } else if (document.msExitFullscreen) {
            return document.msFullscreenElement;
        }
        return false;
    },
    trigger: function (callback) {
        if (Fullscreen.isFullScreen()) {
            Fullscreen.exit(function () {
                callback('exit');
            });
        } else {
            Fullscreen.enter(function () {
                callback('enter');
            });
        }
    }
}

if (!('api' in window)) {
    window.api = {}
}
window.api.fullscreen = Fullscreen;
if (!('MS' in window)) {
    window.MS = {}
}
window.MS.fullscreen = Fullscreen;
