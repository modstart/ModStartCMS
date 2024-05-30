<div class="line">
    <div class="label">
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
