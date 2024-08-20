<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        /* Targeting SVG icons within pagination */
        .pagination .page-link svg,
        nav svg {
            width: 12px;
            height: 12px;
        }

        /* Optional: Adjust the padding of the pagination links */
        .pagination .page-link,
        nav .page-link {
            padding: 6px 10px;
        }

        .modal-dialog {
            max-width: 100%;
            margin: 0;
        }

        .modal-content {
            border: none;
            border-radius: 0;
        }

        .modal-background {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(to bottom, black 50%, white 50%);
        }

        .modal-content-wrapper {
            position: relative;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
        }

        .line-left,
        .line-right {
            content: '';
            position: absolute;
            top: 50%;
            height: 1px;
            /* Thin line height */
            background-color: #E6E6E6;
            /* Line color */
            z-index: 0;
            /* Ensure lines are behind the image */
        }

        .line-left {
            left: 0;
            width: calc(50% - 80px);
            /* Adjust as needed to position the line */
        }

        .line-right {
            right: 0;
            width: calc(50% - 80px);
            /* Adjust as needed to position the line */
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }

        .social-icons i {
            font-size: 1.5rem;
            margin: 0 10px;
        }

        .social-icons a i {
            font-size: 1.5rem;
            margin: 0 10px;
            color: #90B955; /* Light black color */
            transition: color 0.3s ease; /* Ensure smooth transition */
        }

        .social-icons a i:hover {
            color: #90B955; /* Color on hover */
        }
        .modal-header {
            border-bottom: none; /* Remove any shadow if present */
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            $('.user-item').on('click', function() {
                var user = $(this).data('user');

                $('#userImage').attr('src', user.picture.large);
                $('#userName').text(user.name.first + ' ' + user.name.last);
                $('#userEmail').text('Email: ' + user.email);
                $('#userPhone').text('Phone: ' + user.phone);
                $('#userLocation').text('Location: ' + user.location.city + ', ' + user.location.state);

                var userModal = new bootstrap.Modal($('#userModal'));
                userModal.show();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
