<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>[{{modstart_config('siteName')}}] @yield('pageTitle')</title>
    <style type="text/css">
        /* ===== Reset & Base ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100% !important;
            height: 100% !important;
            margin: 0;
            padding: 0;
        }
        body {
            font-size: 14px;
            line-height: 1.7;
            color: #2D3748;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Microsoft YaHei", "PingFang SC", "Hiragino Sans GB", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Email Wrapper ===== */
        .email-wrap {
            max-width: 800px;
            margin: 0 auto;
        }

        /* ===== Card Container ===== */
        .email-card {
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #E8ECF0;
        }

        /* ===== Top Accent Bar ===== */
        .email-accent {
            height: 4px;
            background: {{modstart_config('sitePrimaryColor','#2f4cb9')}};
        }

        /* ===== Header ===== */
        .email-header {
            padding: 24px 32px 20px;
            text-align: center;
            border-bottom: 1px solid #F0F2F5;
        }
        .email-header .logo {
            display: inline-block;
            color: #1A202C;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.4;
            text-decoration: none;
            letter-spacing: 0.3px;
        }
        .email-header .logo-text {
            display: block;
        }

        /* ===== Body Content ===== */
        .email-body {
            padding: 28px 32px 24px;
            font-size: 14px;
            line-height: 1.65;
            color: #2D3748;
        }

        /* --- Paragraphs --- */
        .email-body p {
            margin-bottom: 12px;
            line-height: 1.65;
        }
        .email-body p:last-child {
            margin-bottom: 0;
        }

        /* --- Links --- */
        .email-body a {
            color: {{modstart_config('sitePrimaryColor','#2f4cb9')}};
            text-decoration: none;
            font-weight: 500;
        }
        .email-body a:hover {
            text-decoration: underline;
        }

        /* --- Bold / Strong --- */
        .email-body strong,
        .email-body b {
            color: #1A202C;
        }

        /* --- Headings --- */
        .email-body h1,
        .email-body h2,
        .email-body h3,
        .email-body h4 {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 700;
            line-height: 1.4;
            color: #1A202C;
        }
        .email-body h1:first-child,
        .email-body h2:first-child,
        .email-body h3:first-child,
        .email-body h4:first-child {
            margin-top: 0;
        }
        .email-body h1 { font-size: 22px; }
        .email-body h2 { font-size: 19px; }
        .email-body h3 { font-size: 17px; }
        .email-body h4 { font-size: 15px; }

        /* --- Lists --- */
        .email-body ul,
        .email-body ol {
            margin: 0 0 12px 20px;
            padding: 0;
        }
        .email-body li {
            margin-bottom: 6px;
            line-height: 1.7;
        }
        .email-body li:last-child {
            margin-bottom: 0;
        }

        /* --- Horizontal Rule --- */
        .email-body hr {
            margin: 20px 0;
            border: 0;
            height: 1px;
            background: #EDF0F5;
        }

        /* --- Blockquote --- */
        .email-body blockquote {
            margin: 14px 0;
            padding: 10px 16px;
            border-left: 4px solid {{modstart_config('sitePrimaryColor','#2f4cb9')}};
            background: #F8FAFC;
            border-radius: 0 8px 8px 0;
            color: #4A5A72;
            font-style: normal;
        }
        .email-body blockquote p:last-child {
            margin-bottom: 0;
        }

        /* --- Code (inline & block) --- */
        .email-body code {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            background: #F1F4F8;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 13px;
            color: #D53F8C;
        }
        .email-body pre {
            margin: 14px 0;
            padding: 12px 16px;
            background: #F1F4F8;
            border-radius: 8px;
            overflow-x: auto;
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
            font-size: 13px;
            line-height: 1.5;
            color: #2D3748;
        }
        .email-body pre code {
            background: none;
            padding: 0;
            border-radius: 0;
            font-size: inherit;
            color: inherit;
        }

        /* --- Small / Muted --- */
        .email-body small,
        .email-body .text-muted {
            font-size: 13px;
            color: #8896AB;
        }

        /* ===== Footer ===== */
        .email-footer {
            background: #F8FAFD;
            padding: 20px 32px;
            text-align: center;
            font-size: 13px;
            color: #9CAAB8;
            line-height: 1.65;
            border-top: 1px solid #EDF0F5;
        }
        .email-footer a {
            color: {{modstart_config('sitePrimaryColor','#2f4cb9')}};
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }

        /* ===== Table ===== */
        .ub-email-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
            color: #2D3748;
        }
        .ub-email-table td {
            border: 1px solid #E8ECF0;
            border-style: solid none none solid;
            padding: 7px 12px;
        }
        .ub-email-table tr:last-child td {
            border-bottom: 1px solid #E8ECF0;
        }
        .ub-email-table td:last-child {
            border-right: 1px solid #E8ECF0;
        }
        /* Rounded corners: top-left */
        .ub-email-table tr:first-child td:first-child {
            border-top-left-radius: 8px;
        }
        /* Rounded corners: top-right */
        .ub-email-table tr:first-child td:last-child {
            border-top-right-radius: 8px;
        }
        /* Rounded corners: bottom-left */
        .ub-email-table tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }
        /* Rounded corners: bottom-right */
        .ub-email-table tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }
        .ub-email-table tr:nth-child(even) td {
            background-color: #F7F9FC;
        }
        .ub-email-table th {
            background: #F0F4F8;
            color: #1A202C;
            font-weight: 600;
            padding: 7px 12px;
            text-align: left;
            border: 1px solid #E8ECF0;
            border-style: solid none none solid;
        }
        .ub-email-table tr:last-child th {
            border-bottom: 1px solid #E8ECF0;
        }
        .ub-email-table th:last-child {
            border-right: 1px solid #E8ECF0;
        }
        /* th rounded corners */
        .ub-email-table tr:first-child th:first-child {
            border-top-left-radius: 8px;
        }
        .ub-email-table tr:first-child th:last-child {
            border-top-right-radius: 8px;
        }
        .ub-email-table tr:last-child th:first-child {
            border-bottom-left-radius: 8px;
        }
        .ub-email-table tr:last-child th:last-child {
            border-bottom-right-radius: 8px;
        }

        /* ===== Legacy ID Support ===== */
        #content {
            padding: 28px 32px 24px;
            font-size: 14px;
            line-height: 1.65;
            color: #2D3748;
            background: #FFFFFF;
        }
        #foot {
            background: #F8FAFD;
            padding: 20px 32px;
            text-align: center;
            font-size: 13px;
            color: #9CAAB8;
            line-height: 1.65;
            border-top: 1px solid #EDF0F5;
        }

        /* ===== Responsive ===== */
        @media only screen and (max-width: 640px) {
            .email-wrap {
                padding: 0;
            }
            .email-header {
                padding: 20px 18px 16px;
            }
            .email-header .logo {
                font-size: 17px;
            }
            .email-body {
                padding: 22px 18px 18px;
            }
            #content {
                padding: 22px 18px 18px;
            }
            .email-footer {
                padding: 16px 18px;
            }
            #foot {
                padding: 16px 18px;
            }
        }
        @media only screen and (max-width: 420px) {
            .email-body {
                padding: 18px 14px 14px;
            }
            #content {
                padding: 18px 14px 14px;
            }
        }

        /* ===== Outlook & Email Client Fixes ===== */
        .ExternalClass, .ReadMsgBody {
            width: 100%;
        }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass td {
            line-height: 100%;
        }
    </style>
    <!--[if mso]>
    <style type="text/css">
        .email-card { overflow: visible; }
        table td { border-collapse: collapse; }
        .email-header { padding: 20px 24px 16px !important; }
        .email-body { padding: 24px 24px 18px !important; }
        #content { padding: 24px 24px 18px !important; }
        .email-footer { padding: 16px 24px !important; }
        #foot { padding: 16px 24px !important; }
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#FFFFFF;font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, 'Microsoft YaHei', 'PingFang SC', 'Hiragino Sans GB', sans-serif;font-size:15px;line-height:1.7;color:#2D3748;-webkit-font-smoothing:antialiased;">
<!--[if mso]>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF;"><tr><td align="center">
<![endif]-->
<div class="email-wrap" style="max-width:800px;margin:0 auto;">
    <div class="email-card" style="background:#FFFFFF;border-radius:16px;overflow:hidden;border:1px solid #E8ECF0;">

        {{-- Top Accent Bar --}}
        <div class="email-accent" style="height:4px;background:{{modstart_config('sitePrimaryColor','#2f4cb9')}};font-size:0;line-height:0;">&nbsp;</div>

        {{-- Header --}}
        <div class="email-header" style="padding:24px 32px 20px;text-align:center;border-bottom:1px solid #F0F2F5;">
            <a class="logo" href="http://{{modstart_config('siteDomain')}}" target="_blank" style="display:inline-block;color:#1A202C;font-size:20px;font-weight:700;line-height:1.4;text-decoration:none;letter-spacing:0.3px;">
                <span class="logo-text" style="display:block;">{{modstart_config('siteName')}}</span>
            </a>
        </div>

        {{-- Body — child templates override @section('bodyContent') --}}
        @section('body')
            <div class="email-body" style="padding:28px 32px 24px;font-size:14px;line-height:1.65;color:#2D3748;">
                @section('bodyContent')
                @show
            </div>

            {{-- Footer --}}
            <div class="email-footer" style="background:#F8FAFD;padding:20px 32px;text-align:center;font-size:13px;color:#9CAAB8;line-height:1.65;border-top:1px solid #EDF0F5;">
                @if(modstart_config('siteDomain'))
                    <div>
                        {{modstart_config('siteDomain')}} &copy; {{ date('Y') }}
                    </div>
                @endif
                <div style="margin-top:4px;font-size:12px;color:#B8C4D0;">
                    此邮件由系统自动发送，请勿直接回复
                </div>
            </div>
        @show

    </div>
</div>
<!--[if mso]>
</td></tr></table>
<![endif]-->
</body>
</html>
