<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #e4e4e7; background: #0c0c0f; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 32px auto; padding: 0 16px; }
        .card { background: #18181b; border: 1px solid #27272a; border-radius: 12px; padding: 32px; }
        .header { font-size: 12px; color: #71717a; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px; }
        h1 { font-size: 20px; font-weight: 600; color: #fafafa; margin: 0 0 16px; }
        .body { font-size: 14px; line-height: 1.6; color: #a1a1aa; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #27272a; font-size: 12px; color: #52525b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">{{ $announcement->hackathon->title }}</div>
            <h1>{{ $announcement->title }}</h1>
            <div class="body">
                {!! nl2br(e($announcement->body)) !!}
            </div>
            <div class="footer">
                This announcement was sent via {{ config('app.name') }}.
            </div>
        </div>
    </div>
</body>
</html>
