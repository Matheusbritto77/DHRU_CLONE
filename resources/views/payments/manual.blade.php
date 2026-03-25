<div class="max-w-3xl mx-auto px-6 py-10">
    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Pagamento Manual</p>
        <h1 class="mt-3 text-2xl font-semibold text-slate-900">{{ $gatewayLabel }}</h1>
        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $message }}</p>

        <div class="mt-6 rounded-2xl bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-900">Resumo</p>
            <p class="mt-2 text-sm text-slate-600">Credito: {{ number_format((float) $quote['credit_amount'], 2) }} USD</p>
            <p class="mt-1 text-sm text-slate-600">Cobranca: {{ number_format((float) $quote['total_gateway'], 2) }} {{ $quote['gateway_currency'] }}</p>
            <p class="mt-1 text-sm text-slate-600">Visual do usuario: {{ number_format((float) $quote['display_total'], 2) }} {{ $quote['user_currency'] }}</p>
        </div>
    </div>
</div>
