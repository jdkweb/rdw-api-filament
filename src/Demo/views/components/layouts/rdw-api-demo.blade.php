<!doctype html>
<html>
<head>
    <title>RDW API - license plate check</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel=stylesheet href="https://cdn.jsdelivr.net/npm/pretty-print-json@3.0/dist/css/pretty-print-json.dark-mode.css">
    <script src="https://cdn.jsdelivr.net/npm/pretty-print-json@3.0/dist/pretty-print-json.min.js"></script>
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="m-16 bg-gray-300">

<div class="-mt-8 mx-auto w-[80%]">
    <label class="font-semibold pr-4">{{ __('rdw-api::form.languageLabel') }}</label>
    <select class="h-18 w-64 p-2 rounded"
        onchange="location.href='/{{ config('rdw-api.rdw_api_folder') }}/{{ config('rdw-api.rdw_api_filament_folder') }}/{{ config('rdw-api.rdw_api_demo_slug') }}/change-language/'+this.value">
        <option value="">Select</option>
        <option value="nl" {{ ($language=='nl'?'selected':'') }}>Nederlands</option>
        <option value="en"  {{ ($language=='en'?'selected':'') }}>English</option>
    </select>
</div>
{{ $slot }}
@filamentScripts
@vite('resources/js/app.js')
</body>
</html>
