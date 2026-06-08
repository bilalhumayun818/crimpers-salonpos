@extends('layouts.app')
@section('title', 'New Product')
@section('content')
    <style>
        :root {
            --y1: #F7DF79;
            --y2: #FBEFBC;
            --yd: #c9a800;
            --ydark: #a07800;
            --ybg: #fffdf0;
        }

        .pg-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .back-btn {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1.5px solid #e4e4e7;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #71717a;
            text-decoration: none;
            transition: .2s;
            flex-shrink: 0;
        }

        .back-btn:hover {
            border-color: var(--y1);
            color: var(--ydark);
            background: var(--ybg);
        }

        .pg-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #18181b;
            letter-spacing: -.02em;
            margin-bottom: 3px;
        }

        .pg-sub {
            font-size: .85rem;
            color: #71717a;
        }

        .form-card {
            background: #fff;
            border: 1.5px solid #f0e8a0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .04);
            margin-bottom: 16px;
        }

        .form-section {
            padding: 20px 24px;
            border-bottom: 1px solid #f4f4f5;
        }

        .section-title {
            font-size: .72rem;
            font-weight: 700;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .section-icon {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: var(--y2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ydark);
        }

        .f-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .f-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .f-grid-4 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .f-row {
            margin-bottom: 14px;
        }

        .f-label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .f-input,
        .f-select,
        .f-textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #f0e8a0;
            border-radius: 9px;
            font-size: .875rem;
            font-family: 'Outfit', sans-serif;
            color: #18181b;
            background: var(--ybg);
            outline: none;
            transition: .2s;
            box-sizing: border-box;
        }

        .f-input:focus,
        .f-select:focus,
        .f-textarea:focus {
            border-color: var(--y1);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(247, 223, 121, .15);
        }

        .f-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .check-row {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .check-row input[type=checkbox] {
            width: 15px;
            height: 15px;
            accent-color: var(--ydark);
            cursor: pointer;
        }

        .check-lbl {
            font-size: .85rem;
            font-weight: 600;
            color: #374151;
        }

        .form-footer {
            padding: 16px 24px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 9px 20px;
            border: 1.5px solid #e4e4e7;
            background: #fff;
            border-radius: 9px;
            color: #71717a;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
        }

        .btn-cancel:hover {
            border-color: #fca5a5;
            color: #dc2626;
            background: #fef2f2;
        }

        .btn-save {
            padding: 9px 22px;
            border: none;
            background: linear-gradient(135deg, var(--y1), var(--yd));
            border-radius: 9px;
            color: #18181b;
            font-size: .875rem;
            font-weight: 800;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: .2s;
            box-shadow: 0 3px 10px rgba(247, 223, 121, .3);
        }

        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(247, 223, 121, .4);
        }

        /* Quick Add Category Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .modal-box {
            background: #fff;
            border: 1.5px solid #f0e8a0;
            border-radius: 18px;
            width: 100%;
            max-width: 360px;
            padding: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .1);
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #18181b;
            margin-bottom: 8px;
        }

        .modal-sub {
            font-size: .8rem;
            color: #71717a;
            margin-bottom: 18px;
        }

        .m-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-m {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-m-cancel {
            background: #f4f4f5;
            color: #71717a;
        }

        .btn-m-save {
            background: var(--y1);
            color: var(--ydark);
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        .toast {
            min-width: 280px;
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
            border: 1px solid var(--y1);
            transform: translateX(100%);
            transition: .3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            pointer-events: auto;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ti-success {
            background: var(--y2);
            color: var(--ydark);
        }

        .ti-error {
            background: #fee2e2;
            color: #dc2626;
        }

        .toast-msg {
            font-size: .85rem;
            font-weight: 600;
            color: #18181b;
        }

        /* Delete Modal Specifics */
        .modal-box.delete-confirm {
            border-color: #fee2e2;
            max-width: 400px;
        }

        .btn-m-delete {
            background: #ef4444;
            color: #fff;
        }
    </style>

    <div class="pg-header">
        <a href="{{ route('products.index') }}" class="back-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="15 18 9 12 15 6" />
            </svg>
        </a>
        <div>
            <div class="pg-title">New Product</div>
            <div class="pg-sub">Add a retail product or service supply</div>
        </div>
    </div>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px;margin-bottom:16px;">
                <ul style="margin:0;padding-left:20px;color:#ef4444;font-size:0.85rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">

            <div class="form-section">
                <div class="section-title">
                    <div class="section-icon"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg></div>
                    Basic Information
                </div>
                <div class="f-grid-4">
                    <div>
                        <label class="f-label">Product Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" required class="f-input" placeholder="e.g. Shampoo 500ml">
                    </div>
                    <div>
                        <label class="f-label">SKU</label>
                        <input type="text" name="sku" class="f-input" placeholder="Auto-generated if blank">
                    </div>
                    <div>
                        <label class="f-label">Product Type <span style="color:#ef4444;">*</span></label>
                        <select name="product_type" id="product_type" required class="f-select">
                            <option value="retail">For Sale</option>
                            <option value="service_supply">Shop Use</option>
                        </select>
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label class="f-label" style="margin-bottom:0;">Category</label>
                            <div style="display:flex;gap:12px;">
                                <button type="button" onclick="showCatModal()"
                                    style="font-size:.65rem;font-weight:800;color:var(--ydark);background:none;border:none;cursor:pointer;padding:0;">+
                                    Add New</button>
                                <button type="button" id="delete_cat_btn" onclick="deleteSelectedCat()"
                                    style="font-size:.65rem;font-weight:800;color:#ef4444;background:none;border:none;cursor:pointer;padding:0;">-
                                    Delete Selected</button>
                            </div>
                        </div>
                        <select name="category_id" id="cat_select" class="f-select">
                            <option value="">No Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="f-row">
                    <label class="f-label">Description <span
                            style="color:#a1a1aa;font-weight:400;">(optional)</span></label>
                    <textarea name="description" class="f-textarea" placeholder="Product description…"></textarea>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <div class="section-icon"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                        </svg></div>
                    Pricing &amp; Inventory
                </div>
                <div class="f-grid-3" style="margin-bottom:14px;">
                    <div id="selling_price_wrapper">
                        <label class="f-label">Selling Price (PKR) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="selling_price" id="selling_price" min="0" step="0.01" required class="f-input"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="f-label">Cost Price (PKR)</label>
                        <input type="number" name="cost_price" min="0" step="0.01" class="f-input"
                            placeholder="Purchase cost">
                    </div>
                    <div>
                        <label class="f-label">Current Stock <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="current_stock" min="0" step="1" value="0" required class="f-input">
                    </div>
                </div>
                <div class="f-grid-2" style="margin-bottom:14px;">
                    <div>
                        <label class="f-label">Min Stock Level</label>
                        <input type="number" name="min_stock_level" min="0" step="1" class="f-input"
                            placeholder="Low stock threshold">
                    </div>
                    <div>
                        <label class="f-label">Supplier</label>
                        <select name="supplier_id" class="f-select">
                            <option value="">No Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label class="check-row">
                    <input type="checkbox" name="track_inventory" checked>
                    <span class="check-lbl">Track inventory for this product</span>
                </label>
            </div>

            <div class="form-footer">
                <a href="{{ route('products.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Create Product</button>
            </div>
        </div>
    </form>
    </div>
    </form>

    {{-- Quick Add Modal --}}
    <div class="modal-overlay" id="catModal">
        <div class="modal-box">
            <div class="modal-title">New Category</div>
            <div class="modal-sub">Create a new category for your products</div>
            <div class="f-row">
                <label class="f-label">Category Name</label>
                <input type="text" id="new_cat_name" class="f-input" placeholder="e.g. Skin Care">
            </div>
            <div class="m-footer">
                <button type="button" class="btn-m btn-m-cancel" onclick="hideCatModal()">Cancel</button>
                <button type="button" class="btn-m btn-m-save" id="save_cat_btn">Create Category</button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal-overlay" id="delCatModal">
        <div class="modal-box delete-confirm">
            <div class="modal-title" style="color:#ef4444;">Delete Category?</div>
            <div class="modal-sub" id="del_msg_text">Are you sure you want to delete this category? All products in it will
                be affected.</div>
            <div class="m-footer">
                <button type="button" class="btn-m btn-m-cancel" onclick="hideDelModal()">Keep Category</button>
                <button type="button" class="btn-m btn-m-delete" id="confirm_del_btn">Delete Permanently</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast_container"></div>

    <script>
        function showCatModal() { document.getElementById('catModal').style.display = 'flex'; }
        function hideCatModal() { document.getElementById('catModal').style.display = 'none'; document.getElementById('new_cat_name').value = ''; }

        function showDelModal() { document.getElementById('delCatModal').style.display = 'flex'; }
        function hideDelModal() { document.getElementById('delCatModal').style.display = 'none'; }

        function showToast(msg, type = 'success') {
            const container = document.getElementById('toast_container');
            const toast = document.createElement('div');
            toast.className = `toast`;
            toast.innerHTML = `
            <div class="toast-icon ${type === 'success' ? 'ti-success' : 'ti-error'}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="${type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'}"/></svg>
            </div>
            <div class="toast-msg">${msg}</div>
        `;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        const catSelect = document.getElementById('cat_select');
        const deleteBtn = document.getElementById('delete_cat_btn');

        catSelect.addEventListener('change', function () {
            deleteBtn.style.display = this.value ? 'inline-block' : 'none';
        });

        let categoryToDelete = null;

        async function deleteSelectedCat() {
            const id = catSelect.value;
            const name = catSelect.options[catSelect.selectedIndex].text;
            if (!id) return;

            categoryToDelete = id;
            document.getElementById('del_msg_text').textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
            showDelModal();
        }

        document.getElementById('confirm_del_btn').addEventListener('click', async function () {
            if (!categoryToDelete) return;

            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Deleting...';

            try {
                const response = await fetch(`{{ url('categories') }}/${categoryToDelete}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: '_method=DELETE'
                });

                const data = await response.json();
                if (data.success) {
                    catSelect.remove(catSelect.selectedIndex);
                    catSelect.dispatchEvent(new Event('change'));
                    showToast(data.message);
                    hideDelModal();
                } else {
                    showToast(data.message || 'Failed to delete', 'error');
                }
            } catch (error) {
                console.error("Error deleting category:", error);
                showToast('Server error. Failed to delete.', 'error');
            }
            btn.disabled = false;
            btn.textContent = 'Delete Permanently';
            categoryToDelete = null;
        });

        document.getElementById('save_cat_btn').addEventListener('click', async function () {
            const name = document.getElementById('new_cat_name').value;
            if (!name) { showToast('Please enter a name', 'error'); return; }

            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Saving...';

            const response = await fetch("{{ route('categories.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ name: name, type: 'product' })
            });

            const data = await response.json();
            if (data.success) {
                const option = new Option(data.category.name, data.category.id, true, true);
                catSelect.add(option);
                catSelect.dispatchEvent(new Event('change'));
                showToast(data.message || 'Category added!');
                hideCatModal();
            } else {
                showToast('Failed to create category', 'error');
            }
            btn.disabled = false;
            btn.textContent = 'Create Category';
        });

        const productType = document.getElementById('product_type');
        const sellingPriceWrapper = document.getElementById('selling_price_wrapper');
        const sellingPriceInput = document.getElementById('selling_price');

        productType.addEventListener('change', function() {
            if(this.value === 'service_supply') {
                sellingPriceWrapper.style.display = 'none';
                sellingPriceInput.required = false;
                if (!sellingPriceInput.value) sellingPriceInput.value = '0';
            } else {
                sellingPriceWrapper.style.display = 'block';
                sellingPriceInput.required = true;
            }
        });
        
        productType.dispatchEvent(new Event('change'));
    </script>
@endsection