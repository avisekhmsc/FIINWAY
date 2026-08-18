@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Products Management</h1>
    
    <div class="flex gap-2">
        @foreach(['all' => 'All', 'pending' => 'Pending Approval', 'active' => 'Active', 'rejected' => 'Rejected'] as $val => $label)
            <a href="{{ route('admin.products', ['status' => $val]) }}" class="btn btn-sm {{ $status === $val ? 'btn-primary' : 'btn-outline bg-white border-slate-200 text-slate-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

<div class="fk-card p-0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Seller</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->primary_image_url }}" class="w-12 h-12 rounded-lg object-cover">
                            <div>
                                <p class="font-semibold text-slate-800 line-clamp-1 max-w-[200px]">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500 uppercase">{{ $product->condition_type }}</p>
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->seller->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td class="font-bold">₹{{ number_format($product->selling_price) }}</td>
                    <td>
                        <span class="badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td>
                        @if($product->status === 'pending')
                        <div class="flex gap-2" x-data="{ rejectOpen: false }">
                            <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="fk-btn-primary btn-sm"><i class="ri-check-line"></i></button>
                            </form>
                            <button @click="rejectOpen = true" class="btn btn-danger btn-sm"><i class="ri-close-line"></i></button>
                            
                            <!-- Reject Modal -->
                            <div x-show="rejectOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40" x-cloak>
                                <div class="bg-white rounded-xl p-6 w-96 shadow-xl" @click.away="rejectOpen = false">
                                    <h3 class="font-bold text-lg mb-4">Reject Product</h3>
                                    <form action="{{ route('admin.products.reject', $product->id) }}" method="POST">
                                        @csrf
                                        <textarea name="reason" class="input w-full mb-4" placeholder="Reason for rejection..." required></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="rejectOpen = false" class="fk-btn-outline btn-sm">Cancel</button>
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-[#006837] hover:underline text-sm font-semibold flex items-center gap-1">
                                    <i class="ri-edit-box-line"></i> Modify
                                </a>
                                <span class="text-slate-300">|</span>
                                <form action="{{ route('admin.products.toggle', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="{{ $product->status === 'active' ? 'text-orange-600' : 'text-green-600' }} hover:underline text-sm font-semibold flex items-center gap-1">
                                        <i class="ri-power-line"></i> {{ $product->status === 'active' ? 'Turn Off' : 'Turn On' }}
                                    </button>
                                </form>
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-slate-500 hover:text-slate-700 text-sm font-semibold">
                                    <i class="ri-external-link-line"></i> View
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-slate-500">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $products->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
