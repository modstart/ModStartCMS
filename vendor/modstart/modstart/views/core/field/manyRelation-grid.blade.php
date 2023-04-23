@if(!empty($value))
    @if(\ModStart\Field\ManyRelation::MODE_GROUP_TAGS==$mode)
        @foreach($groupTags as $gt)
            @foreach($gt[$groupTagsChildKey] as $gtItem)
                @if(in_array($gtItem['id'], $value))
                    <div class="ub-tag info">
                        {{$gtItem['title']}}
                    </div>
                @endif
            @endforeach
        @endforeach
    @endif
@endif
