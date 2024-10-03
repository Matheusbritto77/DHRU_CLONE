@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold mb-4">Novidades do Sistema</h1>

        <!-- Conteúdo da página de novidades -->
        <p>Aqui você encontra as últimas novidades do sistema.</p>
    </div>

    <!-- Script do SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verifica se o cookie já foi definido para não exibir novamente
            if (!Cookies.get('novidades_exibidas')) {
                // Exibe o SweetAlert2 com as novidades
                Swal.fire({
                    title: 'Novidades do Sistema!',
                    html: '<p>Aqui estão as últimas novidades...</p>',
                    icon: 'info',
                    confirmButtonText: 'Entendi!',
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg',
                        title: 'text-gray-900 dark:text-white font-semibold text-lg',
                        htmlContainer: 'mt-4',
                        confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md focus:outline-none focus:bg-blue-600',
                    },
                    buttonsStyling: false // Desabilita estilização padrão do SweetAlert2 para usar classes do Tailwind CSS
                });

                // Define o cookie para expirar em 10 segundos
                Cookies.set('novidades_exibidas', true, { expires: 0.0001 }); // 10 segundos em dias
            }
        });

        // PHP integrado com JavaScript para verificar se o usuário está autenticado
        <?php if(auth()->check()): ?>
            // Exibir popup de novidades se o cookie não estiver definido
            if (!Cookies.get('novidades_exibidas')) {
                Swal.fire({
                    title: 'Novidades do Sistema!',
                    html: '<p>Aqui estão as últimas novidades...</p>',
                    icon: 'info',
                    confirmButtonText: 'Entendi!',
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg',
                        title: 'text-gray-900 dark:text-white font-semibold text-lg',
                        htmlContainer: 'mt-4',
                        confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md focus:outline-none focus:bg-blue-600',
                    },
                    buttonsStyling: false // Desabilita estilização padrão do SweetAlert2 para usar classes do Tailwind CSS
                });

                // Define o cookie para expirar em 10 segundos
                Cookies.set('novidades_exibidas', true, { expires: 0.0001 }); // 10 segundos em dias
            }
        <?php endif; ?>
    </script>
@endsection