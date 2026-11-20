<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <form action="{{ route('relatorios.gerar') }}" method="GET">
            @csrf

            <input type="hidden" name="relatorio" value="{{ $relatorio->slug() }}">
            <input type="hidden" name="formato" value="excel">

            <div class="flex gap-3">
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status">
                        <option value="">Todos</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="data-inicio" value="Data de inicio"/>
                    <x-input id="data-inicio" type="date" name="data-inicio" :value="old('data-inicio')" />
                </div>
                <div>
                    <x-input-label for="data-fim" value="Data de fim"/>
                    <x-input id="data-fim" type="date" name="data-fim" data-data-inicial="data-inicio" :value="old('data-fim')" />
                </div>
            </div>

            <div class="float-right py-2">
                <x-primary-button data-download>
                    {{ __('Gerar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>


