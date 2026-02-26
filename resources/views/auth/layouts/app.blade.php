<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Pro Drive - Admin Dashboard">
    <meta name="author" content="Pro Drive Team">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title', 'Pro Drive - Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('admin/img/Prodrive 4.png') }}" type="image/png">

    <!-- Custom fonts -->
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- Tailwind CSS for additional utility classes -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Custom CSS Enhancements -->
    <style>
        :root {
            --primary-color: #034947;
            --secondary-color: #5a5c69;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #5a5c69 0%, #403f4c 100%);
            color: #f4f4f4;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #5a5c69 0%, #403f4c 100%) !important;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #025c54;
            border-color: #025c54;
        }

        /* Improved focus and active states */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(3, 73, 71, 0.25);
        }

        /* Adjust text for better readability on dark background */
        .text-gray-900 {
            color: #333 !important;
        }
    </style>

    @yield('extra-head')
</head>

<body class="bg-gradient-primary">
    <div class="container">
        @yield('content')
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript -->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages -->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    @yield('extra-scripts')
</body>

</html>