<textarea {{ $attributes->merge([
    'class' => 'w-full input-form border rounded-md shadow-sm focus:outline-none focus:ring-1 
    border-gray-300 focus:border-primary focus:ring-primary']) }}>{{ $value ?? $slot }}
</textarea>