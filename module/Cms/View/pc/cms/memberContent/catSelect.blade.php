@extends($_viewMemberFrame)

@section('pageTitleMain')选择栏目@endsection

@section('memberBodyContent')

    <div class="ub-panel">
        <div class="head">
            <div class="title">
                <a href="{{modstart_web_url('member')}}">我</a>
                <i class="iconfont icon-angle-right ub-text-muted"></i>
                <span>发布内容</span>
                <i class="iconfont icon-angle-right ub-text-muted"></i>
                <span>选择栏目</span>
            </div>
        </div>
        <div class="body">
            <div class="ub-alert ub-alert-warning">
                <i class="iconfont icon-warning"></i>
                点击选择需要发布的栏目
            </div>
            <div class="tw-bg-gray-50 tw-p-4 tw-rounded-lg">
                @foreach($catTreeCanPost as $tree1)
                    <div class="tw-border-0 tw-border-b tw-border-solid tw-border-gray-100 tw-py-2">
                        @if($tree1['_memberCanPost'])
                            <a class="tw-text-lg tw-inline-block tw-bg-gray-200 tw-rounded tw-py-2 tw-text-center" style="min-width:8rem;" href="?catId={{$tree1['id']}}">
                                {{$tree1['title']}}
                            </a>
                        @else
                            <span class="tw-text-lg tw-text-gray-400 tw-inline-block tw-bg-gray-200 tw-rounded tw-py-2 tw-text-center" style="min-width:8rem;">
                                {{$tree1['title']}}
                            </span>
                        @endif
                    </div>
                    @if(!empty($tree1['_child']))
                        <div class="tw-py-2">
                            @foreach($tree1['_child'] as $tree2)
                                @if(empty($tree2['_child']))
                                    <a class="tw-inline-block tw-bg-gray-200 tw-rounded tw-py-2 tw-text-center" style="min-width:8rem;" href="?catId={{$tree2['id']}}">
                                        {{$tree2['title']}}
                                    </a>
                                @else
                                    <div>
                                        <div>
                                            @if($tree1['_memberCanPost'])
                                                <a class="tw-inline-block tw-bg-gray-200 tw-rounded tw-py-2 tw-text-center" style="min-width:8rem;" href="?catId={{$tree2['id']}}">
                                                    {{$tree2['title']}}
                                                </a>
                                            @else
                                                <span class="tw-text-gray-400 tw-inline-block tw-bg-gray-200 tw-rounded tw-py-2 tw-text-center" style="min-width:8rem;">
                                                    {{$tree2['title']}}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="tw-py-4">
                                            @foreach($tree1['_child'] as $tree3)
                                                @if($tree3['_memberCanPost'])
                                                    <a href="?catId={{$tree3['id']}}" class="tw-inline-block tw-bg-gray-200 tw-rounded tw-py-1 tw-text-center" style="min-width:8rem;">
                                                        {{$tree3['title']}}
                                                    </a>
                                                @else
                                                    <span class="tw-text-gray-400 tw-inline-block tw-bg-gray-200 tw-rounded tw-py-1 tw-text-center" style="min-width:8rem;">
                                                        {{$tree3['title']}}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

@endsection