<div id="{{$id}}Input" v-cloak>
    <input type="hidden" name="{{$name}}" :value="jsonValue" />
    <table class="ub-table border tw-bg-white">
        <tbody>
            <tr>
                <td width="80">请求地址</td>
                <td>
                    <el-input size="mini" v-model="value.url" placeholder="请输入请求地址"></el-input>
                </td>
            </tr>
            <tr>
                <td>请求方式</td>
                <td>
                    <el-radio-group size="mini" v-model="value.method">
                        <el-radio label="GET">GET</el-radio>
                        <el-radio label="POST">POST</el-radio>
                    </el-radio-group>
                </td>
            </tr>
            <tr>
                <td>请求头</td>
                <td>
                    <el-table size="mini" :data="value.headers" border>
                        <el-table-column prop="key" label="Key" width="200">
                            <template slot-scope="scope">
                                <el-input size="mini" v-model="scope.row.key" placeholder="请输入Key"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column prop="value" label="Value">
                            <template slot-scope="scope">
                                <el-input size="mini" v-model="scope.row.value" placeholder="请输入Value"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="100">
                            <template slot-scope="scope">
                                <a href="javascript:;" @click="value.headers.splice(scope.$index, 1)" class="ub-text-danger">
                                    <i class="iconfont icon-plus"></i>
                                    删除
                                </a>
                            </template>
                        </el-table-column>
                    </el-table>
                    <a href="javascript:;" @click="value.headers.push({key:'',value:''})" class="ub-text-muted">
                        <i class="iconfont icon-plus"></i>
                        增加
                    </a>
                </td>
            </tr>
            <tr>
                <td>请求参数</td>
                <td>
                    <el-table size="mini" :data="value.query" border>
                        <el-table-column prop="key" label="Key" width="200">
                            <template slot-scope="scope">
                                <el-input size="mini" v-model="scope.row.key" placeholder="请输入Key"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column prop="value" label="Value">
                            <template slot-scope="scope">
                                <el-input size="mini" v-model="scope.row.value" placeholder="请输入Value"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="100">
                            <template slot-scope="scope">
                                <a href="javascript:;" @click="value.query.splice(scope.$index, 1)" class="ub-text-danger">
                                    <i class="iconfont icon-plus"></i>
                                    删除
                                </a>
                            </template>
                        </el-table-column>
                    </el-table>
                    <a href="javascript:;" @click="value.query.push({key:'',value:''})" class="ub-text-muted">
                        <i class="iconfont icon-plus"></i>
                        增加
                    </a>
                </td>
            </tr>
            <tr>
                <td>请求编码</td>
                <td>
                    <el-radio-group size="mini" v-model="value.enctype">
                        <el-radio label="Json">json</el-radio>
                        <el-radio label="FormData">form-data</el-radio>
                        <el-radio label="UrlEncoded">x-www-form-urlencoded</el-radio>
                    </el-radio-group>
                </td>
            </tr>
            <tr v-if="['FormData','UrlEncoded'].includes(value.enctype)">
                <td>请求内容</td>
                <td>
                    <el-table size="mini" :data="value.bodyParam" border>
                        <el-table-column prop="key" label="Key" width="200">
                            <template slot-scope="scope">
                                <el-input size="mini" v-model="scope.row.key" placeholder="请输入Key"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column prop="value" label="Value">
                            <template slot-scope="scope">
                                <el-input size="mini" v-model="scope.row.value" placeholder="请输入Value"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="100">
                            <template slot-scope="scope">
                                <a href="javascript:;" @click="value.bodyParam.splice(scope.$index, 1)" class="ub-text-danger">
                                    <i class="iconfont icon-plus"></i>
                                    删除
                                </a>
                            </template>
                        </el-table-column>
                    </el-table>
                    <a href="javascript:;" @click="value.bodyParam.push({key:'',value:''})" class="ub-text-muted">
                        <i class="iconfont icon-plus"></i>
                        增加
                    </a>
                </td>
            </tr>
            <tr v-else>
                <td>
                    请求内容
                </td>
                <td>
                    <el-input type="textarea" :rows="5" v-model="value.bodyRaw" placeholder="请输入内容"></el-input>
                </td>
            </tr>
        </tbody>
    </table>
</div>
{{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
{{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
{{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
<?php
$apiValue = $value;
if(null===$apiValue){
    $apiValue = $defaultValue;
}
if(null===$apiValue){
    $apiValue = [
        'url'=>'http://',
        'method'=>'GET',
        'headers'=>[],
        'query'=>[],
        // FormData, Json, UrlEncoded
        'enctype'=>'Json',
        'bodyParam'=>[],
        'bodyRaw'=> '{}'
    ];
}
?>
<script>
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: {
                value: {!! json_encode($apiValue) !!}
            },
            computed: {
                jsonValue: function () {
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
