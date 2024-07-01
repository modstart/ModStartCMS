<div class="line" data-field="{{$name}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Selector">
            <input type="hidden" name="{{$name}}" value="{{$value}}" />
            <a href="javascript:;" data-title class="btn">
                <i class="iconfont icon-plus"></i>
                选择用户
            </a>
        </div>
        <script>
            $(function(){
                var $selector = $('#{{$id}}Selector');
                $selector.find('[data-title]').click(function() {
                    (new MS.selectorDialog({
                        server: '{{$server}}',
                        limitMin: 1,
                        limitMax: 1,
                        callback: function(items) {
                            if(items.length){
                                $selector.find('input').val(items[0]._id);
                                $selector.find('[data-title]').text($(items[0].username).text());
                            }
                        }
                    })).show()
                });
            });
        </script>
    </div>
</div>
