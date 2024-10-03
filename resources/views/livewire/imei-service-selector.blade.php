<!-- resources/views/livewire/imei-service-selector.blade.php -->

<div class="container mx-auto py-8">
    <h1 class="mb-4 text-2xl">{{ __('Select IMEI Service') }}</h1>

    <div class="relative">
        <label for="search-input" class="form-label">{{ __('Search IMEI Services') }}</label>
        <input wire:model="search" type="text" id="search-input" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search services...">
    </div>

    <select id="service-select" class="form-select mt-2 w-full py-3 px-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" size="30" multiple>
        <option value="">{{ __('Search or Select IMEI Service') }}</option>
        @foreach ($filteredServices as $service)
            <option value="{{ $service['SERVICEID'] }}" data-custom='@json($service)'>
                {{ $service['SERVICENAME'] }} - {{ $service['TIME'] }} mins - ${{ $service['CREDIT'] }}
            </option>
        @endforeach
    </select>
</div>
