@extends('modstart::admin.dialogFrame')

@section('pageTitle'){{$pageTitle}}@endsection

@section('bodyAppend')
    @parent
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script src="@asset('asset/entry/exportWork.js')"></script>
    <script>
        $(function () {
            new Vue({
                el: '#app',
                data() {
                    return {
                        exportName: '{{$exportName}}',
                    }
                },
                methods: {
                    doExport() {
                        MS.exportWork.doExportExecute('xlsx', (page, cb) => {
                            MS.api.postSuccess(
                                window.location.href,
                                {
                                    page: page,
                                    exportName: this.exportName
                                },
                                (res) => {
                                    cb(res.data);
                                }
                            );
                        });
                    }
                }
            });
        });
    </script>
@endsection

@section('body')
    <div id="app" v-cloak class="tw-p-2 tw-rounded-lg tw-my-4 tw-max-w-screen-md tw-mx-auto tw-shadow-lg tw-bg-white">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    <i class="iconfont icon-download"></i>
                    {{$pageTitle}}
                </div>
            </div>
            <div class="body">
                @if($total<=0)
                    <div class="ub-alert warning">
                        <i class="iconfont icon-warning"></i>
                        当前筛选条件没有筛选到可导出的数据，请关闭当前页面重新筛选。
                    </div>
                @endif
                <div class="ub-form">
                    <div class="line">
                        <div class="label">&nbsp;</div>
                        <div class="field">
                            即将导出 <span class="tw-font-bold">{{$total}}</span> 条记录
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">文件名</div>
                        <div class="field">
                            <el-input v-model="exportName" placeholder="输入导出文件名">
                                <template slot="append">.xlsx</template>
                            </el-input>
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <a class="btn btn-primary" href="javascript:;" @click="doExport()">
                                开始导出Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
