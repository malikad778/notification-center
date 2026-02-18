<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">{{ $payload->title }}</div>
        <p>{{ $payload->body }}</p>
        
        @if($payload->actionUrl)
            <div style="margin-top: 20px;">
                <a href="{{ $payload->actionUrl }}" class="btn">View Details</a>
            </div>
        @endif
        
        <div class="footer">
            Sent via Notification Center
        </div>
    </div>
</body>
</html>
