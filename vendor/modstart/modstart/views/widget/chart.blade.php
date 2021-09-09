{!! \ModStart\ModStart::js('asset/vendor/echarts/echarts.all.js') !!}
{!! \ModStart\ModStart::script("
var chart = echarts.init(document.getElementById(".json_encode($id)."));
chart.setOption(".json_encode($option).");
") !!}
<div id="{{$id}}" style="height:300px;"></div>
