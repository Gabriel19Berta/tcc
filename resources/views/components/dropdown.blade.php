@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white', 'floating' => false])

{{--
    floating: o menu é posicionado em relação à tela (position: fixed), e não ao
    elemento pai. Use dentro de áreas com rolagem (ex.: ações das tabelas), onde um
    menu "absolute" seria cortado pelo overflow e criaria barra de rolagem.
    Abre para baixo ou, se não houver espaço, para cima, sempre dentro da tela.
--}}

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};

$positionClasses = $floating ? 'fixed' : 'absolute mt-2 ' . $alignmentClasses;
@endphp

<div class="relative"
        x-data="{
            open: false,
            alternar() {
                this.open = ! this.open;

                if (this.open && {{ $floating ? 'true' : 'false' }}) {
                    this.$nextTick(() => this.posicionar());
                }
            },
            posicionar() {
                const margem = 8;
                const gatilho = this.$refs.gatilho.getBoundingClientRect();
                const menu = this.$refs.menu;
                const altura = menu.offsetHeight;
                const largura = menu.offsetWidth;

                // Abre para baixo; se não couber e houver mais espaço acima, abre para cima.
                const espacoAbaixo = window.innerHeight - gatilho.bottom;
                const paraCima = espacoAbaixo < altura + margem && gatilho.top > espacoAbaixo;
                let top = paraCima ? gatilho.top - altura - margem : gatilho.bottom + margem;

                // Alinha pela borda direita (padrão) ou esquerda do botão.
                let left = '{{ $align }}' === 'left' ? gatilho.left : gatilho.right - largura;

                // Mantém o menu inteiro dentro da tela.
                top = Math.max(margem, Math.min(top, window.innerHeight - altura - margem));
                left = Math.max(margem, Math.min(left, window.innerWidth - largura - margem));

                menu.style.top = top + 'px';
                menu.style.left = left + 'px';
            },
        }"
        @click.outside="open = false"
        @close.stop="open = false"
        @if ($floating)
            {{-- Menu fixo na tela: fecha ao rolar ou redimensionar para não se descolar do botão. --}}
            @scroll.window.capture="open = false"
            @resize.window="open = false"
        @endif
>
    <div x-ref="gatilho" @click="alternar()">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-ref="menu"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="{{ $positionClasses }} z-50 {{ $width }} rounded-md shadow-lg"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
