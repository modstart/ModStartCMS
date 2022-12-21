<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field" >
        <div id="{{$id}}Container">
            <input type="hidden" name="{{$name}}" :value="value" />
            <el-select
                    v-model="value"
                    filterable
                    remote
                    clearable
                    @if($readonly) disabled @endif
                    size="mini"
                    reserve-keyword
                    placeholder="{{$placeholder}}"
                    :remote-method="doRemoteSearch"
                    style="width:auto;"
                    :loading="loading">
                <el-option
                        v-for="item in options"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                </el-option>
            </el-select>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    {{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
    {{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
    {{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Container',
            data: {
                value: {!! json_encode($value) !!},
                loading: false,
                options: [],
                searchTimer: null
            },
            mounted () {
                this.doSearch('',this.value);
            },
            methods: {
                doSearch(keywords, value){
                    this.loading = true
                    MS.api.post("{{$server}}",{keywords:keywords,value:value},res=>{
                        this.loading = false
                        this.options = res.data.options
                    })
                },
                doRemoteSearch(keywords){
                    if(this.searchTimer){
                        clearTimeout(this.searchTimer);
                    }
                    this.searchTimer = setTimeout(()=>{
                        this.doSearch(keywords)
                    }, 500);
                }
            }
        });
    });
</script>
