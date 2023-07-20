<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Stack Email</title>
</head>
<body>
    <p>Hello {{ $data['first_name'] }},</p>
    <p>Your review notecards are ready for {{ $data['today'] }}. Please <a href="{{ $data['login_link'] }}">log in</a> to review them.
    </p>
    <p>Thanks, <br>The Noted Team</p>

    <p>Want to stop receving emails? Click the link below to unsubscribe.</p>
    <a href="{{$data['unsubscribe']}}">Unsubscribe</a>
</body>
</html>
