@if($gridEditable)
    <div>
        <input type="checkbox" value="1" name="{{$name}}_{{$_index}}" lay-skin="switch"
               lay-filter="{{$name}}_{{$_index}}"
               lay-text="{!! join('|',array_values($options)) !!}" @if(!empty($value)) checked @endif />
    </div>
    <script>
        layui.use('form', function () {
            var form = layui.form;
            form.on('switch({{$name}}_{{$_index}})', function (data) {
                var index = parseInt($(data.elem).closest('tr').attr('data-index'));
                $(data.elem).closest('[data-grid]').trigger('grid-item-cell-change', {
                    ele: data.elem,
                    index: index,
                    column: '{{$column}}',
                    value: data.elem.checked?1:0
                });
            });
        });
    </script>
@else
    @if(!empty($value))
        <span class="ub-text-success"><i class="iconfont icon-dot-sm"></i>{{$options[1]}}</span>
    @else
        <span class="ub-text-muted"><i class="iconfont icon-dot-sm"></i>{{$options[0]}}</span>
    @endif
@endif
