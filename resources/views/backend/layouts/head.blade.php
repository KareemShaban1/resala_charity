<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="Coderthemes" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('backend/assets/images/favicon.ico')}}">

    <!-- third party css -->
    <link href="{{asset('backend/assets/css/vendor/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet" type="text/css">
    <!-- third party css end -->

    <!-- App css -->
    <link href="{{asset('backend/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
    @if (App::getLocale() == 'en')
        <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
    @else
        <link href="{{ asset('backend/assets/css/rtl_style.css') }}" rel="stylesheet">
    @endif

    <!-- Global Assets -->
    @include('backend.layouts.global-assets')

    <!-- jQuery -->
    <!-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}" defer></script> -->

    <!-- Custom CSS -->
    @stack('styles')
</head>