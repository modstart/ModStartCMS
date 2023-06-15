<?php
if(!empty($param['modelPrefix'])){
    $modelPrefix = $param['modelPrefix'];
}else{
    $modelPrefix = 'items[iIndex].dynamic';
}
?>
@foreach($fields as $f)
    <div class="line">
        <div class="label">
            @if(!empty($f['isRequired']))
                <span>*</span>
            @endif
            {{$f['title']}}
        </div>
        <div class="field">
            @if($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_TEXT)
                <el-input v-model="{{$modelPrefix}}['{{$f['name']}}']"
                          placeholder="{{$f['placeholder']}}"
                ></el-input>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_NUMBER)
                <el-input-number v-model="{{$modelPrefix}}['{{$f['name']}}']"
                                 placeholder="{{$f['placeholder']}}"
                ></el-input-number>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_SWITCH)
                <el-switch v-model="{{$modelPrefix}}['{{$f['name']}}']"></el-switch>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_RADIO)
                <el-radio-group v-model="{{$modelPrefix}}['{{$f['name']}}']">
                    @foreach($f['data']['options'] as $o)
                        <el-radio label="{{$o['title']}}">{{$o['title']}}</el-radio>
                    @endforeach
                </el-radio-group>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_SELECT)
                <el-select v-model="{{$modelPrefix}}['{{$f['name']}}']">
                    @foreach($f['data']['options'] as $o)
                        <el-option label="{{$o['title']}}" value="{{$o['title']}}"></el-option>
                    @endforeach
                </el-select>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_CHECKBOX)
                <el-checkbox-group v-model="{{$modelPrefix}}['{{$f['name']}}']">
                    @foreach($f['data']['options'] as $o)
                        <el-checkbox label="{{$o['title']}}">{{$o['title']}}</el-checkbox>
                    @endforeach
                </el-checkbox-group>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_FILE)
                <file-selector v-model="{{$modelPrefix}}['{{$f['name']}}']" upload-enable></file-selector>
            @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_FILES)
                <files-selector v-model="{{$modelPrefix}}['{{$f['name']}}']" upload-enable></files-selector>
            @else
                暂未支持 {{$f['type']}}
                <code>{{json_encode($f)}}</code>
            @endif
        </div>
    </div>
@endforeach
