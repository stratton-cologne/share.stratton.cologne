<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stratton Share</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>

<body class="bg-slate-950 text-slate-100">
    <div id="app"></div>
</body>

</html>