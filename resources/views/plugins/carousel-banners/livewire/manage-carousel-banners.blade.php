<div>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Salvar banners
            </x-filament::button>
        </div>
    </form>
</div>
