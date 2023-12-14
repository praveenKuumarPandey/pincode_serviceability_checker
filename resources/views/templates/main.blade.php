<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name='csrf-token' content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pincode Serviceability Checker') }}</title>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <!-- Styles -->
    {{--
    <link rel="stylesheet" href="{{ asset('css/app.css')}}"> --}}

    <!-- js -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    {{-- <script src="{{ asset('js/manifest.js') }}" defer></script> --}}


    <style>
        body {
            background-color: #f7f8f9;
            color: #3a3c42;
        }

        .navbar {
            background-color: #1e2632;
            background: #75c944;
            box-shadow: 0 5px 10px 0 rgba(204, 204, 204, 0.3);

            a {
                color: #fff;
            }

            a:active {
                color: rgb(255, 227, 66);
            }

            a:link {
                color: #303030;
            }

            a:visited {
                color: #303030;
            }

            a:hover {
                color: #303030;
            }

            margin-bottom: 40px;
        }

        .ajax-loader {
            visibility: hidden;
            background-color: rgba(255, 255, 255, 0.7);
            position: absolute;
            z-index: +100 !important;
            width: 50vw;
            height: 50vh;
        }

        .ajax-loader img {
            /* position: relative; */
            width: 30vw;
            height: 50vh;
        }

        /* #resultMessage{
        width: 30vw;
        height: 50vh;
        background-color: orangered;
        } */



        .red-text {
            color: red;
            font-weight: bolder;
            /* background-color: yellow; */

        }

        .green-text {
            color: green;
            font-weight: bolder;
            /* background-color: lightgray; */

        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('pincode.default_view')}}">{{ config('app.name', 'Laravel App')
                }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page"
                            href="{{ route('pincode.default_view') }}">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Pincode Checker App
                        </a>
                        <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('pincode.default_view') }}">Pincode Checker
                                    Tool</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('pincode.uploadServiceabilityData') }}">Upload
                                    Updated
                                    Serviceability Data</a></li>

                        </ul>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')

    </main>


    @stack('scripts')

</body>

</html>