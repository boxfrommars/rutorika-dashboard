<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="/assets/admin/favicon.ico" />
    <meta name="_token" content="{{ csrf_token() }}" />

    <title>Dashboard</title>

    <link rel="stylesheet" href="/packages/rutorika/dashboard/vendor/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/packages/rutorika/dashboard/vendor/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="/packages/rutorika/dashboard/vendor/jquery-minicolors/jquery.minicolors.css" type="text/css" />
    <link rel="stylesheet" href="/packages/rutorika/dashboard/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" href="/packages/rutorika/dashboard/vendor/jquery-file-upload/css/jquery.fileupload.css">
    <link rel="stylesheet" href="/packages/rutorika/dashboard/vendor/magnific-popup/magnific-popup.css">

    <link rel="stylesheet" href="/packages/rutorika/dashboard/css/dashboard.css" />

    <script src="/packages/rutorika/dashboard/vendor/jquery/jquery.min.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/underscore/underscore.min.js"></script>
</head>

<body>

    <div id="wrapper">
        <div id="sidebar-wrapper">
            @include('dashboard::partials.sidebar')
        </div>

        <div id="page-content-wrapper">
            <div class="navbar navbar-default navbar-fixed-top" role="navigation" id="main-navbar">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <button type="button" class="navbar-toggle fake pull-left" id="menu-toggle">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Dashboard</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="/">Перейти на сайт</a></li>
                            <li><a href="#">Выйти</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 main">
                        @include('dashboard::partials.flash')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="/packages/rutorika/dashboard/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/bootstrap/js/bootstrap.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/bootstrap-growl/jquery.bootstrap-growl.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/jquery/jquery.cookie.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/moment-with-locales.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/jquery-minicolors/jquery.minicolors.min.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/jquery-file-upload/js/jquery.iframe-transport.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/jquery-file-upload/js/jquery.fileupload.js"></script>
    <script src="/packages/rutorika/dashboard/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

    <script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>

    <script src="/packages/rutorika/dashboard/js/script.js"></script>
</body>

</html>