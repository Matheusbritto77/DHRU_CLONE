<div class="space-y-6">
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Salvar gateway
            </x-filament::button>
        </div>
    </form>
</div>
