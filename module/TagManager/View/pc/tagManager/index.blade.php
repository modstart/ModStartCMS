@extends($_viewFrame)

@section('pageTitleMain'){{'标签云'}}@endsection

@section('bodyContent')
    <div
        class="tw-text-white ub-text-center tw-text-lg tw-py-4 lg:tw-py-10 ub-cover tw-bg-gradient-to-br tw-from-gray-400 tw-to-gray-500">
        <div class="tw-text-xl lg:tw-text-4xl animated fadeInUp">
            <i class="iconfont icon-tag"></i>
            标签云
        </div>
    </div>

    <div class="ub-container margin-top">
        @if(empty($bizList))
            <div class="ub-empty">
                <div class="icon">
                    <i class="iconfont icon-empty-box"></i>
                </div>
                <div class="text">暂无记录</div>
            </div>
        @endif
        @foreach($bizList as $bizItem)
            <div class="ub-panel">
                <div class="head">
                    <div class="title">
                        {{$bizItem['title']}}标签云
                    </div>
                </div>
                <div class="body">
                    <div>
                        <canvas id="cloudWords_{{$bizItem['name']}}"
                                width="{{$canvasWidth}}" height="{{$canvasHeight}}" style="width:100%;"
                                class="canvas"></canvas>
                    </div>
                    <div>
                        @foreach($bizItem['records'] as $tagItem)
                            <a class="tw-bg-gray-100 tw-leading-8 tw-inline-block tw-px-4 tw-rounded-2xl tw-text-gray-800 tw-mr-2 tw-mb-2"
                               href="{{$tagItem["_url"]}}" target="_blank">
                                {{$tagItem['tag']}}
                                <span class="tw-rounded-3xl tw-bg-gray-300 tw-text-white tw-px-2">
                                    {{$tagItem['cnt']}}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="ub-color-a"></div>
            <script src="@asset('vendor/TagManager/js/wordcloud2.js')"></script>
            <script>
                $(function () {
                    var list = {!! json_encode($bizItem['counts'],JSON_UNESCAPED_UNICODE) !!};
                    var colors = ['#4F7FF3', '#5BC692', '#F0A453', '#6A46BD', '#587BE9', '#e9bd6c'];
                    WordCloud(document.getElementById('cloudWords_{{$bizItem['name']}}'), {
                        fontFamily: 'Helvetica Neue,Helvetica,PingFang SC,Tahoma,Arial,sans-serif',
                        color: function (word, weight) {
                            return colors[parseInt(Math.random() * colors.length)];
                        },
                        backgroundColor: '#F0F1F4',
                        list: list,
                        click: function (item) {
                            window.location.href = item[2];
                        }
                    });
                });
            </script>
        @endforeach
    </div>

@endsection
