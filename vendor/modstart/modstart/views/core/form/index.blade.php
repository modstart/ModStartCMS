<form class="ub-form {{$formClass}}" method="post" action="{{$formUrl or \ModStart\Core\Input\Request::currentPageUrl()}}" data-ajax-form>
    @foreach($fields as $field)
        {!! $field->render() !!}
    @endforeach
    @if($showSubmit || $showReset)
        <div class="line">
            <div class="field">
                @if($showSubmit)
                    <button type="submit" class="btn btn-primary">{{L('Submit')}}</button>
                @endif
                @if($showReset)
                    <button type="reset" class="btn">{{L('Reset')}}</button>
                @endif
            </div>
        </div>
    @endif
</form>
