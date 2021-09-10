<div class="row">
    <div class="col-md-6">
        <img src="{{modstart_web_url('captcha/image')}}?{{time()}}"
             data-captcha
             onclick="this.src='{{modstart_web_url('captcha/image')}}?'+Math.random();"
             style="height:2rem;border:1px solid #DDD;border-radius:3px;width:100%;" />
    </div>
    <div class="col-md-6">
        <input type="text" name="captcha" placeholder="图片验证" />
    </div>
</div>
