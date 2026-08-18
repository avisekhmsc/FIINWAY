@props([
    'icon' => 'ri-inbox-line',
    'title' => 'No items found',
    'message' => 'There is nothing to display here at the moment.',
    'actionUrl' => null,
    'actionLabel' => null
])

<div class="p-12 text-center bg-white rounded-3xl border border-slate-100 shadow-sm max-w-md mx-auto my-8">
    <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mb-4 shadow-sm">
        <i class="{{ $icon }}"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $title }}</h3>
    <p class="text-sm text-slate-500 mb-6 leading-relaxed">{{ $message }}</p>

    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-all shadow-lg shadow-indigo-600/20 active:scale-95">
            {{ $actionLabel }} <i class="ri-arrow-right-line ml-2"></i>
        </a>
    @endif
</div>
