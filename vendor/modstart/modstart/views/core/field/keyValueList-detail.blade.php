<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        @if(is_array($value))
            <div class="tw-bg-gray-100 tw-p-1">
                <table class="ub-table border mini" style="width:{{$width-40}}px;">
                    @foreach($value as $v)
                        <tr>
                            <td width="100">{{$v[$keyLabel]}}</td>
                            <td>{{$v[$valueLabel]}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </div>
</div>


