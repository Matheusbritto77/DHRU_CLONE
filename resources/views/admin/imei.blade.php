<x-app-layout layout="app2">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-apple-dark dark:text-white leading-tight tracking-tight">
                {{ __('Serviços IMEI') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto">
        
        <!-- Search and Filter Bar -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="search-input" class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-[#1d1d1f] border border-gray-200 dark:border-gray-800 text-apple-dark dark:text-gray-100 text-[15px] rounded-[16px] shadow-sm focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" placeholder="{{ __('Buscar Serviço de IMEI...') }}">
            </div>

            <div class="relative">
                <select id="group-filter" class="w-full pl-4 pr-11 py-3.5 bg-white dark:bg-[#1d1d1f] border border-gray-200 dark:border-gray-800 text-apple-dark dark:text-gray-100 text-[15px] rounded-[16px] shadow-sm focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none appearance-none">
                    <option value="">{{ __('Todos os Grupos') }}</option>
                </select>
                <div class="absolute inset-y-0 right-0 top-0 pr-4 flex items-center pointer-events-none h-full">
                    <i class="fas fa-chevron-down text-gray-400 text-[12px]"></i>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white dark:bg-[#1d1d1f] shadow-[0_8px_30px_rgba(0,0,0,0.03)] dark:shadow-[0_8px_30px_rgba(0,0,0,0.2)] rounded-[24px] overflow-hidden border border-gray-100 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-black/20 border-b border-gray-100 dark:border-gray-800/80">
                            <th class="py-4 px-6 text-[12px] font-semibold text-gray-500 uppercase tracking-widest">{{ __('Nome do Serviço') }}</th>
                            <th class="py-4 px-6 text-[12px] font-semibold text-gray-500 uppercase tracking-widest">{{ __('Tempo Médio') }}</th>
                            <th class="py-4 px-6 text-[12px] font-semibold text-gray-500 uppercase tracking-widest text-right">{{ __('Custo') }}</th>
                        </tr>
                    </thead>
                    <tbody id="service-list" class="divide-y divide-gray-50 dark:divide-gray-800/40">
                        <!-- Dynamic rows will be appended here via jQuery -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts do jQuery refatorado com Tailwind Classes na injeção -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const searchInput = $('#search-input');
            const serviceList = $('#service-list');
            const groupFilter = $('#group-filter');

            const filteredServices = {!! json_encode($filteredServices ?? []) !!};

            function renderServices(services) {
                serviceList.empty();
                if (services.length > 0) {
                    let currentGroup = null;

                    services.forEach(function(service) {
                        if (currentGroup !== service.GROUPNAME) {
                            const groupHeaderRow = $('<tr></tr>')
                                .append($('<td colspan="3" class="py-3 px-6 bg-gray-50/80 dark:bg-[#161617]/80 text-[13px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider border-t border-b border-gray-100 dark:border-gray-800"></td>').text(service.GROUPNAME));
                            serviceList.append(groupHeaderRow);
                            currentGroup = service.GROUPNAME;
                        }

                        const serviceRow = $('<tr class="hover:bg-gray-50/80 dark:hover:bg-white/[0.04] transition-colors cursor-pointer group"></tr>')
                            .append($('<td class="py-4 px-6 text-[15px] font-medium text-apple-dark dark:text-gray-200 group-hover:text-apple-blue dark:group-hover:text-blue-400 transition-colors service-link"></td>')
                                .text(service.SERVICENAME)
                                .data('service', service)
                            )
                            .append($('<td class="py-4 px-6 text-[14px] text-gray-500 dark:text-gray-400"></td>').text(service.TIME))
                            .append($('<td class="py-4 px-6 text-[14px] font-medium text-green-600 dark:text-green-400 text-right"></td>').text('$' + service.CREDIT));

                        // Clique na linha
                        serviceRow.click(function(e) {
                            e.preventDefault();
                            const serviceData = $(this).find('.service-link').data('service');
                            if(typeof openModal === 'function') openModal(serviceData);
                        });

                        serviceList.append(serviceRow);
                    });
                } else {
                    const noResultRow = $('<tr></tr>')
                        .append($('<td colspan="3" class="py-16 px-6 text-center text-gray-500 dark:text-gray-400 font-light"></td>').html('<i class="fas fa-search text-3xl mb-3 opacity-20 block"></i>Nenhum serviço encontrado.'));
                    serviceList.append(noResultRow);
                }
            }

            function populateGroupFilter(services) {
                const groups = [...new Set(services.map(service => service.GROUPNAME))];
                const options = groups.map(group => `<option value="${group}">${group}</option>`);
                groupFilter.append(options);
            }

            if(filteredServices) {
                renderServices(filteredServices);
                populateGroupFilter(filteredServices);
            }

            searchInput.on('input', function() {
                const query = $(this).val().trim().toLowerCase();
                const filtered = filteredServices.filter(service =>
                    (service.SERVICENAME || '').toLowerCase().includes(query)
                );
                const selectedGroup = groupFilter.val();
                const filteredByGroup = selectedGroup ? filtered.filter(service => service.GROUPNAME === selectedGroup) : filtered;
                renderServices(filteredByGroup);
            });

            groupFilter.on('change', function() {
                const selectedGroup = $(this).val();
                const filteredByGroup = selectedGroup ? filteredServices.filter(service => service.GROUPNAME === selectedGroup) : filteredServices;
                renderServices(filteredByGroup);
            });
        });
    </script>
    
    @includeIf('admin.modal.sn')
</x-app-layout>
