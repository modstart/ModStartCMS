<div class="ub-panel ub-cover">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-desktop"></i>
            服务器信息
        </div>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">📢 安全公告</div>
                    <div class="tw-flex-grow" data-system-notice></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🚀 系统内核</div>
                    <div class="tw-flex-grow">ModStart V{{\ModStart\ModStart::$version}} ( <b>{{strtoupper(ModStart\ModStart::env())}}</b> )</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🖥️ 操作系统</div>
                    <div class="tw-flex-grow">{{PHP_OS}}</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🐘 PHP版本</div>
                    <div class="tw-flex-grow">
                        V{{PHP_VERSION}}
                        {{PHP_INT_SIZE == 8 ? '64' : '32'}}位
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🌐 运行环境</div>
                    <div class="tw-flex-grow">
                        @if(PHP_SAPI=='fpm-fcgi')
                            Nginx（FPM）
                        @elseif(PHP_SAPI=='cgi-fcgi')
                            Nginx（FASTCGI）
                        @elseif(PHP_SAPI=='apache2handler')
                            Apache
                        @else
                            {{PHP_SAPI}}
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🧠 内存限制</div>
                    <div class="tw-flex-grow">{{@ini_get('memory_limit')}}</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🌍 服务器时区</div>
                    <div class="tw-flex-grow">{{date_default_timezone_get()}}</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">🕒 服务器时间</div>
                    <div class="tw-flex-grow" data-server-time>-</div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12">
                <div class="tw-flex tw-p-2">
                    <div class="tw-w-24 tw-flex-shrink-0 tw-font-bold">📂 服务器参数</div>
                    <div class="tw-flex-grow">
                        <div>
                            <code>upload_max_filesize({{@ini_get('upload_max_filesize')}})</code>
                            <code>post_max_size({{@ini_get('post_max_size')}})</code>
                            <code>max_file_uploads({{@ini_get('max_file_uploads')}})</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            // 请勿删除，用于获取最新的安全通告（比如框架、模块有重大缺陷的应急通知等）
            $('body').append('<script src="https://modstart.com/api/modstart/notice?modules={{urlencode($modules)}}&t={{date('YmdH')}}"><' + '/script>');
            $(function(){
                var $serverTime = $('[data-server-time]');
                var timeDiff = {{time()*1000}} - (new Date()).getTime()
                setInterval(function(){
                    $serverTime.text(new Date((new Date()).getTime() + timeDiff).toLocaleString());
                }, 1000);
            });
        </script>
    </div>
</div>
