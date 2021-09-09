<div class="ub-panel">
    <div class="head">
        <div class="title">服务器信息</div>
    </div>
    <div class="body">
        <table class="ub-table tw-font-mono">
            <tbody>
            <tr>
                <td width="100" class="tw-font-bold">操作系统</td>
                <td>{{PHP_OS}}</td>
            </tr>
            <tr>
                <td class="tw-font-bold">PHP版本</td>
                <td>V{{PHP_VERSION}}</td>
            </tr>
            <tr>
                <td class="tw-font-bold">HTTP服务</td>
                <td>
                    @if(PHP_SAPI=='fpm-fcgi')
                        Nginx（FPM）
                    @elseif(PHP_SAPI=='cgi-fcgi')
                        Nginx（FASTCGI）
                    @elseif(PHP_SAPI=='apache2handler')
                        Apache
                    @else
                        {{PHP_SAPI}}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="tw-font-bold">
                    ModStart
                </td>
                <td>
                    V{{\ModStart\ModStart::$version}}
                </td>
            </tr>
            <tr>
                <td class="tw-font-bold">
                    安全公告
                </td>
                <td>
                    <div data-system-notice></div>
                </td>
            </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            // 请勿删除，用于获取最新的安全通告（比如框架、模块有重大缺陷的应急通知等）
            $('body').append('<script src="https://modstart.com/api/modstart/notice?modules={{urlencode(join(',',$modules))}}"><' + '/script>');
        </script>
    </div>
</div>
