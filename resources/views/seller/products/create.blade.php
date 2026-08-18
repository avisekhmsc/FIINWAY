@extends('layouts.app', ['hideNav' => true, 'hideFooter' => true])

@section('content')
<div class="bg-[#f1f3f6] min-h-[calc(100vh-60px)] flex flex-col" x-data="productWizard()">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center gap-4 px-4 py-4 justify-between">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('seller.products') }}" class="w-9 h-9 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100">
                    <i class="ri-arrow-left-line text-lg"></i>
                </a>
                <h1 class="text-base font-bold text-[#212121]">Add New Product</h1>
            </div>
            
            <!-- Wizard Progress -->
            <div class="flex justify-between relative w-full sm:w-96 px-4">
                <div class="absolute top-4 left-6 right-6 h-0.5 bg-slate-200 -z-10"></div>
                <div class="absolute top-4 left-6 h-0.5 bg-[#006837] transition-all duration-300 -z-10" :style="`width: calc(${(step - 1) / 3 * 100}% * (100% - 3rem) / 100)`"></div>
                <template x-for="i in 4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors border-2 bg-white z-10"
                         :class="step > i ? 'bg-[#388e3c] border-[#388e3c] text-white' : step === i ? 'bg-[#006837] border-[#006837] text-white' : 'border-slate-300 text-slate-400'"
                         x-text="step > i ? '✓' : i"></div>
                </template>
            </div>
        </div>
    </div>

    <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm" class="flex-1 flex flex-col">
        @csrf
        
        <div class="flex-1 p-4 overflow-y-auto pb-24">
            <!-- STEP 1: Photos & Basic -->
            <div x-show="step === 1" x-transition.opacity>
                <h2 class="text-xl font-bold text-slate-900 mb-6">Photos & Basic Info</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Upload Photos (Max 5)</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="upload-zone !p-0 aspect-square flex flex-col items-center justify-center bg-indigo-50 border-indigo-200">
                            <input type="file" name="images[]" multiple accept="image/*" class="hidden" @change="handleFiles" required>
                            <i class="ri-camera-fill text-2xl text-indigo-500 mb-1"></i>
                            <span class="text-[0.65rem] font-semibold text-indigo-600">Add Photo</span>
                        </label>
                        <template x-for="(img, idx) in previewImages" :key="idx">
                            <div class="aspect-square rounded-xl overflow-hidden relative">
                                <img :src="img" class="w-full h-full object-cover">
                                <button type="button" @click="removeImage(idx)" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs shadow-md"><i class="ri-close-line"></i></button>
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">First photo will be the cover image.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name</label>
                        <input type="text" name="name" class="input" placeholder="e.g. iPhone 13 Pro 256GB" required x-model="formData.name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                        <select name="category_id" class="input bg-white" required x-model="formData.category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Condition & Details -->
            <div x-show="step === 2" x-cloak x-transition.opacity>
                <h2 class="text-xl font-bold text-slate-900 mb-6">Condition & Details</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Product Condition</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition-all" :class="formData.condition_type === 'new' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-100 bg-white text-slate-600'">
                                <input type="radio" name="condition_type" value="new" class="sr-only" x-model="formData.condition_type">
                                <i class="ri-sparkling-fill text-2xl mb-1"></i>
                                <span class="font-bold text-sm">Brand New</span>
                            </label>
                            <label class="flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition-all" :class="formData.condition_type === 'old' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-100 bg-white text-slate-600'">
                                <input type="radio" name="condition_type" value="old" class="sr-only" x-model="formData.condition_type">
                                <i class="ri-recycle-fill text-2xl mb-1"></i>
                                <span class="font-bold text-sm">Used / Old</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="formData.condition_type === 'old'" x-collapse class="space-y-4 bg-orange-50 p-4 rounded-xl border border-orange-100">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">How old is it? (Months)</label>
                            <input type="number" name="product_age_months" class="input bg-white" placeholder="e.g. 12">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Available Documents</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="bill_available" value="1" class="w-4 h-4 text-indigo-600 rounded">
                                    <span class="text-sm font-medium">Original Bill</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="warranty_available" value="1" class="w-4 h-4 text-indigo-600 rounded" x-model="formData.has_warranty">
                                    <span class="text-sm font-medium">Under Warranty</span>
                                </label>
                            </div>
                        </div>
                        <div x-show="formData.has_warranty" x-collapse>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Warranty Details</label>
                            <input type="text" name="warranty_info" class="input bg-white" placeholder="e.g. 6 months remaining">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Any Damage / Repairs?</label>
                            <textarea name="damage_details" class="input bg-white" placeholder="Describe any scratches, dents, or past repairs..." rows="2"></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Full Description</label>
                        <textarea name="description" class="input" placeholder="Describe your product in detail..." rows="4" required></textarea>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Pricing -->
            <div x-show="step === 3" x-cloak x-transition.opacity>
                <h2 class="text-xl font-bold text-slate-900 mb-6">Pricing</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Selling Price (₹)</label>
                        <input type="number" name="selling_price" class="input text-xl font-black text-indigo-600" placeholder="0" required x-model="formData.price">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Original Price (₹) - Optional</label>
                        <input type="number" name="original_price" class="input" placeholder="0" x-model="formData.mrp">
                        <p class="text-xs text-slate-500 mt-1">If provided, we'll show a discount percentage.</p>
                    </div>

                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100 mt-4">
                        <h4 class="text-sm font-bold text-emerald-800 mb-2">Estimated Earnings</h4>
                        <div class="flex justify-between items-center text-sm mb-1">
                            <span class="text-emerald-700">Selling Price</span>
                            <span class="font-medium">₹<span x-text="formData.price || 0"></span></span>
                        </div>
                        <div class="flex justify-between items-center text-sm mb-2">
                            <span class="text-emerald-700">Platform Fee (10%)</span>
                            <span class="font-medium text-red-500">-₹<span x-text="Math.round((formData.price || 0) * 0.10)"></span></span>
                        </div>
                        <div class="divider border-emerald-200 border-dashed my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-emerald-900">You will get</span>
                            <span class="font-black text-emerald-600 text-lg">₹<span x-text="Math.round((formData.price || 0) * 0.90)"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Delivery -->
            <div x-show="step === 4" x-cloak x-transition.opacity>
                <h2 class="text-xl font-bold text-slate-900 mb-6">Delivery Options</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">How will you deliver?</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex flex-col justify-center p-3 border-2 rounded-xl cursor-pointer transition-all" :class="formData.delivery_type === 'courier' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-100 bg-white'">
                                <input type="radio" name="delivery_type" value="courier" class="sr-only" x-model="formData.delivery_type">
                                <div class="flex items-center gap-2 mb-1 text-indigo-700">
                                    <i class="ri-truck-line text-lg"></i>
                                    <span class="font-bold text-sm">Courier</span>
                                </div>
                                <span class="text-xs text-slate-500">I will ship it</span>
                            </label>
                            <label class="flex flex-col justify-center p-3 border-2 rounded-xl cursor-pointer transition-all" :class="formData.delivery_type === 'self' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-100 bg-white'">
                                <input type="radio" name="delivery_type" value="self" class="sr-only" x-model="formData.delivery_type">
                                <div class="flex items-center gap-2 mb-1 text-indigo-700">
                                    <i class="ri-store-2-line text-lg"></i>
                                    <span class="font-bold text-sm">Self Pickup</span>
                                </div>
                                <span class="text-xs text-slate-500">Buyer picks up</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="formData.delivery_type === 'courier' || formData.delivery_type === 'both'" x-collapse>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Expected Delivery Time (Days)</label>
                        <input type="number" name="delivery_days" class="input" value="5" required>
                    </div>

                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 mt-6">
                        <div class="flex gap-3">
                            <i class="ri-information-fill text-indigo-500 text-xl"></i>
                            <div>
                                <h4 class="text-sm font-bold text-indigo-900 mb-1">Final Step</h4>
                                <p class="text-xs text-indigo-700 leading-relaxed">By posting this product, you agree to our seller policies. Once approved by admin, it will be visible to buyers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-50 shadow-lg">
            <div class="max-w-lg mx-auto flex gap-3">
                <button type="button" class="flex-1 py-3 border border-slate-300 text-[#212121] font-bold text-sm rounded-sm hover:bg-slate-50" x-show="step > 1" @click="step--">Back</button>
                <button type="button" class="flex-1 py-3 bg-[#e94f1c] hover:bg-[#cc4214] text-white font-bold text-sm rounded-sm shadow" x-show="step < 4" @click="validateStep()">Next Step <i class="ri-arrow-right-line ml-1"></i></button>
                <button type="submit" class="flex-1 py-3 bg-[#388e3c] hover:bg-green-700 text-white font-bold text-sm rounded-sm shadow" x-show="step === 4" id="submitBtn">Post Product <i class="ri-check-line ml-1"></i></button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productWizard', () => ({
        step: 1,
        previewImages: [],
        formData: {
            name: '',
            category_id: '',
            condition_type: 'new',
            has_warranty: false,
            price: '',
            mrp: '',
            delivery_type: 'courier'
        },
        
        handleFiles(e) {
            const files = Array.from(e.target.files).slice(0, 5);
            this.previewImages = [];
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => { this.previewImages.push(e.target.result) };
                reader.readAsDataURL(file);
            });
        },
        
        removeImage(idx) {
            this.previewImages.splice(idx, 1);
            // note: actually removing from input is complex, this is just visual for demo
        },
        
        validateStep() {
            if(this.step === 1) {
                if(!this.formData.name || !this.formData.category_id || this.previewImages.length === 0) {
                    alert('Please fill name, category and upload at least 1 image.');
                    return;
                }
            }
            if(this.step === 3) {
                if(!this.formData.price || this.formData.price <= 0) {
                    alert('Please enter a valid selling price.');
                    return;
                }
            }
            this.step++;
            window.scrollTo(0,0);
        }
    }))
});

document.getElementById('productForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin text-xl"></i> Uploading...';
    btn.classList.add('opacity-80', 'cursor-not-allowed');
});
</script>
@endsection
