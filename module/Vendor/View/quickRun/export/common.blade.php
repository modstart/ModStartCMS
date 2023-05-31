@extends('modstart::admin.dialogFrame')

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
                        exportName: '{{$exportName}}',
                        format: 'xlsx',
                        checkedHeadTitles: {!! json_encode(array_keys($headTitles)) !!}
                    }
                },
                computed:{
                    allChecked(){
                        return this.checkedHeadTitles.length === {!! count($headTitles) !!};
                    }
                },
                methods: {
                    onAllCheckChange(v){
                        if(v){
                            this.checkedHeadTitles = {!! json_encode(array_keys($headTitles)) !!};
                        }else{
                            this.checkedHeadTitles = [];
                        }
                    },
                    doExport() {
                        MS.importExportWork.doExportExecute(this.format, (page, cb) => {
                            MS.api.postSuccess(
                                window.location.href,
                                {
                                    page: page,
                                    exportName: this.exportName,
                                    format: this.format,
                                    checkedHeadTitles: JSON.stringify(this.checkedHeadTitles)
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
                <div class="ub-form">
                    <div class="line">
                        <div class="label">&nbsp;</div>
                        <div class="field">
                            即将导出 <span class="tw-font-bold">{{$total}}</span> 条记录
                        </div>
                    </div>
                    @if(!empty($customHeadTitle))
                    <div class="line">
                        <div class="label">
                            导出字段
                        </div>
                        <div class="field">
                            <el-checkbox-group v-model="checkedHeadTitles">
                                @foreach($headTitles as $i=>$t)
                                    <el-checkbox style="min-width:4rem;" :label="{{$i}}" key="{{$t}}">{{$t}}</el-checkbox>
                                @endforeach
                            </el-checkbox-group>
                            <div>
                                <el-checkbox :value="allChecked" @change="onAllCheckChange">全选</el-checkbox>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="line">
                        <div class="label">文件格式</div>
                        <div class="field">
                            <el-radio-group v-model="format" >
                                @foreach($formats as $f)
                                    <el-radio label="{{$f}}">{{$f}}</el-radio>
                                @endforeach
                            </el-radio-group>
                        </div>
                    </div>

                    <div class="line">
                        <div class="label">文件名</div>
                        <div class="field">
                            <el-input v-model="exportName" placeholder="输入导出文件名">
                                <template slot="append">
                                    .@{{format}}
                                </template>
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
