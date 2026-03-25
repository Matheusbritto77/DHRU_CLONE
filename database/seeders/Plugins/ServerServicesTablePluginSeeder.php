<?php

namespace Database\Seeders\Plugins;

class ServerServicesTablePluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'server-services-table';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Tabela Server Services',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-table-cells',
            'description' => 'Catalogo server com busca, filtro, cards e modal de pedido.',
            'default_settings' => [
                'search_label' => 'Buscar servicos',
                'search_placeholder' => 'Pesquisar por nome do servico...',
                'filter_label' => 'Filtrar por grupo',
                'table_title' => 'Catalogo Server',
                'view_label' => 'Visualizacao',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 py-6 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="rounded-[32px] p-6 md:p-8 theme-panel">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['table_title'] }}</p>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Escolha o servico server do jeito que ficar melhor para voce.</h2>
                </div>
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:min-w-[620px]">
                    <div class="flex-1">
                        <label for="server-search-input" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['search_label'] }}</label>
                        <input type="text" id="server-search-input" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" placeholder="{{ $settings['search_placeholder'] }}">
                    </div>
                    <div class="md:w-[220px]">
                        <label for="server-group-filter" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['filter_label'] }}</label>
                        <select id="server-group-filter" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text">
                            <option value="">Todos os grupos</option>
                        </select>
                    </div>
                    <div class="md:w-[180px]">
                        <span class="mb-2 block text-sm font-medium theme-muted">{{ $settings['view_label'] }}</span>
                        <div class="grid grid-cols-2 rounded-[18px] p-1 theme-soft">
                            <button type="button" id="server-view-list" class="rounded-[14px] px-4 py-3 text-sm font-medium transition">Lista</button>
                            <button type="button" id="server-view-cards" class="rounded-[14px] px-4 py-3 text-sm font-medium transition">Cards</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="server-table-view" class="mt-8 overflow-hidden rounded-[28px] border" style="border-color: var(--theme-border);">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead style="background: var(--theme-surface-soft);">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Servico</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Tempo</th>
                                <th class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Custo</th>
                            </tr>
                        </thead>
                        <tbody id="server-service-list"></tbody>
                    </table>
                </div>
            </div>

            <div id="server-cards-view" class="mt-8 hidden">
                <div id="server-service-cards" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"></div>
            </div>
        </div>
    </div>
</section>

<div class="fixed inset-0 z-50 hidden items-center justify-center px-4" id="server-form-modal">
    <div class="absolute inset-0 bg-black/45 backdrop-blur-sm" onclick="closeServerModal()"></div>
    <div class="relative w-full max-w-2xl rounded-[32px] p-6 md:p-8 theme-panel-strong">
        <form action="{{ route('submit_server') }}" method="POST" id="server-serial-form">
            @csrf
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.28em] theme-muted">Novo pedido</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Detalhes do servico</h3>
                </div>
                <button type="button" class="h-11 w-11 rounded-full theme-soft theme-text" onclick="closeServerModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Servico</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="server-service-name"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Tempo</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="server-time"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Custo</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="server-cost-display"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Service ID</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="server-service-id-display"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft md:col-span-2">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Fornecedor</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="server-provider-display"></p>
                </div>
            </div>

            <div id="server-quantity-wrapper" class="mt-6 hidden">
                <label for="server-quantity" class="mb-2 block text-sm font-medium theme-muted">Quantidade</label>
                <p class="mb-2 text-sm theme-muted">Minimo: <span id="server-min-qnt"></span>, Maximo: <span id="server-max-qnt"></span></p>
                <input type="number" id="server-quantity" name="Qnt" min="1" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" placeholder="Digite a quantidade">
            </div>

            <div id="server-dynamic-fields-section" class="mt-6 space-y-4"></div>

            <input type="hidden" id="server-service-id" name="SERVICEID">
            <input type="hidden" id="server-provider-id" name="PROVIDER_ID">
            <input type="hidden" id="server-catalog-service-id" name="CATALOG_SERVICE_ID">
            <input type="hidden" id="server-cost" name="COST">
            <input type="hidden" id="server-base-cost" value="">
            <input type="hidden" id="server-service-name-input" name="servicename">
            <input type="hidden" name="username" value="{{ Auth::user()->name }}">

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="button" class="rounded-full px-5 py-3 text-sm font-medium theme-soft theme-text" onclick="closeServerModal()">Cancelar</button>
                <button type="submit" class="rounded-full px-5 py-3 text-sm font-medium shadow-sm theme-accent-bg">Enviar pedido</button>
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serverFilteredServices = {!! json_encode($filteredServices ?? []) !!};
    console.log('Server Services rendering:', serverFilteredServices.length, 'items');

    const serverSearchInput = document.getElementById('server-search-input');
    const serverGroupFilter = document.getElementById('server-group-filter');
    const serverServiceList = document.getElementById('server-service-list');
    const serverServiceCards = document.getElementById('server-service-cards');
    const serverTableView = document.getElementById('server-table-view');
    const serverCardsView = document.getElementById('server-cards-view');
    const serverViewListButton = document.getElementById('server-view-list');
    const serverViewCardsButton = document.getElementById('server-view-cards');
    const serverViewStorageKey = 'server-services-view-mode';
    let serverCurrentView = localStorage.getItem(serverViewStorageKey) || 'list';

    function serverEscapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function populateServerGroupFilter(services) {
        if(!serverGroupFilter) return;
        const groups = [...new Set(services.map((service) => service.GROUPNAME))];
        groups.forEach((group) => {
            const option = document.createElement('option');
            option.value = group;
            option.textContent = group;
            serverGroupFilter.appendChild(option);
        });
    }

    function renderServerServices(services) {
        if(!serverServiceList || !serverServiceCards) return;
        serverServiceList.innerHTML = '';
        serverServiceCards.innerHTML = '';

        if (!services.length) {
            serverServiceList.innerHTML = '<tr><td colspan="3" class="px-6 py-12 text-center text-sm theme-muted">Nenhum servico encontrado.</td></tr>';
            serverServiceCards.innerHTML = '<div class="rounded-[28px] p-8 text-center text-sm theme-panel theme-muted md:col-span-2 xl:col-span-3">Nenhum servico encontrado.</div>';
            return;
        }

        let currentGroup = null;
        let currentCardsGroup = null;

        services.forEach((service) => {
            if (currentGroup !== service.GROUPNAME) {
                currentGroup = service.GROUPNAME;
                const groupRow = document.createElement('tr');
                groupRow.innerHTML = `<td colspan="3" class="px-6 py-4 text-[12px] font-semibold uppercase tracking-[0.28em] theme-muted" style="background: var(--theme-surface-soft);">${serverEscapeHtml(service.GROUPNAME)}</td>`;
                serverServiceList.appendChild(groupRow);
            }

            const row = document.createElement('tr');
            row.className = 'cursor-pointer transition hover:opacity-90';
            row.style.borderTop = '1px solid var(--theme-border)';
            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-medium theme-text">${serverEscapeHtml(service.SERVICENAME)}</td>
                <td class="px-6 py-4 text-sm theme-muted">${serverEscapeHtml(service.TIME)}</td>
                <td class="px-6 py-4 text-right text-sm font-semibold theme-text">$${serverEscapeHtml(service.CREDIT)}<div class="mt-1 text-[11px] font-medium theme-muted">${serverEscapeHtml(service.PROVIDER_NAME ?? '')}</div></td>
            `;
            row.addEventListener('click', () => openServerModal(service));
            serverServiceList.appendChild(row);

            if (currentCardsGroup !== service.GROUPNAME) {
                currentCardsGroup = service.GROUPNAME;
                const groupCard = document.createElement('div');
                groupCard.className = 'rounded-[24px] px-5 py-4 theme-soft md:col-span-2 xl:col-span-3';
                groupCard.innerHTML = `<p class="text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">${serverEscapeHtml(service.GROUPNAME)}</p>`;
                serverServiceCards.appendChild(groupCard);
            }

            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'rounded-[28px] p-6 text-left transition theme-panel hover:opacity-95';
            card.innerHTML = `
                <p class="text-[11px] uppercase tracking-[0.24em] theme-muted">Servico</p>
                <h3 class="mt-3 text-lg font-semibold leading-7 theme-text">${serverEscapeHtml(service.SERVICENAME)}</h3>
                <div class="mt-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.24em] theme-muted">Tempo</p>
                        <p class="mt-2 text-sm theme-text">${serverEscapeHtml(service.TIME)}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] uppercase tracking-[0.24em] theme-muted">Custo</p>
                        <p class="mt-2 text-lg font-semibold theme-text">$${serverEscapeHtml(service.CREDIT)}</p>
                        <p class="mt-2 text-[11px] uppercase tracking-[0.2em] theme-muted">${serverEscapeHtml(service.PROVIDER_NAME ?? '')}</p>
                    </div>
                </div>
            `;
            card.addEventListener('click', () => openServerModal(service));
            serverServiceCards.appendChild(card);
        });
    }

    function applyServerViewMode(mode) {
        serverCurrentView = mode;
        localStorage.setItem(serverViewStorageKey, mode);

        if (mode === 'cards') {
            if(serverTableView) serverTableView.classList.add('hidden');
            if(serverCardsView) serverCardsView.classList.remove('hidden');
            if(serverViewCardsButton) {
                serverViewCardsButton.style.background = 'var(--theme-text)';
                serverViewCardsButton.style.color = 'var(--theme-bg)';
            }
            if(serverViewListButton) {
                serverViewListButton.style.background = 'transparent';
                serverViewListButton.style.color = 'var(--theme-text-muted)';
            }
        } else {
            if(serverTableView) serverTableView.classList.remove('hidden');
            if(serverCardsView) serverCardsView.classList.add('hidden');
            if(serverViewListButton) {
                serverViewListButton.style.background = 'var(--theme-text)';
                serverViewListButton.style.color = 'var(--theme-bg)';
            }
            if(serverViewCardsButton) {
                serverViewCardsButton.style.background = 'transparent';
                serverViewCardsButton.style.color = 'var(--theme-text-muted)';
            }
        }
    }

    function filterServerServices() {
        const query = (serverSearchInput?.value || '').trim().toLowerCase();
        const selectedGroup = serverGroupFilter?.value;

        const services = serverFilteredServices.filter((service) => {
            const matchesQuery = service.SERVICENAME.toLowerCase().includes(query);
            const matchesGroup = !selectedGroup || service.GROUPNAME === selectedGroup;
            return matchesQuery && matchesGroup;
        });

        renderServerServices(services);
    }

    function calculateServerCost() {
        const baseCost = parseFloat(document.getElementById('server-base-cost').value || 0);
        const quantity = parseInt(document.getElementById('server-quantity').value || 0, 10);
        const totalCost = quantity > 0 ? (baseCost * quantity) : baseCost;

        document.getElementById('server-cost').value = totalCost.toFixed(2);
        document.getElementById('server-cost-display').textContent = '$' + totalCost.toFixed(2);
    }

    function openServerModal(serviceData) {
        document.getElementById('server-service-name').textContent = serviceData.SERVICENAME ?? '';
        document.getElementById('server-service-name-input').value = serviceData.SERVICENAME ?? '';
        document.getElementById('server-time').textContent = serviceData.TIME ?? '';
        document.getElementById('server-base-cost').value = serviceData.CREDIT ?? '';
        document.getElementById('server-cost').value = parseFloat(serviceData.CREDIT ?? 0).toFixed(2);
        document.getElementById('server-cost-display').textContent = '$' + (parseFloat(serviceData.CREDIT ?? 0).toFixed(2));
        document.getElementById('server-service-id').value = serviceData.SERVICEID ?? '';
        document.getElementById('server-service-id-display').textContent = serviceData.SERVICEID ?? '';
        document.getElementById('server-provider-id').value = serviceData.PROVIDER_ID ?? '';
        document.getElementById('server-catalog-service-id').value = serviceData.CATALOG_SERVICE_ID ?? '';
        document.getElementById('server-provider-display').textContent = serviceData.PROVIDER_NAME ?? '';

        const quantityWrapper = document.getElementById('server-quantity-wrapper');
        const quantityInput = document.getElementById('server-quantity');
        if (parseInt(serviceData.MINQNT ?? 0, 10) > 0) {
            quantityWrapper.classList.remove('hidden');
            document.getElementById('server-min-qnt').textContent = serviceData.MINQNT ?? '';
            document.getElementById('server-max-qnt').textContent = serviceData.MAXQNT ?? '';
            quantityInput.min = serviceData.MINQNT ?? 1;
            quantityInput.max = serviceData.MAXQNT ?? '';
            quantityInput.value = serviceData.MINQNT ?? 1;
            calculateServerCost();
        } else {
            quantityWrapper.classList.add('hidden');
            quantityInput.value = '';
        }

        const dynamicFieldsSection = document.getElementById('server-dynamic-fields-section');
        dynamicFieldsSection.innerHTML = '';

        if (serviceData.fieldname) {
            serviceData.fieldname.split(',').map((field) => field.trim()).filter(Boolean).slice(0, 7).forEach((field) => {
                const inputName = field.toLowerCase().replace(/\s+/g, '_');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = `
                    <label for="${serverEscapeHtml(inputName)}" class="mb-2 block text-sm font-medium theme-muted">${serverEscapeHtml(field)}</label>
                    <input type="text" id="${serverEscapeHtml(inputName)}" name="${serverEscapeHtml(inputName)}" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" placeholder="Digite ${serverEscapeHtml(field).toLowerCase()}">
                `;
                dynamicFieldsSection.appendChild(wrapper);
            });
        }

        const modal = document.getElementById('server-form-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    window.closeServerModal = function() {
        const modal = document.getElementById('server-form-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    document.getElementById('server-quantity')?.addEventListener('input', calculateServerCost);

    document.getElementById('server-serial-form')?.addEventListener('submit', function (event) {
        event.preventDefault();

        const form = this;
        const formDataObject = {
            _token: form.querySelector('input[name=\"_token\"]').value,
            servicename: document.getElementById('server-service-name-input').value,
            COST: document.getElementById('server-cost').value,
            username: form.querySelector('input[name=\"username\"]').value,
            SERVICEID: document.getElementById('server-service-id').value,
            PROVIDER_ID: document.getElementById('server-provider-id').value,
            CATALOG_SERVICE_ID: document.getElementById('server-catalog-service-id').value,
        };

        const quantity = document.getElementById('server-quantity').value;
        if (quantity && parseInt(quantity) > 0) {
            formDataObject.Qnt = quantity;
        }

        const dynamicFieldPairs = Array.from(document.querySelectorAll('#server-dynamic-fields-section input[type=\"text\"]'))
            .map((input) => `\"${input.name}\":\"${input.value}\"`)
            .join(',');

        if (dynamicFieldPairs) {
            formDataObject.SERIAL_NUMBER = dynamicFieldPairs;
        }

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formDataObject,
            success: function (response) {
                const swalApple = {
                    background: 'var(--theme-surface-strong)',
                    color: 'var(--theme-text)',
                    backdrop: 'rgba(0,0,0,0.2)',
                    padding: '2rem',
                    customClass: {
                        popup: 'rounded-[32px] border border-white/10 shadow-2xl backdrop-blur-xl',
                        title: 'text-xl font-semibold tracking-tight',
                        htmlContainer: 'text-[15px] leading-relaxed theme-muted',
                        confirmButton: 'rounded-2xl px-10 py-3.5 bg-[#1d1d1f] text-white text-sm font-semibold transition hover:opacity-90 active:scale-95'
                    },
                    buttonsStyling: false
                };

                if (response.success) {
                    Swal.fire({
                        ...swalApple,
                        icon: 'success',
                        iconColor: 'var(--theme-accent)',
                        title: 'Pedido Enviado',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    closeServerModal();
                } else {
                    Swal.fire({
                        ...swalApple,
                        icon: 'warning',
                        iconColor: '#ff9500',
                        title: 'Ops!',
                        text: response.error || 'Nao foi possivel enviar o pedido.',
                    });
                }
            },
            error: function (xhr) {
                let errorMsg = 'Ocorreu um erro ao enviar o pedido.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        errorMsg = Object.values(errors).flat().join('\n');
                    } else if (xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                } else if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                }
                
                Swal.fire({
                    background: 'var(--theme-surface-strong)',
                    color: 'var(--theme-text)',
                    backdrop: 'rgba(0,0,0,0.2)',
                    padding: '2rem',
                    customClass: {
                        popup: 'rounded-[32px] border border-white/10 shadow-2xl backdrop-blur-xl',
                        title: 'text-xl font-semibold tracking-tight',
                        htmlContainer: 'text-[15px] leading-relaxed theme-muted',
                        confirmButton: 'rounded-2xl px-10 py-3.5 bg-[#1d1d1f] text-white text-sm font-semibold transition hover:opacity-90 active:scale-95'
                    },
                    buttonsStyling: false,
                    icon: 'error',
                    iconColor: '#ff3b30',
                    title: 'Validacao',
                    text: errorMsg,
                });
            }
        });
    });

    populateServerGroupFilter(serverFilteredServices);
    renderServerServices(serverFilteredServices);
    applyServerViewMode(serverCurrentView);
    if(serverSearchInput) serverSearchInput.addEventListener('input', filterServerServices);
    if(serverGroupFilter) serverGroupFilter.addEventListener('change', filterServerServices);
    if(serverViewListButton) serverViewListButton.addEventListener('click', () => applyServerViewMode('list'));
    if(serverViewCardsButton) serverViewCardsButton.addEventListener('click', () => applyServerViewMode('cards'));
});
</script>
BLADE,
        ];
    }
}
