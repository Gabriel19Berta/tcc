<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <form action="{{ route('relatorios.gerar') }}" method="GET">
            @csrf
            <input type="hidden" name="relatorio" value="{{ $relatorio->slug() }}">

            <div class="flex gap-3">

            
                <div>
                    <x-input-label for="nome" value="Nome"/>
                    <x-input id="nome" name="nome" :value="request('nome')" />
                </div>

                <div>
                    <x-input-label for="formato" value="Formato" />

                    <select id="formato" name="formato">
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>

            <div class="float-right p-2">
                <x-primary-button>
                    {{ __('Gerar')}}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>


