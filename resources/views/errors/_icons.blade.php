{{-- Íconos de las páginas de error (Heroicons outline, en línea para no depender de nada). --}}
@php
    $paths = [
        'lock'    => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'shield'  => '<path d="M12 3 5 6v6c0 4 3 7 7 9 4-2 7-5 7-9V6l-7-3z"/><path d="m9.5 12.5 5-5M14.5 12.5l-5-5" transform="translate(0 2)"/>',
        'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M8.5 11h5"/>',
        'clock'   => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'gauge'   => '<path d="M12 14l4-4"/><path d="M3.3 17a9 9 0 1 1 17.4 0"/><circle cx="12" cy="14" r="1.2"/>',
        'alert'   => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',
        'wrench'  => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/>',
        'cloud'   => '<path d="M7 18a4.5 4.5 0 0 1-.6-9A6 6 0 0 1 18 8.5a4 4 0 0 1-.5 9.5H7z"/><path d="m3 3 18 18"/>',
        'home'    => '<path d="m3 11 9-7 9 7"/><path d="M5 10v10h14V10"/>',
        'back'    => '<path d="M15 18l-6-6 6-6"/>',
        'login'   => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5M15 12H3"/>',
        'refresh' => '<path d="M21 12a9 9 0 1 1-2.6-6.4L21 8"/><path d="M21 3v5h-5"/>',
        'mail'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    ];
@endphp
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? $paths['alert'] !!}</svg>
