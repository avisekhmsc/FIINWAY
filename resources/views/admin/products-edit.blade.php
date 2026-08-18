@extends('layouts.admin')

@section('header_title', 'Edit Product')

@section('content')
<div class="fk-card p-6 max-w-3xl">
    <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100">
        <img src="{{ $product->primary_image_url }}" class="w-16 h-16 rounded object-cover border border-slate-200">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $product->name }}</h2>
            <p class="text-sm text-slate-500">Seller: <span class="font-medium text-slate-700">{{ $product->seller->name }}</span></p>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Product Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:border-[#006837] focus:outline-none" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                <select name="category_id" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:border-[#006837] focus:outline-none" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Selling Price (₹)</label>
                <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:border-[#006837] focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Original Price (₹)</label>
                <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $product->original_price) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:border-[#006837] focus:outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Stock</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:border-[#006837] focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full border border-slate-300 rounded px-3 py-2 text-sm focus:border-[#006837] focus:outline-none" required>
                    <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ $product->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ $product->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive / Off</option>
                    <option value="sold" {{ $product->status == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>
        </div>
        
        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="fk-btn-primary">Save Changes</button>
            <a href="{{ route('admin.products') }}" class="fk-btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
