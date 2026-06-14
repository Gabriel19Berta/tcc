<form method="GET" action="{{ $action }}" class="mb-4 flex flex-col lg:flex-row lg:justify-between gap-4 sm:px-0 px-4">
    <div class="flex flex-wrap gap-2 items-end">
        {{ $slot }}
    </div>

    <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
        <a href="{{ url()->current() }}" class="flex btn btn-danger px-4 block text-center">
            Limpar
            <i class="fa-solid fa-eraser ml-2"></i>
        </a>

        <x-primary-button class="w-full sm:w-auto justify-center">
            {{ __('Buscar') }}
            <i class="fa-solid fa-magnifying-glass ml-2"></i>
        </x-primary-button>
    </div>
</form>