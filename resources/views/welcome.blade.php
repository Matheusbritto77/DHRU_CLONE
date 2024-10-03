<!DOCTYPE html>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<html lang="pt-br">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport responsivo -->
    <title>BR Server</title>
    
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
            background-color: #f8f9fa; /* Cor de fundo suave */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Garante que o conteúdo ocupe toda a altura da viewport */
        }
        header {
            background-color: #343a40; /* Cor de fundo do cabeçalho */
            color: #ffffff; /* Cor do texto no cabeçalho */
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6; /* Adiciona uma borda suave na parte inferior do cabeçalho */
        }
        .carousel {
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        .carousel-track {
            display: flex;
            transition: transform 0.5s ease;
        }
        .carousel-track img {
            width: 100%; /* Ajusta a largura para ocupar 100% do container */
            height: auto; /* Altura ajustada automaticamente para manter a proporção */
        }
        .navbar {
            justify-content: space-between; /* Alinha os itens do navbar */
        }
        .navbar-brand {
            margin-right: auto; /* Empurra a marca para a esquerda */
        }
        .buttons .btn {
            background-color: #007bff; /* Cor de fundo azul para botões */
            border-color: #007bff; /* Cor da borda azul */
            color: #ffffff; /* Cor do texto dos botões */
        }
        .buttons .btn:hover {
            background-color: #0056b3; /* Cor de fundo azul mais escura ao passar o mouse */
            border-color: #0056b3; /* Cor da borda azul mais escura ao passar o mouse */
        }
        .social-icons {
            text-align: center;
            margin-top: auto; /* Faz com que os ícones fiquem no final da página */
            margin-bottom: 20px; /* Espaçamento para o rodapé */
        }
        .social-icons a {
            display: inline-block;
            margin: 0 10px;
            color: #343a40;
            font-size: 24px;
        }
        .social-icons a:hover {
            opacity: 0.8;
        }
        .message-container {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 20px; /* Espaçamento para o rodapé */
        }
        .message {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: 100%; /* Garante que todos os containers de mensagem tenham a mesma altura */
        }
        footer {
            background-color: #343a40; /* Cor de fundo do rodapé */
            color: #ffffff; /* Cor do texto no rodapé */
            padding: 10px 0;
            text-align: center;
            width: 100%;
            border-top: 1px solid #dee2e6; /* Adiciona uma borda suave na parte superior do rodapé */
        }
        .footer-icons {
            margin-top: 20px; /* Espaçamento para os ícones */
            margin-bottom: 20px; /* Espaçamento para o rodapé */
        }
        .modal-content {
            background-color: #343a40; /* Fundo escuro */
            color: #ffffff; /* Texto branco */
            border-radius: 10px; /* Borda arredondada */
        }
        .modal-header {
            border-bottom: 1px solid #454d55; /* Bordas mais escuras */
        }
        .modal-footer {
            border-top: 1px solid #454d55; /* Bordas mais escuras */
        }
        .modal-body {
            padding: 30px; /* Espaçamento interno */
        }
        .modal-title {
            color: #ffffff; /* Texto branco */
        }

        .modal-dialog {
            margin-top: 100px; /* Ajuste a margem superior para centralizar verticalmente */
        }
    </style>
<body>
    
<div>
 
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
            <a class="navbar-brand flex items-center justify-center" href="#">
    <img src="logo3.jpg" class="block h-16 w-auto" alt="Your Logo">
</a>


            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                          <a class="nav-link" href="#" data-toggle="modal" data-target="#loginModal">Login</a>
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
    


<!-- Redes Sociais -->
<div id="redes-sociais" class="social-icons">
    <a href="#" target="_blank" class="animate__animated animate__bounceIn"><i class="fab fa-whatsapp"></i></a>
    <a href="#" target="_blank" class="animate__animated animate__bounceIn animate__delay-1s"><i class="fab fa-instagram"></i></a>
    <a href="#" target="_blank" class="animate__animated animate__bounceIn animate__delay-2s"><i class="fab fa-telegram"></i></a>
    <!-- Adicione mais ícones conforme necessário -->
</div>

<!-- Containers de Mensagens -->
<div id="mensagens" class="message-container">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="message animate__animated animate__fadeInLeft">
                    <h3>AUTO API</h3>
                    <p>🌟 Bem-vindo! 🌟

Olá! Estamos aqui para oferecer serviços incríveis com API automática 24 horas por dia! 🕒✨

Na nossa plataforma, você encontra tudo o que precisa de forma rápida e eficiente, com integração contínua e suporte ininterrupto. 🚀💻

Estamos prontos para transformar suas operações com tecnologia avançada e confiável. Não perca tempo, venha descobrir como podemos facilitar o seu dia a dia! 🌐🔧

Conte conosco para soluções que fazem a diferença! 💡💼

</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="message animate__animated animate__fadeInUp">
                    <h3>🌟 Suporte Rápido 24 Horas Por Dia 🌟</h3>
                    <p>Precisa de ajuda imediata? Estamos aqui para você! 🕒💬

Nosso suporte funciona 24 horas por dia, todos os dias da semana, para resolver suas dúvidas e problemas com agilidade e eficiência. 🚀🔧

Não importa a hora ou o dia, nossa equipe está pronta para garantir que você receba a assistência que merece, sempre com um atendimento ágil e personalizado. 💼🌐

Conte conosco para manter suas operações funcionando sem interrupções! 💪🔍</div>
            </div>
            <div class="col-md-4">
                <div class="message animate__animated animate__fadeInRight">
                    <h3>🌟 Melhores Preços Para Revendedores 🌟</h3>
                    <p>Atenção, revendedores! Temos ofertas especiais esperando por você! 💼💲

Na nossa plataforma, garantimos os melhores preços para revendedores que buscam qualidade e economia. 🛍️💰

Oferecemos condições exclusivas e descontos competitivos para que você possa maximizar seus lucros e satisfazer seus clientes. 🚀✨

Não perca essa oportunidade de fazer negócio conosco! Entre em contato hoje mesmo e descubra como podemos ajudar o seu negócio a crescer! 📈🌐</div>
            </div>
        </div>
    </div>
</div>

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
@include('auth.login2')
</html>
