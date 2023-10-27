<?php
if(!empty($param['modelPrefix'])){
    $modelPrefix = $param['modelPrefix'];
}else{
    $modelPrefix = 'items[iIndex].custom';
}
?>
@foreach($fields as $f)
    @if(!empty($f))
        <div class="line">
            <div class="label">
                {{$f['title']}}
            </div>
            <div class="field">
                @if($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_TEXT)
                    <el-input v-model="{{$modelPrefix}}['{{$f['_name']}}']"
                    ></el-input>
                @elseif($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_RADIO)
                    <el-radio-group v-model="{{$modelPrefix}}['{{$f['_name']}}']">
                        @foreach($f['data']['option'] as $o)
                            <el-radio label="{{$o}}">{{$o}}</el-radio>
                        @endforeach
                    </el-radio-group>
                @elseif($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_FILE)
                    <file-selector v-model="{{$modelPrefix}}['{{$f['_name']}}']" upload-enable></file-selector>
                @elseif($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_FILES)
                    <files-selector v-model="{{$modelPrefix}}['{{$f['_name']}}']" upload-enable></files-selector>
                @else
                    暂未支持 {{$f['type']}}
                    <pre>{{\ModStart\Core\Util\SerializeUtil::jsonEncode($f)}}</pre>
                @endif
            </div>
        </div>
    @endif
@endforeach
