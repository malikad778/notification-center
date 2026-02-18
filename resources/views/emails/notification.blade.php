<!DOCTYPE html>
<html>
<head>
    <title>{{ $payload->title }}</title>
</head>
<body>
    <h1>{{ $payload->title }}</h1>
    
    <div>
        {!! nl2br(e($payload->body)) !!}
    </div>

    @if($payload->actionUrl)
        <div style="margin-top: 20px;">
            <a href="{{ $payload->actionUrl }}" style="background-color: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                View Details
            </a>
        </div>
    @endif
    
    <div style="margin-top: 20px; color: #666; font-size: 12px;">
        @foreach($payload->data as $key => $value)
            @if(is_string($value))
                <p><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>
            @endif
        @endforeach
    </div>
</body>
</html>
