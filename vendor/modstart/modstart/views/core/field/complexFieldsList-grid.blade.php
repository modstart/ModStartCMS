<div class="tw-bg-gray-50 tw-p-1 tw-rounded">
    <table class="ub-table mini tw-bg-white">
        <thead>
        <tr>
            @foreach($fields as $f)
                <th>{{$f['title']}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @if(is_array($value))
            @foreach($value as $v)
                <tr>
                    @foreach($fields as $f)
                        <td>{{$v[$f['name']]}}</td>
                    @endforeach
                </tr>
            @endforeach
        @elseif(!empty($value))
            <tr>
                <td colspan="{{count($fields)}}">
                    <pre
                        style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty($value)}}</pre>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
