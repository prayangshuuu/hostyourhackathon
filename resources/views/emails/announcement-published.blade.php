<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; padding: 20px;">
    <p style="color: #666; font-size: 14px; margin-bottom: 8px;">{{ $announcement->hackathon->title }}</p>
    <h2 style="margin-top: 0;">{{ $announcement->title }}</h2>
    
    <div style="margin-top: 20px; margin-bottom: 30px; font-size: 15px;">
        {!! nl2br(e($announcement->body)) !!}
    </div>
    
    <p>
        <a href="{{ route('announcements.show', $announcement) }}" style="color: #4f46e5; text-decoration: underline;">View announcement on site &rarr;</a>
    </p>
</body>
</html>
