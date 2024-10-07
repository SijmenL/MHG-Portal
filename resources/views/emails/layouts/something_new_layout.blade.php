<!DOCTYPE html>
<html>
    <head>
        <title></title>
        
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="x-apple-disable-message-reformatting" content="">
        <meta content="target-densitydpi=device-dpi" name="viewport">
        <meta content="true" name="HandheldFriendly">
        <meta content="width=device-width" name="viewport">
        <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">

        <style type="text/css">
        /* Global Reset */
        body, table, td, a { text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; }
        img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        table { border-collapse: collapse !important; }
        body { margin: 0; padding: 0; width: 100%; }
        .ExternalClass { width: 100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        /* Main Table Styling */
        table {
            border-collapse: separate;
            table-layout: fixed;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        table td {
            border-collapse: collapse;
        }
        body, a, li, p, h1, h2, h3 {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        html {
            -webkit-text-size-adjust: none !important;
        }
        body {
            font-family: BlinkMacSystemFont, Segoe UI, Helvetica Neue, Arial, sans-serif, 'Inter Tight';
            font-size: 15px;
            line-height: 1.5;
            color: #0e1e3b;
            background-color: #ffffff;
        }
        h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 34px;
            font-weight: 600;
            color: #1e202b;
        }
        h2 {
            font-size: 24px;
            font-weight: 400;
            color: #333;
        }
        h3 {
            font-size: 20px;
            font-weight: 400;
            color: #333;
        }
        p {
            font-size: 15px;
            line-height: 22px;
            color: #0e1e3b;
        }
        /* Container Styling */
        .container {
            width: 100%;
        }
        .centered {
            text-align: center;
        }
        /* Box Styling */
        .box {
            background-color: #a8a9a9;
            border-radius: 12px;
            padding: 30px;
            text-align: left;
        }
        /* Two-column layout for desktop */
        .two-columns {
            display: flex;
            justify-content: space-between;
        }
        .two-columns .column {
            width: 48%;
            text-align: left;
        }
        /* Mobile layout: stack columns vertically */
        @media only screen and (max-width: 600px) {
            .two-columns {
                display: block;
            }
            .two-columns .column {
                width: 100%;
                margin-bottom: 20px;
                text-align: center;
            }

            .box{
                margin-left: 20px;
                margin-right: 20px;
            }
        }
        /* Action Button */
        .action-button {
            background-color: #0092df;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            line-height: 40px;
            text-decoration: none;
            padding: 0 20px;
            display: inline-block;
        }
        /* Footer */
        .footer-text {
            font-size: 13px;
            line-height: 22px;
            color: #0e1e3b;
            font-weight: 300;
            text-align: left;
        }
        .footer-bold {
            font-weight: bold;
            color: #0f1f3d;
        }
        </style>
    </head>
    <body>
        <div class="container">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
            <tbody><tr>
                <td align="center">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tbody><tr>
                        <td>
                            <table role="presentation" cellpadding="0" cellspacing="0" align="center">
                                <tbody><tr>
                                <td style="padding: 40px 15px; width: 450px; border-bottom: 1px solid #efeff4;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tbody><tr class="centered">
                                            <td>
                                            <img src="https://waterscoutingmhg.nl/wp-content/uploads/2023/07/MHGlogoalgemeen.png" width="auto" height="100" alt="" style="display:block; margin:auto;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 30px;">
                                                @yield('title')
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 20px;">
                                                @yield('greeting')
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 20px;">
                                            <div class="box">
                                                @yield('info')
                                            </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 30px;" class="centered">
                                                @yield('action')	
                                            </td>
                                        </tr>
                                        <tr>
                                            @yield('main_footer')
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 20px;">
                                            <p class="footer-text">All rights reserved | Portal MHG © 2024</p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                                </td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    </tbody></table>
                </td>
            </tr>
        </tbody></table>
        </div>
    </body>
</html>