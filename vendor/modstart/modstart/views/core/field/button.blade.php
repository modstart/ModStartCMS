<div class="line" data-field id="{{$id}}">
    <div class="label">
        &nbsp;
    </div>
    <div class="field">
        <button type="{{$type}}" class="btn btn-{{$style}}">{{$label}}</button>
    </div>
</div>
<script>
    $(function(){
        var $field = $('#{{$id}}'), $button = $field.find('button');
        $button.on('click',function(){
            @if(!empty($onClickJsFunction))
                ({!! $onClickJsFunction !!})();
            @endif
        });
    });
</script>
