<div class="line">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <div>
            <script id="{{$name}}" name="{{$name}}" type="text/plain">{!! $value !!}</script>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        window.api.editor.basic('{{$name}}', {
            server: "{{$server}}",
            ready: function () {
                // console.log('ready');
            }
        }, {topOffset: 0});
    });
</script>
