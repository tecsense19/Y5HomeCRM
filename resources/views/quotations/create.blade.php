@extends('layouts.app')
@section('title', 'New Quotation')
@section('page-title', 'Create Quotation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form method="POST" action="{{ route('quotations.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card mb-3">
                <div class="card-header">Quotation Details</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="opportunity_id">Link to Opportunity</label>
                            <select name="opportunity_id" id="opportunity_id" class="form-select">
                                <option value="">— Optional —</option>
                                @foreach($opportunities as $opp)
                                    <option value="{{ $opp->id }}" data-customer="{{ $opp->customer_name }}" {{ old('opportunity_id') == $opp->id ? 'selected' : '' }}>
                                        {{ $opp->opportunity_number }} – {{ $opp->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer_name">Customer Name *</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="quotation_date">Quotation Date *</label>
                            <input type="date" name="quotation_date" id="quotation_date" class="form-control" value="{{ old('quotation_date', today()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="quotation_value">Quotation Value (₹)</label>
                            <input type="number" step="0.01" name="quotation_value" id="quotation_value" class="form-control" value="{{ old('quotation_value') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status *</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">Notes / Terms</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Items Builder -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-semibold text-primary"><i class="bi bi-list-stars me-1"></i> Quotation Items & Pricing</span>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-item">
                        <i class="bi bi-plus-lg"></i> Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 align-middle" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Item Name *</th>
                                    <th>Desc / Location</th>
                                    <th style="width: 85px;">GST</th>
                                    <th style="width: 75px;">Qty *</th>
                                    <th style="width: 110px;">Rate (₹) *</th>
                                    <th style="width: 90px;">Disc (%)</th>
                                    <th class="text-end" style="width: 120px;">Amount (₹)</th>
                                    <th class="text-end" style="width: 110px;">CGST (₹)</th>
                                    <th class="text-end" style="width: 110px;">SGST (₹)</th>
                                    <th class="text-end" style="width: 120px;">Total (₹)</th>
                                    <th style="width: 42px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                <!-- Dynamic rows here -->
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="7" class="text-end">Sub Total:</td>
                                    <td class="text-end" id="sub-total-val">₹0.00</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold text-danger">
                                    <td colspan="7" class="text-end">Discount:</td>
                                    <td class="text-end" id="discount-val">₹0.00</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="7" class="text-end">Taxable Amount:</td>
                                    <td class="text-end" id="taxable-val">₹0.00</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold text-success">
                                    <td colspan="7" class="text-end">Total CGST:</td>
                                    <td></td>
                                    <td class="text-end" id="cgst-val">₹0.00</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold text-success">
                                    <td colspan="7" class="text-end">Total SGST:</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end" id="sgst-val">₹0.00</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold text-muted">
                                    <td colspan="7" class="text-end">Round Up:</td>
                                    <td colspan="4" class="text-end" id="round-up-val">₹0.00</td>
                                    <td></td>
                                </tr>
                                <tr class="fw-bold table-primary fs-5">
                                    <td colspan="7" class="text-end">Grand Total (INR):</td>
                                    <td colspan="4" class="text-end" id="grand-total-val">₹0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <input type="hidden" name="items" id="items-json-input">
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Quotation & Technical Documents (Optional)</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="quotation_pdf">Quotation PDF</label>
                            <input type="file" name="quotation_pdf" id="quotation_pdf" class="form-control" accept=".pdf">
                            <div class="form-text">Upload additional/external quotation PDF.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="boq_pdf">BOQ PDF / Spreadsheet</label>
                            <input type="file" name="boq_pdf" id="boq_pdf" class="form-control" accept=".pdf,.xlsx,.xls">
                            <div class="form-text">Upload the detailed Bill of Quantities (BOQ).</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="drawings">CAD Drawings / Layout Layouts</label>
                            <input type="file" name="drawings[]" id="drawings" class="form-control" multiple accept=".pdf,.dwg,.jpg,.jpeg,.png">
                            <div class="form-text">Choose one or more layout/floor plan drawing files.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Create Quotation</button>
                <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const oppSelect = document.getElementById('opportunity_id');
        const custNameInput = document.getElementById('customer_name');

        oppSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const customerName = selectedOption.getAttribute('data-customer');
            if (customerName) {
                custNameInput.value = customerName;
            }
        });

        // Items logic
        let items = [];
        const oldItemsRaw = @json(old('items', []));
        if (oldItemsRaw) {
            if (typeof oldItemsRaw === 'string') {
                try {
                    items = JSON.parse(oldItemsRaw);
                } catch(e) {
                    items = [];
                }
            } else {
                items = oldItemsRaw;
            }
        }

        const btnAddItem = document.getElementById('btn-add-item');
        btnAddItem.addEventListener('click', () => {
            items.push({
                name: '',
                description: '',
                gst_rate: 18,
                quantity: 1,
                rate: 0,
                discount: 0
            });
            renderRows();
        });

        function renderRows() {
            const tbody = document.getElementById('items-tbody');
            tbody.innerHTML = '';
            
            if (items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="12" class="text-center text-muted py-4">No items added yet. Click 'Add Item' to start building the quotation.</td></tr>`;
                updateTotals();
                return;
            }
            
            items.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm item-name" value="${escapeHtml(item.name || '')}" placeholder="e.g. YS - Radius Sport 18W 4K" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm item-desc" value="${escapeHtml(item.description || '')}" placeholder="e.g. Living Room">
                    </td>
                    <td>
                        <select class="form-select form-select-sm item-gst">
                            <option value="0" ${item.gst_rate == 0 ? 'selected' : ''}>0%</option>
                            <option value="5" ${item.gst_rate == 5 ? 'selected' : ''}>5%</option>
                            <option value="12" ${item.gst_rate == 12 ? 'selected' : ''}>12%</option>
                            <option value="18" ${item.gst_rate == 18 ? 'selected' : ''}>18%</option>
                            <option value="28" ${item.gst_rate == 28 ? 'selected' : ''}>28%</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" step="1" class="form-control form-control-sm text-end item-qty" value="${item.quantity || 1}" required>
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" class="form-control form-control-sm text-end item-rate" value="${item.rate || 0}" required>
                    </td>
                    <td>
                        <input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm text-end item-disc" value="${item.discount || 0}">
                    </td>
                    <td class="text-end item-amount">₹0.00</td>
                    <td class="text-end item-cgst">₹0.00</td>
                    <td class="text-end item-sgst">₹0.00</td>
                    <td class="text-end fw-semibold item-total">₹0.00</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" data-index="${index}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                
                tr.querySelector('.item-name').addEventListener('input', (e) => { items[index].name = e.target.value; saveToInput(); });
                tr.querySelector('.item-desc').addEventListener('input', (e) => { items[index].description = e.target.value; saveToInput(); });
                tr.querySelector('.item-gst').addEventListener('change', (e) => { items[index].gst_rate = parseFloat(e.target.value) || 0; calculateRowTotal(tr, index); });
                tr.querySelector('.item-qty').addEventListener('input', (e) => { items[index].quantity = parseInt(e.target.value) || 0; calculateRowTotal(tr, index); });
                tr.querySelector('.item-rate').addEventListener('input', (e) => { items[index].rate = parseFloat(e.target.value) || 0; calculateRowTotal(tr, index); });
                tr.querySelector('.item-disc').addEventListener('input', (e) => { items[index].discount = parseFloat(e.target.value) || 0; calculateRowTotal(tr, index); });
                
                tr.querySelector('.btn-delete-row').addEventListener('click', () => {
                    items.splice(index, 1);
                    renderRows();
                });
                
                tbody.appendChild(tr);
                calculateRowTotal(tr, index, false);
            });
            
            updateTotals();
        }

        function calculateRowTotal(tr, index, triggerUpdate = true) {
            const qty = items[index].quantity || 0;
            const rate = items[index].rate || 0;
            const discount = items[index].discount || 0;
            const gstRate = items[index].gst_rate || 0;
            
            const gross = qty * rate;
            const discAmt = gross * (discount / 100);
            const net = gross - discAmt;
            const cgst = net * (gstRate / 2) / 100;
            const sgst = net * (gstRate / 2) / 100;
            const total = net + cgst + sgst;
            
            tr.querySelector('.item-amount').innerText = '₹' + net.toFixed(2);
            tr.querySelector('.item-cgst').innerText = '₹' + cgst.toFixed(2);
            tr.querySelector('.item-sgst').innerText = '₹' + sgst.toFixed(2);
            tr.querySelector('.item-total').innerText = '₹' + total.toFixed(2);
            
            if (triggerUpdate) {
                updateTotals();
            }
        }

        function updateTotals() {
            let subTotal = 0;
            let totalDiscount = 0;
            let totalTaxable = 0;
            let totalCgst = 0;
            let totalSgst = 0;
            
            items.forEach(item => {
                const qty = item.quantity || 0;
                const rate = item.rate || 0;
                const discount = item.discount || 0;
                const gstRate = item.gst_rate || 0;
                
                const gross = qty * rate;
                const discAmt = gross * (discount / 100);
                const net = gross - discAmt;
                const cgst = net * (gstRate / 2) / 100;
                const sgst = net * (gstRate / 2) / 100;
                
                subTotal += gross;
                totalDiscount += discAmt;
                totalTaxable += net;
                totalCgst += cgst;
                totalSgst += sgst;
            });
            
            const totalPreRound = totalTaxable + totalCgst + totalSgst;
            const grandTotal = Math.round(totalPreRound);
            const roundUp = grandTotal - totalPreRound;
            
            document.getElementById('sub-total-val').innerText = '₹' + subTotal.toFixed(2);
            document.getElementById('discount-val').innerText = '₹' + totalDiscount.toFixed(2);
            document.getElementById('taxable-val').innerText = '₹' + totalTaxable.toFixed(2);
            document.getElementById('cgst-val').innerText = '₹' + totalCgst.toFixed(2);
            document.getElementById('sgst-val').innerText = '₹' + totalSgst.toFixed(2);
            document.getElementById('round-up-val').innerText = '₹' + (roundUp >= 0 ? '+' : '') + roundUp.toFixed(2);
            document.getElementById('grand-total-val').innerText = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
            const valInput = document.getElementById('quotation_value');
            if (valInput) {
                valInput.value = grandTotal;
            }
            
            saveToInput();
        }

        function saveToInput() {
            document.getElementById('items-json-input').value = JSON.stringify(items);
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initial render
        renderRows();
    });
</script>
@endpush
@endsection
