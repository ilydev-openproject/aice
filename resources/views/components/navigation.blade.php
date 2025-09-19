<!-- Bottom Nav -->
<div class="fixed max-w-sm w-full bottom-0 shadow-[0px_-4px_6px_-1px_rgba(0,0,0,0.1)]  z-50">
    <div class="flex gap-2 bg-slate-100 backdrop-blur-sm px-4 pt-3 pb-5 shadow-t-lg">
        <a class="flex flex-1 flex-col items-center justify-center gap-1.5 rounded-full" href="/">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('/') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-home class="w-6 h-6" />
            </div>
            <p class="text-xs font-bold leading-normal tracking-wide"
                style="color: {{ Request::is('/') ? 'var(--brand-purple)' : '#756783' }};">
                Home</p>
        </a>
        <a class="flex flex-1 flex-col items-center justify-center gap-1.5" href="{{ route('outlet') }}">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('outlet') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-store class="w-6 h-6" />
            </div>
            <p class="text-xs font-medium leading-normal tracking-wide"
                style="color: {{ Request::is('outlet') ? 'var(--brand-purple)' : '#756783' }};">Toko</p>
        </a>
        <a class="flex flex-1 flex-col items-center justify-center gap-1.5" href="{{ route('visit') }}">
            <div class="flex h-8 items-center justify-center"
                style="color: {{ Request::is('visit') ? 'var(--brand-purple)' : '#756783' }};">
                <x-lucide-handshake class="w-6 h-6" />
            </div>
            <p class="text-xs font-medium leading-normal tracking-wide"
                style="color: {{ Request::is('visit') ? 'var(--brand-purple)' : '#756783' }};">Visit</p>
        </a>
        <a class="flex flex-1 flex-col items-center justify-center gap-1.5 text-[#756783]" href="#">
            <div class="text-[#756783] flex h-8 items-center justify-center">
                <x-lucide-user class="w-6 h-6" />
            </div>
            <p class="text-[#756783] text-xs font-medium leading-normal tracking-wide">Profile</p>
        </a>
    </div>
</div>