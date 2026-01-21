<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2  bg-primary  border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 active:bg-primary-600 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
