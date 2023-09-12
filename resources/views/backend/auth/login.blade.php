<!DOCTYPE html>
<html lang="zxx">


<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Login - {{ $company->name }}</title>


    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap1.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/vendors/themefy_icon/themify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/vendors/font_awesome/css/all.min.css') }}" />


    <link rel="stylesheet" href="{{ asset('backend/vendors/scroll/scrollable.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/css/metisMenu.css') }}">

    <link rel="stylesheet" href="{{ asset('backend/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/css/colors/default.css') }}" id="colorSkinCSS">

    <style>
        .add-position {
            width: 100%;
            margin: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    @include('sweetalert::alert', ['cdn' => 'https://cdn.jsdelivr.net/npm/sweetalert2@9'])
    <div class="main_content_iner add-position">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_box mb_30">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">

                                <div class="modal-content cs_modal">
                                    <div class="modal-header justify-content-center theme_bg_1">
                                        <h5 class="modal-title text_white">Log in</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('storeLogin') }}" method="post">
                                            @csrf
                                            <div class>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter your email" name="email">
                                            </div>
                                            <div class>
                                                <input type="password" class="form-control" placeholder="Password"
                                                    name="password">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <button type="submit" class="btn btn-primary text-center">Log
                                                        in</button>
                                                </div>
                                                <div class="col-md-6" style="text-align: right;">
                                                    <a href="{{ route('forgotPassword') }}" class="pass_forget_btn"
                                                        style="margin-top: 7px;">Forget Password?</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/js/jquery1-3.4.1.min.js') }}"></script>
    <script src="{{ asset('backend/js/popper1.min.js') }}"></script>
    <script src="{{ asset('backend/js/bootstrap.min.html') }}"></script>
    <script src="{{ asset('backend/js/metisMenu.js') }}"></script>
    <script src="{{ asset('backend/vendors/scroll/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('backend/vendors/scroll/scrollable-custom.js') }}"></script>
    <script src="{{ asset('backend/js/custom.js') }}"></script>
</body>


</html>
