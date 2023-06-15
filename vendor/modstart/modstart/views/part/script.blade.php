@if(!empty($script))
<script>
    $(function () {
        @foreach($script as $s)
            {!! $s !!};
        @endforeach
    });
</script>
@endif
