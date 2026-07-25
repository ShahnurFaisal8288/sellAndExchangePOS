<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div class="bg-background flex min-h-svh flex-col items-center justify-center md:p-10">
        <div class="flex w-full max-w-sm flex-col">
            <a href="" class="flex flex-col items-center gap-0 font-medium" wire:navigate>
                <span class="d-flex align-items-center justify-content-center rounded">
    <img src="{{ asset('assets/img/apple&series2.png.png') }}" alt="Logo"
        style="height: 100px; width: 120px; object-fit: contain;" />
</span>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
