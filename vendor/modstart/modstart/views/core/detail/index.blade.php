<div class="ub-form {{$formClass}}">
    @foreach($fields as $field)
        {!! $field->render() !!}
    @endforeach
</div>
