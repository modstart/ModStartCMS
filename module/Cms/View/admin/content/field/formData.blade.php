<table class="ub-lister-table mini">
    <tbody>
    @foreach($customFields as $customField)
        <tr>
            <td width="100">{{$customField['title']}}:</td>
            <td>
                @if(in_array($customField['fieldType'],[
                    'text','textarea','video','audio'
                ]))
                    {{empty($data[$customField['name']])?'-':$data[$customField['name']]}}
                @else
                    {{$data[$customField['name']]?'-':$data[$customField['name']]}}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
