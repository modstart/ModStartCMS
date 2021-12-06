<script>
    $(function () {
        $(document).on('click', 'a', function () {
            var href = this.href;
            if(!href) return;
            var l = window.location;
            var currentUrl = l.protocol + '//' + l.host;
            if (href.indexOf('http://') === 0 || href.indexOf('https://') === 0 || href.indexOf('//') === 0) {
                if (href.indexOf(currentUrl) !== 0) {
                    $(this).attr('href', "{{modstart_web_url('link_external_jumper')}}?target=" + MS.util.urlencode(href));
                }
            }
        });
    });
</script>
