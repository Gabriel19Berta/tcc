<form action="{{ route('clientes.toggleStatus', $id) }}" method="POST" class="form-toggle-status">
    @csrf
    @method('PATCH')

    <button type="submit" class="px-2 py-1 rounded-md text-white text-sm
        {{ $status ? 'bg-primary' : 'bg-danger-500' }}">
        
        {{ $status ? 'Ativo' : 'Inativo' }}
    </button>
    
</form>

