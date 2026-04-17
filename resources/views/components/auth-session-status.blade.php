@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-emerald-400 bg-emerald-500/10 p-3 rounded-xl border border-emerald-500/20 text-center']) }}>
        {{ $status }}
    </div>
@endif