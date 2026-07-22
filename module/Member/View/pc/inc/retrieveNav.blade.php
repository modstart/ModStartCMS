<div class="tw-px-3 tw-pt-10">
    <div class="row">
        @foreach(['选择','验证','重置密码','完成'] as $i=>$t)
            <div class="col-3">
                <div class="tw-bg-gray-50 tw-rounded-2xl tw-leading-8 tw-text-center tw-text-sm @if($current==$i) ub-bg-primary tw-text-white @endif">
                    {{$i+1}}.{{$t}}
                </div>
            </div>
        @endforeach
    </div>
</div>
