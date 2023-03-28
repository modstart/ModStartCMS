<?php $adminUser = \ModStart\Admin\Model\AdminUser::getCached($value); ?>
{!! $adminUser?'<i class="iconfont icon-user"></i> '.htmlspecialchars($adminUser['username']):'<span class="ub-text-muted">-</span>' !!}
