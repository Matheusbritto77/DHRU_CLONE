<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('SERVER Services') }}
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
            color: white; /* Cor do link para serviços */
            cursor: pointer; /* Altera o cursor para indicar que é clicável */
            text-decoration: none; /* Sem sublinhado nos links */
            transition: color 0.3s ease; /* Transição suave de cor ao passar o mouse */
            
        }

        .service-link:hover {
            color: green; /* Cor do link ao passar o mouse */
        }

        .service-table th,
        .service-table td {
            padding: 12px 20px; /* Espaçamento interno das células da tabela */
            text-align: left; /* Alinhamento do texto à esquerda */
        }

        .service-table th {
            background-color: grey; /* Cor de fundo do cabeçalho da tabela */
            font-size: 0.9rem; /* Tamanho da fonte do cabeçalho da tabela */
            color: black; /* Cor do texto do cabeçalho da tabela */
            border-bottom: 2px solid #cbd5e0; /* Linha divisória abaixo do cabeçalho da tabela */
        }

        .service-table td {
            border-bottom: 1px solid #cbd5e0; /* Linha divisória entre as células */
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

        /* Media query para rolagem horizontal em telas pequenas */
        @media screen and (max-width: 640px) {
            .service-table {
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }

            .service-table th,
            .service-table td {
                min-width: 150px;
                padding: 10px;
            }
        }

        /* Estilos para os valores de tempo e custo */
        .service-table td:nth-child(2),
        .service-table td:nth-child(3) {
            color: white; /* Cor branca para os valores de tempo e custo */
        }
    </style>

    <div class="container mx-auto py-8">
        <h1 class="mb-4 text-2xl text-white">{{ __('Select Server Service') }}</h1>

        <div class="search-group-container">
            <div class="relative">
                <label for="search-input" class="form-label text-white">{{ __('Search Server Services') }}</label>
                <input type="text" id="search-input" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search services...">
            </div>

            <div class="relative">
                <label for="group-filter" class="form-label text-white">{{ __('Filter by Group') }}</label>
                <select id="group-filter" class="form-select block w-full py-2 px-3 border border-black-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black">
                    <option value="">All Groups</option>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>

        <table class="min-w-full bg-white border border-white-300 service-table">
            <thead>
                <tr>
                    <th class="py-2 px-4">{{ __('Service Name') }}</th>
                    <th class="py-2 px-4">{{ __('Time') }}</th>
                    <th class="py-2 px-4">{{ __('Cost') }}</th>
                </tr>
            </thead>
            <tbody id="service-list">
                <!-- Dynamic rows will be appended here -->
            </tbody>
        </table>
    </div>


    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const searchInput = $('#search-input');
            const serviceList = $('#service-list');
            const groupFilter = $('#group-filter');

            // Example data to simulate server response
            const filteredServices = {!! json_encode($filteredServices) !!};

            function renderServices(services) {
                serviceList.empty();
                if (services.length > 0) {
                    let currentGroup = null;

                    services.forEach(function(service) {
                        // Check if the current service belongs to a new group
                        if (currentGroup !== service.GROUPNAME) {
                            // Render group header
                            const groupHeaderRow = $('<tr></tr>')
                                .append($('<td colspan="3" class="py-2 px-4 group-header"></td>').text(service.GROUPNAME));
                            serviceList.append(groupHeaderRow);

                            // Update current group
                            currentGroup = service.GROUPNAME;
                        }

                        // Builds the table row with all three <td> nested correctly (sem o SERVICEID)
                        const serviceRow = $('<tr></tr>')
                            .append($('<td class="py-2 px-4 service-link"></td>')
                                .text(service.SERVICENAME)
                                .data('service', service)
                            )
                            .append($('<td class="py-2 px-4"></td>').text(service.TIME))
                            .append($('<td class="py-2 px-4"></td>').text('$' + service.CREDIT));

                        serviceList.append(serviceRow);
                    });

                    // Click handler for service links
                    $('.service-link').click(function(e) {
                        e.preventDefault();
                        const serviceData = $(this).data('service');
                        console.log('Service Data:', serviceData);

                        // Check if fieldname is not empty
                        if (serviceData.fieldname) {
                            // Open Modal with Service Data
                            openModal(serviceData);
                        } else {
                            // Optionally, provide feedback that the modal cannot be opened for this service
                            console.log('Modal cannot be opened for this service:', serviceData);
                        }
                    });
                } else {
                    const noResultRow = $('<tr></tr>')
                        .append($('<td colspan="3" class="py-2 px-4 text-center"></td>').text('No services found'));
                    serviceList.append(noResultRow);
                }
            }

            // Function to populate the group filter dropdown
            function populateGroupFilter(services) {
                const groups = [...new Set(services.map(service => service.GROUPNAME))];
                const options = groups.map(group => `<option value="${group}">${group}</option>`);
                groupFilter.append(options);
            }

            // Function to load services immediately on page load
            renderServices(filteredServices);
            populateGroupFilter(filteredServices);

            // Event listener for input changes in the search field
            searchInput.on('input', function() {
                const query = $(this).val().trim().toLowerCase();
                const filtered = filteredServices.filter(service =>
                    service.SERVICENAME.toLowerCase().includes(query)
                );

                // Filter by selected group if a group is selected
                const selectedGroup = groupFilter.val();
                const filteredByGroup = selectedGroup ? filtered.filter(service => service.GROUPNAME === selectedGroup) : filtered;

                renderServices(filteredByGroup);
            });

            // Event listener for changes in the group filter dropdown
            groupFilter.on('change', function() {
                const selectedGroup = $(this).val();
                const filteredByGroup = selectedGroup ? filteredServices.filter(service => service.GROUPNAME === selectedGroup) : filteredServices;
                renderServices(filteredByGroup);
            });
        });

        
    </script>

    @include('modal.form')
</x-app-layout>
