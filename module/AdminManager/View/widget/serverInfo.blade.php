<div class="ub-panel ub-cover">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-desktop"></i>
            服务器信息
        </div>
    </div>
    <div class="body">
        <table class="ub-table tw-font-mono">
            <tbody>
            <tr>
                <td class="tw-font-bold">
                    安全公告
                </td>
                <td>
                    <div data-system-notice></div>
                </td>
            </tr>
            <tr>
                <td width="100" class="tw-font-bold">
                    MSCore
                </td>
                <td>
                    V{{\ModStart\ModStart::$version}}
                    ( With <b>{{strtoupper(\ModStart\Module\ModuleManager::getEnv())}}</b> )
                </td>
            </tr>
            <tr>
                <td class="tw-font-bold">操作系统</td>
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
                <td class="tw-font-bold">文件上传限制</td>
                <td>
                    {{@ini_get('upload_max_filesize')}}
                </td>
            </tr>
            <tr>
                <td class="tw-font-bold">表单提交限制</td>
                <td>
                    {{@ini_get('post_max_size')}}
                </td>
            </tr>
            <tr>
                <td class="tw-font-bold">最大提交数量</td>
                <td>
                    {{@ini_get('max_file_uploads')}}
                </td>
            </tr>
            <tr>
                <td class="tw-font-bold">分配内存限制</td>
                <td>
                    {{@ini_get('memory_limit')}}
                </td>
            </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            // 请勿删除，用于获取最新的安全通告（比如框架、模块有重大缺陷的应急通知等）
            $('body').append('<script src="https://modstart.com/api/modstart/notice?modules={{urlencode($modules)}}"><' + '/script>');
        </script>
    </div>
</div>
