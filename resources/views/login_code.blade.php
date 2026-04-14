<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Sign-In Code</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', Figtree, ui-sans-serif, system-ui, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f0f5 0%, #f3f4f6 100%);
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .email-wrapper {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        .header {
            background: linear-gradient(135deg, #3f3f69 0%, #2a2a4b 100%);
            padding: 10px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            color: #f58220;
            letter-spacing: -0.5px;
        }
        .header-subtitle {
            color: #6b7280;
            font-size: 14px;
            opacity: 0.9;
            margin-top: 8px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 24px;
            color: #1f2937;
        }
        .code-section {
            background: linear-gradient(135deg, #f0f0f5 0%, #e1e1ed 100%);
            border-left: 4px solid #f58220;
            padding: 24px;
            border-radius: 8px;
            margin: 32px 0;
            text-align: center;
        }
        .code-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .code-value {
            font-size: 36px;
            font-weight: 700;
            color: #f58220;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            margin: 0;
            word-break: break-all;
        }
        .expiry-info {
            background: #fffaf5;
            border: 1px solid #fed9b8;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
            color: #c65b0d;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .expiry-icon {
            font-size: 20px;
        }
        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin-top: 32px;
            line-height: 1.6;
        }
        .security-note {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
            font-size: 12px;
            color: #4b5563;
            border-left: 3px solid #6b7280;
        }
        .security-note strong {
            color: #1f2937;
        }
        .footer {
            background: #f9fafb;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 24px 0;
        }
        .brand {
            color: #f58220;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <h1>Let's Connect</h1>
            </div>

            <div class="content">
                <p>We hebben een verzoek ontvangen om in te loggen op uw Let's Connect-account. Gebruik de onderstaande code om in te loggen:</p>

                <div class="code-section">
                    <div class="code-label">Uw Log-in code</div>
                    <div class="code-value">{{ $code }}</div>
                </div>

                <div class="expiry-info">
                    <span>Deze code is <strong>10 minuten</strong> geldig.</span>
                </div>

                <div class="divider"></div>

                <p style="font-size: 12px; color: #6b7280; margin-bottom: 0;">
                    Heeft u een vraag? Stuur dan een mail naar dit addres: <span class="brand">lets.connect.support@gmail.com</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

