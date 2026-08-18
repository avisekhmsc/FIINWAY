@extends('layouts.app', ['hideNav' => true])

@section('content')
<div class="bg-slate-50 min-h-screen pb-32">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 p-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.dashboard') }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-700">
                    <i class="ri-arrow-left-line text-lg"></i>
                </a>
                <h1 class="text-lg font-bold text-slate-900">My Products</h1>
            </div>
            <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm !rounded-full"><i class="ri-add-line"></i> Add New</a>
        </div>
        
        <!-- Tabs -->
        <div class="flex overflow-x-auto gap-4 hide-scrollbar">
            @php $tabs = ['all' => 'All', 'active' => 'Active', 'pending' => 'Pending', 'inactive' => 'Inactive']; @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('seller.products', ['status' => $val]) }}" class="whitespace-nowrap pb-2 text-sm font-semibold transition-colors {{ $status === $val ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-500' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="p-4 space-y-4">
        @forelse($products as $product)
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex gap-4 relative">
            
            <div class="absolute top-4 right-4 flex gap-2">
                <a href="{{ route('seller.products.edit', $product->id) }}" class="w-8 h-8 rounded-full bg-green-50 text-green-700 flex items-center justify-center hover:bg-green-100 transition-colors"><i class="ri-edit-line"></i></a>
                @if($product->status !== 'inactive')
                <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" onclick="return confirm('Are you sure you want to deactivate this product?')"><i class="ri-delete-bin-line"></i></button>
                </form>
                @endif
            </div>

            <div class="w-20 h-20 rounded-xl bg-slate-50 shrink-0">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-xl mix-blend-multiply">
            </div>
            
            <div class="flex-1 pt-1 pr-16">
                <h3 class="text-sm font-semibold text-slate-800 line-clamp-1 mb-1">{{ $product->name }}</h3>
                <p class="text-sm font-black text-slate-900 mb-2">₹{{ number_format($product->selling_price) }}</p>
                
                <div class="flex items-center gap-2">
                    <span class="badge {{ $product->condition_type === 'new' ? 'badge-new' : 'badge-old' }} !text-[0.6rem]">{{ ucfirst($product->condition_type) }}</span>
                    <span class="badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'pending' ? 'badge-warning' : 'badge-danger') }} !text-[0.6rem]">{{ ucfirst($product->status) }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                <i class="ri-stack-line text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No products found</h3>
            <p class="text-sm text-slate-500 mb-6">You haven't added any products yet.</p>
            <a href="{{ route('seller.products.create') }}" class="btn btn-primary"><i class="ri-add-line"></i> Add First Product</a>
        </div>
        @endforelse

        <div class="mt-4">
            {{ $products->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
