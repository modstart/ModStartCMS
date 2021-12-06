<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>授权登录中...</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta content="always" name="referrer"/>
</head>
<body>
<div style="text-align:center;color:#999;line-height:100px;">
    授权登录中...
</div>
<div id="oauthProxyRedirectButton" style="text-align:center;background:#CCC;color:#333;line-height:100px;display:none;">
    Redirect Now
</div>
<script>
    var OauthProxy = {
        version: '1.0.0',
        parseUrl: function (url) {
            url = url || window.location.href;
            var parser = document.createElement("a");
            parser.href = url;
            var param = {};
            var pairs = (parser.search || '?').substring(1).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var pos = pairs[i].indexOf('=');
                if (pos === -1) {
                    continue;
                }
                param[pairs[i].substring(0, pos)] = decodeURIComponent(pairs[i].substring(pos + 1));
            }
            return {
                protocol: parser.protocol,
                host: parser.host,
                hostname: parser.hostname,
                port: parser.port,
                pathname: parser.pathname,
                hash: parser.hash,
                search: parser.search,
                origin: parser.origin,
                param: param,
            };
        },
        buildUrl: function (urlInfo) {
            var part = [];
            part.push(urlInfo.protocol);
            part.push('//');
            part.push(urlInfo.host);
            part.push(urlInfo.pathname);
            var param = [];
            for (var k in urlInfo.param) {
                param.push(encodeURIComponent(k) + '=' + encodeURIComponent(urlInfo.param[k]));
            }
            if (param.length > 0) {
                part.push('?');
                part.push(param.join('&'));
            }
            if (urlInfo.hash) {
                part.push(urlInfo.hash);
            }
            return part.join('');
        },
        log: function (label, json) {
            json = json || {};
            console.log('>>>>> OauthProxy - ' + label + ' ' + JSON.stringify(json, null, 4));
        },
        redirect: function (url) {
            var oauthProxyDebug = (localStorage.getItem('oauthProxyDebug') ? true : false);
            if (oauthProxyDebug) {
                window.__oauthProxyRedirect = url;
                document.querySelector('#oauthProxyRedirectButton').style.display = 'block';
            } else {
                window.location.href = url;
            }
        },
        doProcess: function () {

            var ele = document.querySelector('#oauthProxyRedirectButton');
            if (ele) {
                ele.addEventListener('click', function () {
                    window.location.href = window.__oauthProxyRedirect;
                });
            }

            var urlInfo = OauthProxy.parseUrl(), redirectTo, i;
            var oauthProxyRedirect = localStorage.getItem('oauthProxyRedirect');

            OauthProxy.log('urlInfo', urlInfo);
            OauthProxy.log('oauthProxyRedirect', oauthProxyRedirect);
            var proxy = urlInfo.param['_proxy'] || '';
            if (proxy) {
                OauthProxy.log('Proxy Mode');
                var targetInfo = OauthProxy.parseUrl(proxy);
                OauthProxy.log('targetInfo', targetInfo);
                var redirectKeys = ['redirect_uri'];
                var found = false;
                for (i = 0; i < redirectKeys.length; i++) {
                    var k = redirectKeys[i];
                    if (k in targetInfo.param) {
                        OauthProxy.log('redirectKey', k);
                        localStorage.setItem('oauthProxyRedirect', targetInfo.param[k]);
                        targetInfo.param[k] = urlInfo.origin + urlInfo.pathname;
                        found = true;
                        break;
                    }
                }
                if (found) {
                    OauthProxy.log('targetInfo New', targetInfo);
                    redirectTo = OauthProxy.buildUrl(targetInfo);
                    OauthProxy.log('redirectTo', redirectTo);
                    OauthProxy.redirect(redirectTo);
                } else {
                    OauthProxy.log('not found redirectKey');
                }
                return true;
            } else if (oauthProxyRedirect) {
                OauthProxy.log('Redirect Mode');
                var redirectInfo = OauthProxy.parseUrl(oauthProxyRedirect);
                OauthProxy.log('redirectInfo', redirectInfo);
                var mergeKeys = ['protocol', 'host', 'pathname', 'hash'];
                for (i = 0; i < mergeKeys.length; i++) {
                    urlInfo[mergeKeys[i]] = redirectInfo[mergeKeys[i]];
                }
                OauthProxy.log('urlInfo New', urlInfo);
                redirectTo = OauthProxy.buildUrl(urlInfo);
                OauthProxy.log('redirectTo', redirectTo);
                localStorage.removeItem('oauthProxyRedirect');
                OauthProxy.redirect(redirectTo);
                return true;
            }
            OauthProxy.log('Unknown Mode');
            return false;
        }
    };
    OauthProxy.doProcess();
</script>
</body>
</html>
