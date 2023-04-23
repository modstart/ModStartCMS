@extends($frameView)

@section('pageTitle'){{$pageTitle}}@endsection

@section('bodyAppend')
    @parent
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script src="@asset('asset/entry/importExportWork.js')"></script>
    <script>
        $(function () {
            new Vue({
                el: '#app',
                data() {
                    return {
                        headTitles: {!! json_encode($headTitles) !!},
                        templateName: {!! json_encode($templateName) !!},
                        templateData: {!! json_encode($templateData) !!},
                        file: null,
                        uploadResult: [],
                    }
                },
                methods: {
                    doDownloadTemplate(format) {
                        const data = [this.headTitles].concat(this.templateData)
                        switch (format) {
                            case 'xlsx':
                                new MS.importExportWork.ExcelWriter()
                                    .data(data)
                                    .filename(this.templateName + '.xlsx')
                                    .download()
                                break;
                            case 'csv':
                                MS.importExportWork.FileUtil.downloadCSV(this.templateName + '.csv', data)
                                break;
                            default:
                                console.error('未支持的格式 ' + format)
                                break;
                        }
                    },
                    doFileSelect(file) {
                        this.file = file.raw
                        const fileFormat = file.name.split('.').pop().toLowerCase()
                        let importSuccess = 0, importDuplicated = 0, importFail = 0
                        const loadingIndex = MS.dialog.loadingOn('正在导入')
                        const loading = (text) => {
                            $('#layui-layer' + loadingIndex + ' .loading-text').html(text)
                            $(window).resize()
                        }
                        const error = (text) => {
                            this.$refs.upload.clearFiles()
                            MS.dialog.alertError(text)
                            MS.dialog.loadingOff()
                        }
                        const success = (text) => {
                            this.$refs.upload.clearFiles()
                            MS.dialog.alertSuccess(text)
                            MS.dialog.loadingOff()
                        }
                        const upload = (data, format) => {
                            if (data.length < 1) {
                                error('数据为空')
                                return
                            }
                            if (JSON.stringify(this.headTitles) !== JSON.stringify(data[0])) {
                                console.log('data', data)
                                error('文件格式不正确')
                                return
                            }
                            data.shift()
                            data = data
                                .filter(o => {
                                    return !!o.join('')
                                })
                            let processed = 0
                            let total = data.length
                            this.uploadResult = []
                            let row = 0
                            new MS.importExportWork.ListDispatcher()
                                .set(data)
                                .chunk(1)
                                .error((msg, me) => {
                                    error('上传数据错误：' + msg)
                                })
                                .interval(0)
                                .dispatch((list, cb, me) => {
                                    processed += list.length
                                    loading(`数据导入中（进度${processed}/${total}，成功${importSuccess}条，失败${importFail}条，重复${importDuplicated}条）`)
                                    let one = list[0]
                                    // console.log('ImportData', one)
                                    MS.api.post(window.location.href, {
                                        data: JSON.stringify(one),
                                    }, res => {
                                        MS.api.defaultCallback(res, {
                                            success: res => {
                                                importSuccess++
                                                this.uploadResult.push({
                                                    row: ++row,
                                                    status: 'success',
                                                    msg: '',
                                                    record: one
                                                })
                                            },
                                            error: res => {
                                                if (res.code === 1) {
                                                    importDuplicated++
                                                    this.uploadResult.push({
                                                        row: ++row,
                                                        status: 'duplicated',
                                                        msg: res.msg,
                                                        record: one
                                                    })
                                                } else {
                                                    importFail++
                                                    this.uploadResult.push({
                                                        row: ++row,
                                                        status: 'fail',
                                                        msg: res.msg,
                                                        record: one
                                                    })
                                                }
                                            }
                                        })
                                        cb({code: 0, msg: null})
                                    })
                                })
                                .finish((me) => {
                                    success(`成功上传${importSuccess}条数据，失败${importFail}条数据，重复${importDuplicated}条`)
                                })
                                .start();
                        }
                        switch (fileFormat) {
                            case 'xlsx':
                                new MS.importExportWork.ExcelReader().file(this.file).parse((data) => {
                                    upload(data)
                                });
                                break;
                            case 'csv':
                                new MS.importExportWork.CSVParser().file(this.file).read((data) => {
                                    upload(data)
                                });
                                break;
                            default:
                                console.error('未支持的导出格式 ' + fileFormat)
                                break;
                        }

                    },
                }
            });
        });
    </script>
@endsection

@section('headAppend')
    @parent
    <style type="text/css">
        .el-upload {
            display: block;
        }

        .el-upload .el-upload-dragger {
            width: 100%;
        }
    </style>
@endsection

@section($_tabSectionName)
    <div id="app" v-cloak>
        <div class="tw-p-2 tw-rounded tw-mx-auto tw-bg-white">
            <div class="ub-panel">
                <div class="head">
                    <div class="title">
                        <i class="iconfont icon-download"></i>
                        {{$pageTitle}}
                    </div>
                </div>
                <div class="body">
                    @if(!empty($pageDescription))
                        <div class="tw-rounded-lg tw-p-4 tw-bg-gray-100 tw-mb-4">
                            <div class="ub-html lg">
                                {!! $pageDescription !!}
                            </div>
                        </div>
                    @endif
                    <div>
                        <el-upload action="" :show-file-list="false" ref="upload" :auto-upload="false" :file-list="[]"
                                   drag :on-change="doFileSelect">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">将文件拖到此处，或<em>点击上传{{join(',',$formats)}}文件</em></div>
                            <div class="el-upload__tip" slot="tip">
                                @foreach($formats as $format)
                                    <a href="javascript:;" @click="doDownloadTemplate('{{$format}}')"
                                       class="tw-mr-2">
                                        <i class="iconfont icon-download"></i>
                                        下载{{$format}}模板
                                    </a>
                                @endforeach
                            </div>
                        </el-upload>
                    </div>
                </div>
            </div>
            <div class="ub-panel" v-if="uploadResult.length>0">
                <div class="head">
                    <div class="more">
                        <a href="javascript:;" class="ub-text-muted" @click="uploadResult=[]">
                            清空
                        </a>
                    </div>
                    <div class="title">上传结果</div>
                </div>
                <div class="body">
                    <table class="ub-table border">
                        <thead>
                        <tr>
                            <th width="80">行</th>
                            <th width="80">状态</th>
                            <th width="160">消息</th>
                            <th>内容</th>
                        </tr>
                        </thead>
                        <tr v-for="item in uploadResult">
                            <td>@{{ item.row }}</td>
                            <td>
                                <span class="ub-text-success" v-if="item.status==='success'">成功</span>
                                <span class="ub-text-danger" v-else-if="item.status==='fail'">失败</span>
                                <span class="ub-text-warning" v-else-if="item.status==='duplicated'">重复</span>
                            </td>
                            <td>
                                <div v-if="!!item.msg">@{{ item }}</div>
                                <div v-else class="ub-text-muted">-</div>
                            </td>
                            <td>
                                <div class="tw-font-mono">@{{ JSON.stringify(item.record, null, 2) }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
