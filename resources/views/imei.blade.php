<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('IMEI Services') }}
        </h2>
    </x-slot>

    <style>
        /* Estilos CSS para a tabela e serviços */
        .group-header {
            background-color: grey; /* Cor de fundo do cabeçalho do grupo */
            font-size: 1.1rem; /* Tamanho da fonte do nome do grupo */
            font-weight: bold; /* Negrito para destacar o nome do grupo */
            padding: 10px 20px; /* Espaçamento interno do cabeçalho do grupo */
            border-bottom: 2px solid #cbd5e0; /* Linha divisória abaixo do cabeçalho do grupo */
        }

        .service-link {
            color: #3182ce; /* Cor do link para serviços */
            cursor: pointer; /* Altera o cursor para indicar que é clicável */
            text-decoration: none; /* Sem sublinhado nos links */
            transition: color 0.3s ease; /* Transição suave de cor ao passar o mouse */
        }

        .service-link:hover {
            color: green; /* Cor do link ao passar o mouse */
        }

        .service-table-container {
            margin-bottom: 20px; /* Espaçamento inferior para separar da próxima seção */
            overflow-x: auto; /* Adiciona rolagem horizontal */
        }

        .service-table {
            width: 100%; /* Ocupa toda a largura disponível */
            border-collapse: collapse; /* Colapso de bordas para melhor aparência */
        }

        .service-table th,
        .service-table td {
            padding: 8px; /* Espaçamento interno das células da tabela reduzido */
            text-align: left; /* Alinhamento do texto à esquerda */
            border-bottom: 1px solid #cbd5e0; /* Linha divisória entre as células */
            color: white; /* Cor do texto preto */
        }

        .service-table th {
            background-color: grey; /* Cor de fundo do cabeçalho da tabela */
            font-size: 0.9rem; /* Tamanho da fonte do cabeçalho da tabela */
            font-weight: bold; /* Negrito para os cabeçalhos da tabela */
        }

        /* Estilos para alinhar os campos de busca e filtro lado a lado */
        .search-group-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .search-group-container .relative {
            flex: 1;
            margin-right: 1rem;
        }

        .form-label {
            color: #ffffff; /* Cor do texto branco para rótulos */
        }

        .form-input,
        .form-select {
            color: #333; /* Cor do texto do input/select */
        }

        /* Media query para telas menores */
        @media (max-width: 768px) {
            .service-table-container {
                overflow-x: auto; /* Adiciona rolagem horizontal em telas menores */
            }
        }
    </style>
    
    <div class="container mx-auto py-8">
        <h1 class="mb-4 text-2xl text-white">{{ __('Select IMEI Service') }}</h1>

        <div class="search-group-container">
            <div class="relative">
                <label for="search-input" class="form-label">{{ __('Search IMEI Services') }}</label>
                <input type="text" id="search-input" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search services...">
            </div>

            <div class="relative">
                <label for="group-filter" class="form-label">{{ __('Filter by Group') }}</label>
                <select id="group-filter" class="form-select block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Groups</option>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>

        <div class="service-table-container">
            <table class="min-w-full bg-white border border-gray-300 service-table">
                <thead>
                    <tr>
                        <th class="py-2 px-3">{{ __('Service Name') }}</th>
                        <th class="py-2 px-3">{{ __('Time') }}</th>
                        <th class="py-2 px-3">{{ __('Cost') }}</th>
                    </tr>
                </thead>
                <tbody id="service-list">
                    <!-- Dynamic rows will be appended here -->
                </tbody>
            </table>
        </div>
    </div>


   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        const searchInput = $('#search-input');
        const serviceList = $('#service-list');
        const groupFilter = $('#group-filter');

        // Dados de exemplo para simular resposta do servidor
        const filteredServices = {!! json_encode($filteredServices) !!};

        function renderServices(services) {
            serviceList.empty();
            if (services.length > 0) {
                let currentGroup = null;

                services.forEach(function(service) {
                    // Verifica se o serviço atual pertence a um novo grupo
                    if (currentGroup !== service.GROUPNAME) {
                        // Renderiza o cabeçalho do grupo
                        const groupHeaderRow = $('<tr></tr>')
                            .append($('<td colspan="3" class="py-2 px-4 group-header"></td>').text(service.GROUPNAME));
                        serviceList.append(groupHeaderRow);

                        // Atualiza o grupo atual
                        currentGroup = service.GROUPNAME;
                    }

                    // Constrói a linha da tabela com todos os três <td> aninhados corretamente (sem o SERVICEID)
                    const serviceRow = $('<tr></tr>')
                        .append($('<td class="py-2 px-3 service-link"></td>')
                            .text(service.SERVICENAME)
                            .data('service', service)
                        )
                        .append($('<td class="py-2 px-3"></td>').text(service.TIME))
                        .append($('<td class="py-2 px-3"></td>').text('$' + service.CREDIT));

                    serviceList.append(serviceRow);
                });

                // Manipulador de clique para links de serviço
                $('.service-link').click(function(e) {
                    e.preventDefault();
                    const serviceData = $(this).data('service');
                    console.log('Service Data:', serviceData);

                    // Abre o modal com os dados do serviço
                    openModal(serviceData);
                });
            } else {
                const noResultRow = $('<tr></tr>')
                    .append($('<td colspan="3" class="py-2 px-4 text-center"></td>').text('No services found'));
                serviceList.append(noResultRow);
            }
        }

        // Função para preencher o dropdown de filtro de grupo
        function populateGroupFilter(services) {
            const groups = [...new Set(services.map(service => service.GROUPNAME))];
            const options = groups.map(group => `<option value="${group}">${group}</option>`);
            groupFilter.append(options);
        }

        // Função para carregar serviços imediatamente ao carregar a página
        renderServices(filteredServices);
        populateGroupFilter(filteredServices);

        // Listener para alterações de input no campo de busca
        searchInput.on('input', function() {
            const query = $(this).val().trim().toLowerCase();
            const filtered = filteredServices.filter(service =>
                service.SERVICENAME.toLowerCase().includes(query)
            );

            // Filtra pelo grupo selecionado, se um grupo estiver selecionado
            const selectedGroup = groupFilter.val();
            const filteredByGroup = selectedGroup ? filtered.filter(service => service.GROUPNAME === selectedGroup) : filtered;

            renderServices(filteredByGroup);
        });

        // Listener para alterações no dropdown de filtro de grupo
        groupFilter.on('change', function() {
            const selectedGroup = $(this).val();
            const filteredByGroup = selectedGroup ? filteredServices.filter(service => service.GROUPNAME === selectedGroup) : filteredServices;
            renderServices(filteredByGroup);
        });
    });
</script>

@include('modal.sn')
</x-app-layout>
