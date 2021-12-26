<!DOCTYPE html>
<html>
<head lang="{{ config('app.locale') }}">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{csrf_token()}}">

    <title>VUE Test page</title>

    <link href=" {{ mix('/css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <base-structure></base-structure>
</div>
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
