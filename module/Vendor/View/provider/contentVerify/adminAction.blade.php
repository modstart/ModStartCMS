<div class="ub-content-box ub-border">
    <div>
        <a href="javascript:;" data-admin-verify-pass class="btn btn-success">
            <i class="iconfont icon-check"></i> 通过审核
        </a>
        <a href="javascript:;" data-admin-verify-reject class="btn btn-danger">
            <i class="iconfont icon-close"></i> 拒绝审核
        </a>
        <input type="text" data-admin-verify-reject-reason class="form" placeholder="请输入拒绝理由" />
    </div>
</div>
<script>
    $(function(){
        $('[data-admin-verify-pass]').on('click', function(){
            MS.dialog.loadingOn();
            MS.api.postSuccess("{!! $passUrl !!}",{},res=>{
                MS.dialog.loadingOff();
                MS.api.defaultCallback(res);
            },err=>{
                MS.dialog.loadingOff();
            });
            return false;
        });
        $('[data-admin-verify-reject]').on('click', function(){
            MS.dialog.loadingOn();
            MS.api.postSuccess("{!! $rejectUrl !!}",{_reason:$('[data-admin-verify-reject-reason]').val()},res=>{
                MS.dialog.loadingOff();
                MS.api.defaultCallback(res);
            },err=>{
                MS.dialog.loadingOff();
            });
            return false;
        });
    });
</script>
