<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stratton Share Hinweis</title>
</head>

<body style="font-family: Arial, sans-serif; background: #0f172a; color: #e2e8f0; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #111827; border-radius: 16px; padding: 24px;">
        <h2 style="margin-top: 0; color: #ffffff;">Dein Upload läuft bald ab</h2>
        <p>Hallo,</p>
        <p>
            dein Upload mit <strong>{{ $sharedBatch->files()->count() }} Dateien</strong> wird in etwa
            <strong>{{ $hoursLeft }} Stunden</strong> automatisch gelöscht.
        </p>
        <p>Share-Link:</p>
        <p><a href="{{ url("/share/{$sharedBatch->token}") }}"
                style="color: #38bdf8;">{{ url("/share/{$sharedBatch->token}") }}</a></p>
        <p style="margin-top: 24px; color: #94a3b8; font-size: 12px;">Wenn du den Upload länger benötigst, lade die
            Datei bitte erneut hoch.</p>
    </div>
</body>

</html>