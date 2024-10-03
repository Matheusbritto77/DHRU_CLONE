<!DOCTYPE html>
<html lang="pt-br">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Br server </title>
    
    <!-- Incluir Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Incluir Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Incluir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #343a40;
            color: #ffffff;
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .login-section {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e9ecef;
            height: calc(100vh - 100px);
        }
        .login-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-card .card-body {
            padding: 30px;
        }
        .login-card .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            text-align: center;
            padding: 15px;
        }
        footer {
            background-color: #343a40;
            color: #ffffff;
            padding: 10px 0;
            text-align: center;
            width: 100%;
            border-top: 1px solid #dee2e6;
            margin-top: auto;
        }
        .footer-icons {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    
    <!-- Top Bar -->
    <div class="bg-gray-700 text-sm py-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-4 lg:px-2">
            <div class="flex justify-between items-center">
                <div>
                    <span>💻 <a href="mailto:contato@brserver.tech" class="underline text-blue-400 hover:text-blue-600">contato@brserver.tech</a></span>
                </div>
                <div>
                    <span> 📲 <a href="https://wa.me/5534999442627" class="underline text-blue-400 hover:text-blue-600">+55(34)999442627</a></span>
                </div>
            </div>
        </div>
    </div>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo3.jpg" class="block h-9 w-auto" alt="Your Logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        @if (Route::has('register'))
                            <a class="nav-link" href="{{ route('register') }}">Registrar</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Carrossel de Imagens -->
<div id="carouselExample" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="image/imagem1.png" class="d-block w-100" alt="Imagem 1">
        </div>
        <div class="carousel-item">
            <img src="image/imagem2.png" class="d-block w-100" alt="Imagem 2">
        </div>
        <div class="carousel-item">
            <img src="image/imagem3.png" class="d-block w-100" alt="Imagem 3">
        </div>
        <!-- Adicione mais itens conforme necessário -->
    </div>
    <!-- Controles de navegação -->
    <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<section class="login-section">
    <div class="login-card">
        <div class="text-center mb-4">
            
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus class="block mt-1 w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') is-invalid @enderror">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="block mt-1 w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') is-invalid @enderror">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <input class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            {{ __('Remember me') }}
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="inline-block bg-indigo-500 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-600">{{ __('Login') }}</button>
                </div>
            </form>
        </div>
       
    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <!-- Footer Icons -->
    <div class="footer-icons">
        <a href="#" class="animate__animated animate__bounceIn"><i class="fab fa-facebook"></i></a>
        <a href="#" class="animate__animated animate__bounceIn animate__delay-1s"><i class="fab fa-twitter"></i></a>
        <a href="#" class="animate__animated animate__bounceIn animate__delay-2s"><i class="fab fa-linkedin"></i></a>
        <!-- Adicione mais ícones conforme necessário -->
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <img src="logo3.jpg" class="block h-9 w-auto" alt="Your Logo">
            </div>
            <div class="col-md-4">
                <div class="footer-subscribe">
                    <h4>Nos siga </h4>
                    <p>fique por dentro das atualizacoes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="footer-connect">
                    <h4>contatos</h4>
                    <a href="" target="_blank"><i class="fab fa-telegram"></i></a>
                    <a href="" target="_blank"><i class="far fa-envelope"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Incluir Bootstrap JS e dependências -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Script para o Carrossel -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('.carousel-inner');
        const slides = document.querySelectorAll('.carousel-item');
        const totalSlides = slides.length;
        let slideIndex = 0;

        function showSlide(n) {
            slides.forEach((slide) => {
                slide.classList.remove('active');
            });
            slides[n].classList.add('active');
        }

        function nextSlide() {
            slideIndex = (slideIndex + 1) % totalSlides;
            showSlide(slideIndex);
        }

        function prevSlide() {
            slideIndex = (slideIndex - 1 + totalSlides) % totalSlides;
            showSlide(slideIndex);
        }

        document.querySelector('.carousel-control-prev').addEventListener('click', () => {
            prevSlide();
        });

        document.querySelector('.carousel-control-next').addEventListener('click', () => {
            nextSlide();
        });

        setInterval(nextSlide, 3000); // Intervalo de 3 segundos para trocar automaticamente os slides
    });
</script>

</body>
</html>
