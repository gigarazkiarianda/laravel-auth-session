<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Auth</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome untuk ikon -->
    @stack('styles')
    <style>
        /* Gaya kustom */
        .settings-btn {
            background-color: rgba(128, 128, 128, 0.199); /* Abu-abu transparan */
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 1000;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .settings-btn:hover {
            background-color: #38383860;
            text-decoration: none;
            color: white;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f8f9fa;
            box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px 0; /* Padding diturunkan sedikit untuk mengurangi ukuran tombol */
            border-top: 1px solid #ddd; /* Garis batas atas */
        }

        .bottom-nav a {
            color: #007bff;
            text-decoration: none;
            font-size: 24px;
            transition: color 0.3s ease;
        }

        .bottom-nav a:hover {
            color: #0056b3;
        }

        .bottom-nav .active {
            color: #0056b3;
            font-weight: bold;
        }

        /* Dropdown pencarian */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            z-index: 1000;
            display: none; /* Sembunyikan secara default */
        }

        .search-results .result-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .search-results .result-item:hover {
            background-color: #f8f9fa;
        }

        .search-results .result-item img {
            border-radius: 50%;
            margin-right: 10px;
            width: 40px;
            height: 40px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="form-inline my-2 my-lg-0 ml-auto position-relative" method="GET" action="{{ route('user.search') }}">
                <input class="form-control mr-sm-2" type="search" id="search" name="query" placeholder="Cari pengguna" aria-label="Cari">
                <div id="search-results" class="search-results"></div>
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Cari</button>
            </form>
            <ul class="navbar-nav ml-auto">
                @auth
                    <!-- Tombol Settings -->
                    <li class="nav-item">
                        <a class="nav-link settings-btn" href="{{ route('settings') }}">
                            <i class="fas fa-cog"></i>
                        </a>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <div class="container mt-5 mb-5"> <!-- Margin bawah ditambahkan untuk jarak -->
        @yield('content')
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
        </a>
        <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.index') ? 'active' : '' }}">
            <i class="fas fa-comments"></i>
        </a>
        <a href="{{ route('post.create') }}" class="{{ request()->routeIs('post.create') ? 'active' : '' }}">
            <i class="fas fa-plus"></i>
        </a>
        <a href="{{ route('user.myprofile') }}" class="{{ request()->routeIs('user.myprofile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('input', function() {
                const query = $(this).val();
                if (query.length >= 2) { // Cek panjang query minimal 2 karakter
                    $.ajax({
                        url: '{{ route('user.search') }}',
                        type: 'GET',
                        data: { query: query },
                        success: function(response) {
                            let results = '';
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(user => {
                                    results += `
                                        <div class="result-item">
                                            <img src="${user.profile_photo_url}" alt="${user.name}">
                                            <span>${user.name}</span>
                                        </div>
                                    `;
                                });
                                $('#search-results').html(results).show();
                            } else {
                                $('#search-results').html('<div class="result-item">Tidak ada hasil ditemukan</div>').show();
                            }
                        },
                        error: function() {
                            $('#search-results').html('<div class="result-item">Terjadi kesalahan</div>').show();
                        }
                    });
                } else {
                    $('#search-results').empty().hide();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search, #search-results').length) {
                    $('#search-results').empty().hide();
                }
            });
        });
    </script>
</body>
</html>
