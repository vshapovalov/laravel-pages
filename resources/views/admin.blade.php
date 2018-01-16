<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @yield('html_attributes')>
<head >
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
@yield('head')

<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ url('vendor/vshapovalov/crud/assets/css/admin.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
@yield('content')

@section('scripts')
    <script>
        window.baseUrl = "{{ env('APP_URL') }}";
        window.crudPrefix = "{{ config('cruds.crud_prefix') }}";
        window.mediaPrefix = "{{ config('cruds.media_prefix') }}";
        window.mediaPath = {!! json_encode(\Illuminate\Support\Facades\Session::get('media.path', [
            [ 'title' => 'Библиотека', 'id' => 'root', 'path' => '/storage/uploads/', 'type' => 'folder']
        ]))  !!};
        window.uploadPath = "{{ '/storage/uploads' }}";
    </script>

    <script src="{{ url('vendor/vshapovalov/crud/assets/js/admin.js') }}"></script>
@show
</body>
</html>