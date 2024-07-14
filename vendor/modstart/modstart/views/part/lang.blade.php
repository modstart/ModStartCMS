@if(!empty($lang))
<script>(function(){ window.lang = Object.assign(window.lang||{}, {!! json_encode($lang,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE) !!} ); })();</script>
@endif
