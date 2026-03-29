@props([
    'value' => null
])

<select name="uf" id="uf"
    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary">
    <option value="">Selecione</option>

    @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
        <option value="{{ $uf }}"  @selected(old('uf', $value) === $uf)>
            {{ $uf }}
        </option>
    @endforeach
</select>