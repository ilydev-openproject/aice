<!-- Bottom Nav -->
<div class="fixed max-w-sm w-full bottom-0 shadow-[0px_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50">
    <div class="flex gap-2 bg-slate-100 backdrop-blur-sm px-4 py-2 shadow-t-lg">

        <a wire:navigate class="flex flex-1 flex-col items-center justify-center rounded-full"
            href="{{ route('home') }}">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('/') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-home class="w-6 h-6" />
            </div>
            <p class="text-xs font-bold leading-normal tracking-wide"
                style="color: {{ Request::is('/') ? 'var(--brand-purple)' : '#756783' }};">
                Home</p>
        </a>

        <a wire:navigate class="flex flex-1 flex-col items-center justify-center" href="{{ route('outlet') }}">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('outlet') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-store class="w-6 h-6" />
            </div>
            <p class="text-xs font-medium leading-normal tracking-wide"
                style="color: {{ Request::is('outlet') ? 'var(--brand-purple)' : '#756783' }};">Toko</p>
        </a>

        <a wire:navigate class="flex flex-1 flex-col items-center justify-center" href="{{ route('visit') }}">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('visit') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-handshake class="w-6 h-6" />
            </div>
            <p class="text-xs font-medium leading-normal tracking-wide"
                style="color: {{ Request::is('visit') ? 'var(--brand-purple)' : '#756783' }};">Visit</p>
        </a>

        <a wire:navigate class="flex flex-1 flex-col items-center justify-center"
            href="{{ route('products.catalog') }}">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('produk') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-popsicle class="w-6 h-6" />
            </div>
            <p class="text-xs font-medium leading-normal tracking-wide"
                style="color: {{ Request::is('produk') ? 'var(--brand-purple)' : '#756783' }};">Produk</p>
        </a>

    </div>
</div>