<div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Finalizar Pagamento</h1>
        
        <!-- Exibir imagem do QR Code base64 -->
        <img src="data:image/png;base64,{{ $imagemQrcode }}" alt="QR Code">

        <!-- Outras informações da cobrança, se necessário -->
        <div class="mt-4">
            <h2 class="text-lg font-semibold">Detalhes da Cobrança:</h2>
            <pre>{{ json_encode($detalhesCobranca, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>