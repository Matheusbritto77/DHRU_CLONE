<x-filament-panels::page>
    @if ($this->hasCustomAdminComponent())
        @livewire($this->getCustomAdminComponentClass(), ['plugin' => $this->getRecord()], key('plugin-admin-' . $this->getRecord()->getKey()))
    @else
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Salvar configurações
                </x-filament::button>
            </div>
        </form>
    @endif
</x-filament-panels::page>
