<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="{{ asset('js/mindmap.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/mindmap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/mindmap.css') }}">
</head>
<body>
    {!! $mindMapContent !!}
</body>
</html>