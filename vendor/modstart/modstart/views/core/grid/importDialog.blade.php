@extends('modstart::admin.dialogFrame')

@section('pageTitle'){{empty($pageTitle)?L('Import'):$pageTitle}}@endsection

@section('bodyContent')
    <div id="app" v-cloak>
        <div class="ub-form">
            <div class="line">
                <div class="label">
                    {{L('Import')}}
                </div>
                <div class="field">
                    <el-upload action="" ref="upload" :auto-upload="false" :file-list="[]" drag
                               :on-change="doFileSelect">
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">将文件拖到此处，或<em>点击上传XLSX文件</em></div>
                        <div class="el-upload__tip" slot="tip">
                            <a href="javascript:;" @click="doDownloadTemplate()"><i
                                    class="iconfont icon-download"></i> 点击这里下载模板</a>
                        </div>
                    </el-upload>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bodyAppend')
    @parent
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script src="@asset('asset/entry/gridExcelWork.js')"></script>
    <script>
        $(function (){
            new Vue({
                el:'#app',
                data(){
                    return {
                        file: null,
                        importHeader: {!! json_encode($header) !!},
                    }
                },
                methods: {
                    doFileSelect(file) {
                        this.file = file.raw
                        let importSuccess = 0, importDuplicated = 0, importFail = 0
                        const loadingIndex = this.$dialog.loadingOn('正在导入')
                        const loading = (text) => {
                            $('#layui-layer' + loadingIndex + ' .loading-text').html(text)
                            $(window).resize()
                        }
                        const error = (text) => {
                            this.$refs.upload.clearFiles()
                            this.$dialog.alertError(text)
                            this.$dialog.loadingOff()
                        }
                        const success = (text) => {
                            this.$refs.upload.clearFiles()
                            this.$dialog.alertSuccess(text)
                            this.$dialog.loadingOff()
                        }
                        const upload = (data, format) => {
                            if (data.length < 1) {
                                error('数据为空')
                                return
                            }
                            if (JSON.stringify(this.importHeader) !== JSON.stringify(data[0])) {
                                console.log('data', data)
                                error('文件格式不正确')
                                return
                            }
                            data.shift()
                            let processed = 0
                            let total = data.length
                            new window.__gridExcelWork.ListDispatcher()
                                .set(data)
                                .chunk(1)
                                .error((msg, me) => {
                                    error('上传数据错误：' + msg)
                                })
                                .interval(0)
                                .dispatch((list, cb, me) => {
                                    processed += list.length
                                    loading(`数据导入中（进度${processed}/${total}，成功${importSuccess}条，失败${importFail}条，重复${importDuplicated}条）`)
                                    this.$api.post(window.location.href, {record: JSON.stringify(list[0])}, res => {
                                        if (res.code) {
                                            if (res.code === 1) {
                                                importDuplicated++
                                            } else {
                                                importFail++
                                            }
                                        } else {
                                            importSuccess++
                                        }
                                        cb({code: 0, msg: null})
                                    }, res => {
                                        if (res.code) {
                                            if (res.code === 1) {
                                                importDuplicated++
                                            } else {
                                                importFail++
                                            }
                                        } else {
                                            importSuccess++
                                        }
                                        cb({code: 0, msg: null})
                                        return true
                                    })
                                })
                                .finish((me) => {
                                    success(`成功导入${importSuccess}条数据，失败${importFail}条数据，重复${importDuplicated}条`)
                                })
                                .start()
                        }
                        new window.__gridExcelWork.ExcelReader().file(this.file).parse((data) => {
                            upload(data)
                        })
                    },
                    doDownloadTemplate() {
                        new window.__gridExcelWork.ExcelWriter()
                            .data([this.importHeader].concat({!! json_encode($template) !!}))
                            .filename('{{$templateName}}.xlsx')
                            .download();
                    }
                }
            });
        });
    </script>
@endsection
