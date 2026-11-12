<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Portal Login</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="margin-bottom: 12px;">Client Portal Login</h2>

    <p>Hello {{ $client->full_name ?: $client->company_name ?: 'Client' }},</p>

    <p>Your client portal access is ready. You can log in using the details below.</p>

    <p>
        <strong>Login URL:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a><br>
        <strong>Email:</strong> {{ $client->email }}<br>
        @if ($plainPassword !== '')
            <strong>Password:</strong> {{ $plainPassword }}
        @else
            <strong>Password:</strong> Your existing portal password
        @endif
    </p>

    <p>After login, you can raise project issue tickets from the client portal.</p>

    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
