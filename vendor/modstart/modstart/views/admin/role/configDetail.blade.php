<div class="line">
    <div class="label">
        配置
    </div>
    <div class="field">
        @foreach(\ModStart\Admin\Provider\AdminRoleConfigProvider::listAll() as $provider)
            <?php $html = $provider->renderDetail($item); ?>
            @if($html)
                <table class="ub-table mini border tw-bg-white">
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
    </div>
</div>
