<?php

namespace Database\Seeders\Plugins;

class ImeiServicesTablePluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'imei-services-table';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Tabela IMEI Services',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-table-cells',
            'description' => 'Tabela principal com busca, filtro e modal de pedido.',
            'default_settings' => [
                'search_label' => 'Buscar servicos',
                'search_placeholder' => 'Pesquisar por nome do servico...',
                'filter_label' => 'Filtrar por grupo',
                'table_title' => 'Catalogo IMEI',
                'view_label' => 'Visualizacao',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 py-6 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="rounded-[32px] p-6 md:p-8 theme-panel">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['table_title'] }}</p>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Encontre o servico certo sem perder tempo.</h2>
                </div>
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:min-w-[620px]">
                    <div class="flex-1">
                        <label for="imei-search-input" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['search_label'] }}</label>
                        <input type="text" id="imei-search-input" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" placeholder="{{ $settings['search_placeholder'] }}">
                    </div>
                    <div class="md:w-[220px]">
                        <label for="imei-group-filter" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['filter_label'] }}</label>
                        <select id="imei-group-filter" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text">
                            <option value="">Todos os grupos</option>
                        </select>
                    </div>
                    <div class="md:w-[180px]">
                        <span class="mb-2 block text-sm font-medium theme-muted">{{ $settings['view_label'] }}</span>
                        <div class="grid grid-cols-2 rounded-[18px] p-1 theme-soft">
                            <button type="button" id="imei-view-list" class="rounded-[14px] px-4 py-3 text-sm font-medium transition">Lista</button>
                            <button type="button" id="imei-view-cards" class="rounded-[14px] px-4 py-3 text-sm font-medium transition">Cards</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="imei-table-view" class="mt-8 overflow-hidden rounded-[28px] border" style="border-color: var(--theme-border);">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead style="background: var(--theme-surface-soft);">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Servico</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Tempo</th>
                                <th class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Custo</th>
                            </tr>
                        </thead>
                        <tbody id="imei-service-list"></tbody>
                    </table>
                </div>
            </div>

            <div id="imei-cards-view" class="mt-8 hidden">
                <div id="imei-service-cards" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"></div>
            </div>
        </div>
    </div>
</section>

<div class="fixed inset-0 z-50 hidden items-center justify-center px-4" id="imei-form-modal">
    <div class="absolute inset-0 bg-black/45 backdrop-blur-sm" onclick="closeImeiModal()"></div>
    <div class="relative w-full max-w-2xl rounded-[32px] p-6 md:p-8 theme-panel-strong">
        <form action="{{ route('submit_serial_number') }}" method="POST" id="imei-serial-form">
            @csrf
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.28em] theme-muted">Novo pedido</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-tight theme-text" id="imei-custom-name">Detalhes do servico</h3>
                </div>
                <button type="button" class="h-11 w-11 rounded-full theme-soft theme-text" onclick="closeImeiModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2">
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Servico</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="imei-service-name"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Tempo</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="imei-time"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Custo</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="imei-cost-display"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Service ID</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="imei-service-id-display"></p>
                </div>
                <div class="rounded-[22px] p-5 theme-soft md:col-span-2">
                    <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Fornecedor</p>
                    <p class="mt-3 text-base font-semibold theme-text" id="imei-provider-display"></p>
                </div>
            </div>

            <div class="mt-6">
                <label for="imei-serial-number" class="mb-2 block text-sm font-medium theme-muted" id="imei-serial-label">Serial</label>
                <input type="text" id="imei-serial-number" name="SERIAL_NUMBER" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" placeholder="Digite a informacao solicitada">
            </div>

            <div id="imei-dynamic-fields-section" class="mt-4 space-y-4"></div>

            <input type="hidden" id="imei-service-id" name="SERVICEID">
            <input type="hidden" id="imei-provider-id" name="PROVIDER_ID">
            <input type="hidden" id="imei-catalog-service-id" name="CATALOG_SERVICE_ID">
            <input type="hidden" id="imei-cost" name="COST">
            <input type="hidden" id="imei-service-name-input" name="servicename">
            <input type="hidden" id="imei-serial-fieldname" name="fieldname" value="">

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="button" class="rounded-full px-5 py-3 text-sm font-medium theme-soft theme-text" onclick="closeImeiModal()">Cancelar</button>
                <button type="submit" class="rounded-full px-5 py-3 text-sm font-medium shadow-sm theme-accent-bg">Enviar pedido</button>
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imeiFilteredServices = {!! json_encode($filteredServices ?? []) !!};
    console.log('IMEI Services rendering:', imeiFilteredServices.length, 'items');
    
    const imeiSearchInput = document.getElementById('imei-search-input');
    const imeiGroupFilter = document.getElementById('imei-group-filter');
    const imeiServiceList = document.getElementById('imei-service-list');
    const imeiServiceCards = document.getElementById('imei-service-cards');
    const imeiTableView = document.getElementById('imei-table-view');
    const imeiCardsView = document.getElementById('imei-cards-view');
    const imeiViewListButton = document.getElementById('imei-view-list');
    const imeiViewCardsButton = document.getElementById('imei-view-cards');
    const imeiViewStorageKey = 'imei-services-view-mode';
    let imeiCurrentView = localStorage.getItem(imeiViewStorageKey) || 'list';

    function imeiEscapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function populateImeiGroupFilter(services) {
        if(!imeiGroupFilter) return;
        const groups = [...new Set(services.map((service) => service.GROUPNAME))];
        groups.forEach((group) => {
            const option = document.createElement('option');
            option.value = group;
            option.textContent = group;
            imeiGroupFilter.appendChild(option);
        });
    }

    function renderImeiServices(services) {
        if(!imeiServiceList || !imeiServiceCards) return;
        imeiServiceList.innerHTML = '';
        imeiServiceCards.innerHTML = '';

        if (!services.length) {
            imeiServiceList.innerHTML = '<tr><td colspan="3" class="px-6 py-12 text-center text-sm theme-muted">Nenhum servico encontrado.</td></tr>';
            imeiServiceCards.innerHTML = '<div class="rounded-[28px] p-8 text-center text-sm theme-panel theme-muted md:col-span-2 xl:col-span-3">Nenhum servico encontrado.</div>';
            return;
        }

        let currentGroup = null;
        let currentCardsGroup = null;

        services.forEach((service) => {
            if (currentGroup !== service.GROUPNAME) {
                currentGroup = service.GROUPNAME;
                const groupRow = document.createElement('tr');
                groupRow.innerHTML = `<td colspan="3" class="px-6 py-4 text-[12px] font-semibold uppercase tracking-[0.28em] theme-muted" style="background: var(--theme-surface-soft);">${imeiEscapeHtml(service.GROUPNAME)}</td>`;
                imeiServiceList.appendChild(groupRow);
            }

            const row = document.createElement('tr');
            row.className = 'cursor-pointer transition hover:opacity-90';
            row.style.borderTop = '1px solid var(--theme-border)';
            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-medium theme-text">${imeiEscapeHtml(service.SERVICENAME)}</td>
                <td class="px-6 py-4 text-sm theme-muted">${imeiEscapeHtml(service.TIME)}</td>
                <td class="px-6 py-4 text-right text-sm font-semibold theme-text">$${imeiEscapeHtml(service.CREDIT)}<div class="mt-1 text-[11px] font-medium theme-muted">${imeiEscapeHtml(service.PROVIDER_NAME ?? '')}</div></td>
            `;
            row.addEventListener('click', () => openImeiModal(service));
            imeiServiceList.appendChild(row);

            if (currentCardsGroup !== service.GROUPNAME) {
                currentCardsGroup = service.GROUPNAME;
                const groupCard = document.createElement('div');
                groupCard.className = 'rounded-[24px] px-5 py-4 theme-soft md:col-span-2 xl:col-span-3';
                groupCard.innerHTML = `<p class="text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">${imeiEscapeHtml(service.GROUPNAME)}</p>`;
                imeiServiceCards.appendChild(groupCard);
            }

            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'rounded-[28px] p-6 text-left transition theme-panel hover:opacity-95';
            card.innerHTML = `
                <p class="text-[11px] uppercase tracking-[0.24em] theme-muted">Servico</p>
                <h3 class="mt-3 text-lg font-semibold leading-7 theme-text">${imeiEscapeHtml(service.SERVICENAME)}</h3>
                <div class="mt-6 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.24em] theme-muted">Tempo</p>
                        <p class="mt-2 text-sm theme-text">${imeiEscapeHtml(service.TIME)}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] uppercase tracking-[0.24em] theme-muted">Custo</p>
                        <p class="mt-2 text-lg font-semibold theme-text">$${imeiEscapeHtml(service.CREDIT)}</p>
                        <p class="mt-2 text-[11px] uppercase tracking-[0.2em] theme-muted">${imeiEscapeHtml(service.PROVIDER_NAME ?? '')}</p>
                    </div>
                </div>
            `;
            card.addEventListener('click', () => openImeiModal(service));
            imeiServiceCards.appendChild(card);
        });
    }

    function applyImeiViewMode(mode) {
        imeiCurrentView = mode;
        localStorage.setItem(imeiViewStorageKey, mode);

        if (mode === 'cards') {
            if(imeiTableView) imeiTableView.classList.add('hidden');
            if(imeiCardsView) imeiCardsView.classList.remove('hidden');
            if(imeiViewCardsButton) {
                imeiViewCardsButton.style.background = 'var(--theme-text)';
                imeiViewCardsButton.style.color = 'var(--theme-bg)';
            }
            if(imeiViewListButton) {
                imeiViewListButton.style.background = 'transparent';
                imeiViewListButton.style.color = 'var(--theme-text-muted)';
            }
        } else {
            if(imeiTableView) imeiTableView.classList.remove('hidden');
            if(imeiCardsView) imeiCardsView.classList.add('hidden');
            if(imeiViewListButton) {
                imeiViewListButton.style.background = 'var(--theme-text)';
                imeiViewListButton.style.color = 'var(--theme-bg)';
            }
            if(imeiViewCardsButton) {
                imeiViewCardsButton.style.background = 'transparent';
                imeiViewCardsButton.style.color = 'var(--theme-text-muted)';
            }
        }
    }

    function filterImeiServices() {
        const query = (imeiSearchInput?.value || '').trim().toLowerCase();
        const selectedGroup = imeiGroupFilter?.value;

        const services = imeiFilteredServices.filter((service) => {
            const matchesQuery = service.SERVICENAME.toLowerCase().includes(query);
            const matchesGroup = !selectedGroup || service.GROUPNAME === selectedGroup;
            return matchesQuery && matchesGroup;
        });

        renderImeiServices(services);
    }

    function openImeiModal(serviceData) {
        document.getElementById('imei-service-name').textContent = serviceData.SERVICENAME ?? '';
        document.getElementById('imei-service-name-input').value = serviceData.SERVICENAME ?? '';
        document.getElementById('imei-time').textContent = serviceData.TIME ?? '';
        document.getElementById('imei-cost-display').textContent = '$' + (serviceData.CREDIT ?? '');
        document.getElementById('imei-cost').value = serviceData.CREDIT ?? '';
        document.getElementById('imei-service-id').value = serviceData.SERVICEID ?? '';
        document.getElementById('imei-service-id-display').textContent = serviceData.SERVICEID ?? '';
        document.getElementById('imei-provider-id').value = serviceData.PROVIDER_ID ?? '';
        document.getElementById('imei-catalog-service-id').value = serviceData.CATALOG_SERVICE_ID ?? '';
        document.getElementById('imei-provider-display').textContent = serviceData.PROVIDER_NAME ?? '';

        const customName = serviceData.customname || 'Detalhes do servico';
        const fieldName = serviceData.fieldname || 'Campo Necessario';

        document.getElementById('imei-custom-name').textContent = customName;
        document.getElementById('imei-serial-label').textContent = customName;
        document.getElementById('imei-serial-fieldname').value = fieldName;
        document.getElementById('imei-serial-number').value = '';

        const dynamicFieldsSection = document.getElementById('imei-dynamic-fields-section');
        dynamicFieldsSection.innerHTML = '';

        if (serviceData.fieldname) {
            serviceData.fieldname.split(',').map((field) => field.trim()).filter(Boolean).slice(0, 7).forEach((field) => {
                const inputName = field.toLowerCase().replace(/\s+/g, '_');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = `
                    <label for="${imeiEscapeHtml(inputName)}" class="mb-2 block text-sm font-medium theme-muted">${imeiEscapeHtml(field)}</label>
                    <input type="text" id="${imeiEscapeHtml(inputName)}" name="${imeiEscapeHtml(inputName)}" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" placeholder="Digite ${imeiEscapeHtml(field).toLowerCase()}">
                `;
                dynamicFieldsSection.appendChild(wrapper);
            });
        }

        const modal = document.getElementById('imei-form-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    window.closeImeiModal = function() {
        const modal = document.getElementById('imei-form-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    document.getElementById('imei-serial-form')?.addEventListener('submit', function (event) {
        event.preventDefault();

        $.ajax({
            url: this.action,
            type: 'POST',
            data: $(this).serialize(),
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
                    closeImeiModal();
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
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
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

    populateImeiGroupFilter(imeiFilteredServices);
    renderImeiServices(imeiFilteredServices);
    applyImeiViewMode(imeiCurrentView);
    if(imeiSearchInput) imeiSearchInput.addEventListener('input', filterImeiServices);
    if(imeiGroupFilter) imeiGroupFilter.addEventListener('change', filterImeiServices);
    if(imeiViewListButton) imeiViewListButton.addEventListener('click', () => applyImeiViewMode('list'));
    if(imeiViewCardsButton) imeiViewCardsButton.addEventListener('click', () => applyImeiViewMode('cards'));
});
</script>
BLADE,
        ];
    }
}
