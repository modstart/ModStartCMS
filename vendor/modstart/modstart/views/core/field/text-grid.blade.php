@if(!empty($gridEditable))
    <div data-value-show="{{$name}}_{{$_index}}" class="tw-cursor-text tw-rounded tw-px-1 tw--mx-1 hover:tw-bg-gray-100">
        {{$value?$value:'&nbsp;'}}
    </div>
    <div data-value-edit="{{$name}}_{{$_index}}" style="display:none;">
        <input type="text" name="{{$name}}_{{$_index}}" value="{{$value}}"/>
    </div>
    <script>
        $(function () {
            var value = null;
            $('[data-value-show="{{$name}}_{{$_index}}"]')
                .on('click', function () {
                    $(this).hide();
                    $('[data-value-edit="{{$name}}_{{$_index}}"]').show();
                    $('[name={{$name}}_{{$_index}}]').focus();
                });
            var submit = function ($this) {
                if (value == $this.val()) {
                    $('[data-value-edit="{{$name}}_{{$_index}}"]').hide();
                    $('[data-value-show="{{$name}}_{{$_index}}"]').show();
                    return;
                }
                if ($this.data('blur-submit-value') == $this.val()) {
                    return;
                }
                $this.data('blur-submit-value', $this.val());
                $this.closest('[data-grid]').trigger('grid-item-cell-change', {
                    ele: $this,
                    index: {{$_index}},
                    column: '{{$column}}',
                    value: $this.val()
                });
            };
            $('[name={{$name}}_{{$_index}}]')
                .on('focus', function () {
                    value = $(this).val();
                })
                .on('blur', function () {
                    submit($(this));
                })
                .on('keypress', function (e) {
                    if (e.which == 13) {
                        submit($(this));
                    }
                });
        });
    </script>
@else
    {{$value}}
@endif
