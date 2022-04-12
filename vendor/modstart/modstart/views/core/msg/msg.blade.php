@extends('modstart::core.msg.frame')

@section('pageTitle',L('Message'))

@section('headAppend')
    <style type="text/css">
        body, html {background: #FFF;font:13px/20px normal Helvetica, Arial,"微软雅黑",sans-serif;padding:0;margin:0;text-align:center;line-height:1.5em;}
        .container{padding:0 20px;max-width:600px;margin:20px auto;}
        .container-box{border-radius:5px;background:#FFF;margin-top:50px;padding:40px 0;}
        .container-box.loaded{}
        .msg{font-size:16px;color:#4b4b62;margin:20px 0 0 0;}
        .redirect{padding:10px 0;color:#4b4b62;margin:10px 0 0 0;font-size:12px;}
        .redirect a{color:#4b4b62;text-decoration:underline;}
        .ui-success, .ui-error {
            width: 100px;
            height: 100px;
            margin:0 auto;
        }
        .ui-success-circle, .ui-error-circle {
            stroke-dasharray: 260.75219025px, 260.75219025px;
            stroke-dashoffset: 260.75219025px;
            stroke-linecap: round;
            animation: ani-circle 500ms ease-in both;
        }
        .ui-success-path {
            stroke-dasharray: 60px 64px;
            stroke-dashoffset: 62px;
            stroke-linecap: round;
            animation: ani-success-path 0.4s 500ms ease-in both;
        }
        .ui-error-line1 {
            stroke-dasharray: 54px 55px;
            stroke-dashoffset: 55px;
            stroke-linecap: round;
            animation: ani-error-line 0.15s 700ms linear both;
        }
        .ui-error-line2 {
            stroke-dasharray: 54px 55px;
            stroke-dashoffset: 55px;
            stroke-linecap: round;
            animation: ani-error-line 0.2s 500ms linear both;
        }
        @keyframes ani-circle {
            to {
                stroke-dashoffset: 521.5043805px;
            }
        }
        @keyframes ani-success-path {
            0% {
                stroke-dashoffset: 62px;
            }
            65% {
                stroke-dashoffset: -5px;
            }
            84% {
                stroke-dashoffset: 4px;
            }
            100% {
                stroke-dashoffset: -2px;
            }
        }
        @keyframes ani-error-line {
            to {
                stroke-dashoffset: 0;
            }
        }
    </style>
@endsection

@section('body')
    <div class="container">
        <div class="container-box">
            @if($code)
                <div class="icon">
                    <div class="ui-error">
                        <svg  viewBox="0 0 91 91" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="Group-2" transform="translate(3.000000, 3.000000)">
                                    <circle id="Oval-2" stroke="#F4201920" stroke-width="6" cx="41.5" cy="41.5" r="41.5"></circle>
                                    <circle  class="ui-error-circle" stroke="#F42019" stroke-width="6" cx="41.5" cy="41.5" r="41.5"></circle>
                                    <path class="ui-error-line1" d="M22.244224,22 L60.4279902,60.1837662" id="Line" stroke="#F42019" stroke-width="6" stroke-linecap="square"></path>
                                    <path class="ui-error-line2" d="M60.755776,21 L23.244224,59.8443492" id="Line" stroke="#F42019" stroke-width="6" stroke-linecap="square"></path>
                                </g>
                            </g>
                        </svg>
                    </div>
                </div>
            @else
                <div class="icon">
                    <div class="ui-success">
                        <svg viewBox="0 0 91 91" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="Group-3" transform="translate(3.000000, 3.000000)">
                                    <circle id="Oval-2" stroke="#33A93620" stroke-width="6" cx="41.5" cy="41.5" r="41.5"></circle>
                                    <circle class="ui-success-circle" id="Oval-2" stroke="#33A936" stroke-width="6" cx="41.5" cy="41.5" r="41.5"></circle>
                                    <polyline class="ui-success-path" id="Path-2" stroke="#33A936" stroke-linecap="round" stroke-width="8" points="19 38.8036813 31.1020744 54.8046875 63.299221 28"></polyline>
                                </g>
                            </g>
                        </svg>
                    </div>
                </div>
            @endif
            <div class="msg">
                {{$msg}}
            </div>
            <script>
                setTimeout(function(){
                    document.querySelector('.container-box').className = 'container-box loaded';
                },0);
            </script>
            @if (!empty($redirect))
                <div class="redirect">{{ L('Page Redirect Soon') }} <b id="wait">3</b> {{ L('Second') }} <a id="href" class="am-link-muted" href="{{$redirect}}">{{ L('Redirect Now') }}</a> ...</div>
                <script type="text/javascript">
                    (function(){
                        var wait = document.getElementById('wait'), href = document.getElementById('href').href;
                        var interval = setInterval(function(){
                            --wait.innerHTML;
                            if(wait.innerHTML <= 1) {
                                if('[back]'==href){
                                    window.history.go(-1);
                                }else if('[reload]'==href){
                                    window.history.go(-1);
                                }else{
                                    location.href = href;
                                }
                                clearInterval(interval);
                            };
                        }, 1000);
                    })();
                </script>
            @endif
        </div>
    </div>

@endsection

