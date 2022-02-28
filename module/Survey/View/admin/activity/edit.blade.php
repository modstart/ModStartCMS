@extends('modstart::admin.dialogFrame')

@section('pageTitle'){{'问卷调查'}}@endsection

@section('bodyAppend')
    @parent
    <script src="@asset('asset/common/editor.js')"></script>
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script src="@asset('vendor/Vendor/entry/all.js')"></script>
    <script>
        $(function () {
            new Vue({
                el: '#app',
                data: {
                    name: {!! json_encode($activity['name']) !!},
                    enable: {!! json_encode(boolval($activity['enable'])) !!},
                    loginRequired: {!! json_encode(boolval($activity['loginRequired'])) !!},
                    startTime: {!! json_encode($activity['startTime']) !!},
                    endTime: {!! json_encode($activity['endTime']) !!},
                    joinType: {!! json_encode(intval($activity['joinType'])) !!},
                    cover: {!! json_encode($activity['cover']?$activity['cover']:modstart_web_url('asset/image/none.png')) !!},
                    description: {!! json_encode($activity['description']?$activity['description']:'') !!},
                    questions: {!! json_encode($questions) !!}
                },
                mounted: function () {
                    $('[name=data]').val(JSON.stringify(this.$data))
                },
                updated: function () {
                    $('[name=data]').val(JSON.stringify(this.$data))
                }
            });
        });
    </script>
@endsection

@section('bodyContent')

    <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" data-ajax-form method="post">
        <input type="hidden" name="data" value=""/>
        <div id="app" v-cloak>
            <div class="ub-panel">
                <div class="head">
                    <div class="title">基本信息</div>
                </div>
                <div class="body">
                    <el-form label-width="100px">
                        <el-form-item label="问卷名称">
                            <el-input v-model="name"></el-input>
                        </el-form-item>
                        <el-form-item label="问卷开启">
                            <el-switch v-model="enable"></el-switch>
                        </el-form-item>
                        <el-form-item label="要求用户登录">
                            <el-switch v-model="loginRequired"></el-switch>
                        </el-form-item>
                        <el-form-item label="问卷时间">
                            <el-date-picker type="datetime" v-model="startTime"></el-date-picker>
                            -
                            <el-date-picker type="datetime" v-model="endTime"></el-date-picker>
                        </el-form-item>
                        <el-form-item label="参与限制">
                            <el-radio-group v-model="joinType">
                                @foreach(\Module\Survey\Type\JoinType::getList() as $k=>$v)
                                    <el-radio :label="{{$k}}">{{$v}}</el-radio>
                                @endforeach
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="封面">
                            <image-selector v-model="cover"></image-selector>
                        </el-form-item>
                        <el-form-item label="问卷描述">
                            <rich-editor v-model="description"></rich-editor>
                        </el-form-item>
                    </el-form>
                </div>
            </div>

            <div class="ub-panel">
                <div class="head">
                    <div class="title">题目设定</div>
                </div>
                <div class="body">
                    <div class="ub-panel" v-for="(question,questionIndex) in questions">
                        <div class="head">
                            <div class="more">
                                <el-button data-tip-popover="向上移动" v-if="questionIndex>0"
                                           @click="questions.splice(questionIndex-1,0,questions.splice(questionIndex,1)[0])">
                                    <i class="iconfont icon-angle-up"></i></el-button>
                                <el-button data-tip-popover="向下移动" v-if="questionIndex<questions.length-1"
                                           @click="questions.splice(questionIndex+1,0,questions.splice(questionIndex,1)[0])">
                                    <i class="iconfont icon-angle-down"></i></el-button>
                                <el-button data-tip-popover="删除" @click="questions.splice(questionIndex,1)"><i
                                        class="iconfont icon-trash"></i></el-button>
                            </div>
                            <div class="title">问题 @{{ questionIndex+1 }}</div>
                        </div>
                        <div class="body">
                            <el-form label-width="100px">
                                <el-form-item label="题目类型">
                                    <el-radio-group v-model="question.type">
                                        @foreach(\Module\Survey\Type\SurveyQuestionType::getList() as $k=>$v)
                                            <el-radio :label="{{$k}}">{{$v}}</el-radio>
                                        @endforeach
                                    </el-radio-group>
                                </el-form-item>
                                <el-form-item label="必须填写">
                                    <el-switch v-model="question.required"></el-switch>
                                </el-form-item>
                                <el-form-item label="题目描述">
                                    <el-input v-model="question.body" type="textarea"
                                              :autosize="{minRows:1}"></el-input>
                                </el-form-item>
                                <el-form-item label="选项"
                                              v-if="question.type=={{\Module\Survey\Type\SurveyQuestionType::SINGLE_CHOICE}} || question.type=={{\Module\Survey\Type\SurveyQuestionType::MULTI_CHOICE}}">
                                    <el-row v-for="(questionChoice,questionChoiceIndex) in question.choice">
                                        <el-col :span="20">
                                            <el-input v-model="question.choice[questionChoiceIndex]"></el-input>
                                        </el-col>
                                        <el-col :span="3">
                                            <el-button data-tip-popover="删除"
                                                       @click="question.choice.splice(questionChoiceIndex,1)"><i
                                                    class="iconfont icon-trash"></i></el-button>
                                        </el-col>
                                    </el-row>
                                    <el-row>
                                        <el-col :span="24">
                                            <el-button data-tip-popover="增加一个选项" @click="question.choice.push('')"><i
                                                    class="iconfont icon-plus"></i></el-button>
                                        </el-col>
                                    </el-row>
                                </el-form-item>
                            </el-form>
                        </div>
                    </div>

                    <div class="tw-text-center tw-py-10">
                        <a href="javascript:;"
                           @click="questions.push({id:0,type:null,required:false,body:'',choice:[]})"><i
                                class="iconfont icon-plus"></i> 增加题目</a>
                    </div>

                </div>
            </div>
        </div>
    </form>
@endsection
