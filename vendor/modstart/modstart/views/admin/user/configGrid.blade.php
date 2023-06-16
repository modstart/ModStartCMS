@foreach(\ModStart\Admin\Provider\AdminUserConfigProvider::listAll() as $provider)
    <?php $html = $provider->renderGrid($item); ?>
    @if($html)
        <table class="ub-table mini head-dark border tw-bg-white">
            <thead>
                <tr>
                    <th>{{ $provider->title() }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {!! $html !!}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
@endforeach
