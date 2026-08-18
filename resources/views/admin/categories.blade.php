@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Categories</h1>
</div>

<div class="grid grid-cols-3 gap-8">
    <div class="col-span-1">
        <div class="fk-card p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Add New Category</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" class="input w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" class="input w-full" placeholder="e.g. 📱">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" class="input w-full" value="0">
                </div>
                <button type="submit" class="fk-btn-primary btn-block">Add Category</button>
            </form>
        </div>
    </div>

    <div class="col-span-2">
        <div class="fk-card p-0">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr>
                            <td class="text-2xl">{{ $cat->icon }}</td>
                            <td class="font-bold text-slate-800">{{ $cat->name }}</td>
                            <td class="text-slate-500 text-sm">{{ $cat->slug }}</td>
                            <td><span class="badge badge-primary">{{ $cat->products_count }}</span></td>
                            <td>{{ $cat->sort_order }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
