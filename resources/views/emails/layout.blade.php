<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'MA.GIA DONNA' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-logo {
            color: #ffffff;
            font-size: 32px;
            font-weight: bold;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .email-logo .donna {
            color: #fce4ec;
        }
        .email-tagline {
            color: #fce4ec;
            font-size: 14px;
            margin: 10px 0 0 0;
        }
        .email-body {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .email-title {
            font-size: 24px;
            font-weight: bold;
            color: #333333;
            margin: 0 0 20px 0;
        }
        .email-text {
            font-size: 16px;
            color: #666666;
            margin: 0 0 15px 0;
        }
        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            color: #ffffff !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(233, 30, 99, 0.3);
        }
        .email-button:hover {
            box-shadow: 0 6px 8px rgba(233, 30, 99, 0.4);
        }
        .email-info-box {
            background-color: #fce4ec;
            border-left: 4px solid #e91e63;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-info-box strong {
            color: #e91e63;
        }
        .email-warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-footer {
            background-color: #f9f9f9;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        .email-footer-text {
            font-size: 14px;
            color: #999999;
            margin: 5px 0;
        }
        .email-footer-link {
            color: #e91e63;
            text-decoration: none;
        }
        .email-footer-link:hover {
            text-decoration: underline;
        }
        .email-divider {
            height: 1px;
            background-color: #eeeeee;
            margin: 30px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-header {
                padding: 30px 20px;
            }
            .email-body {
                padding: 30px 20px;
            }
            .email-footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1 class="email-logo">
                MA.GIA <span class="donna">DONNA</span>
            </h1>
            <p class="email-tagline">Il tuo centro fitness & wellness</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="email-footer-text">
                <strong>MA.GIA DONNA</strong>
            </p>
            <p class="email-footer-text">
                Centro Fitness & Wellness
            </p>
            <p class="email-footer-text">
                Email: <a href="mailto:info@magiadonna.it" class="email-footer-link">info@magiadonna.it</a>
            </p>
            <p class="email-footer-text" style="margin-top: 20px; font-size: 12px;">
                Questa è un'email automatica, per favore non rispondere direttamente.<br>
                Per supporto contattaci attraverso i nostri canali ufficiali.
            </p>
        </div>
    </div>
</body>
</html>
