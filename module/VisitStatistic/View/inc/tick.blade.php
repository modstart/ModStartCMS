<script>
    (function(){
        var img = new Image();
        img.src = '{{ modstart_web_full_url('visit_statistic/tick') }}?url='+encodeURIComponent(window.location.href)+'&'+Math.random();
        img.onload = function() {
        };
    })();
</script>
