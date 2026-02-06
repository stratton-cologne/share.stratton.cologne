<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stratton Share Link</title>
</head>

<body style="font-family: Arial, sans-serif; background: #0f172a; color: #e2e8f0; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #111827; border-radius: 16px; padding: 24px;">
        <h2 style="margin-top: 0; color: #ffffff;">Dein Share-Link ist bereit</h2>
        <p>Hallo,</p>
        <p>deine Dateien wurden hochgeladen. Hier ist dein Share-Link:</p>
        <p style="word-break: break-all;">
            <a href="{{ $sharedBatch->share_url ?? url('/share/' . $sharedBatch->token) }}" style="color: #38bdf8;">
                {{ url('/share/' . $sharedBatch->token) }}
            </a>
        </p>
        <p><strong>Ablauf:</strong>
            {{ optional($sharedBatch->expires_at)->timezone('Europe/Berlin')->format('d.m.Y H:i') }} Uhr</p>
        @if ($sharedBatch->max_downloads)
            <p><strong>Max. Downloads:</strong> {{ $sharedBatch->max_downloads }}</p>
        @endif

        <div style="margin-top: 16px;">
            <h3 style="margin: 0 0 8px; color: #ffffff; font-size: 16px;">Dateien ({{ count($files) }})</h3>
            <ul style="margin: 0; padding-left: 16px; color: #e2e8f0;">
                @foreach ($files as $file)
                    <li>{{ $file['original_name'] }} ({{ number_format($file['size'] / 1024, 1, ',', '.') }} KB)</li>
                @endforeach
            </ul>
        </div>

        <p style="margin-top: 16px; font-size: 12px; color: #94a3b8;">
            Dieser Link kann mit deinen Empfängern geteilt werden.
        </p>
    </div>
</body>

</html>