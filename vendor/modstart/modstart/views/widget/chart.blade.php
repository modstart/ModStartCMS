{!! \ModStart\ModStart::js('asset/vendor/echarts/echarts.all.js') !!}
{!! \ModStart\ModStart::script("
var ele = document.getElementById(".json_encode($id).");
var chart = echarts.init(ele);
chart.setOption(".json_encode($option).");
MS.ui.onResize( ele, chart.resize );
") !!}
<div id="{{$id}}" style="height:300px;"></div>
