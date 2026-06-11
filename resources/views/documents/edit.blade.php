@extends('layouts.app')
@section('title', 'Edit Document')
@section('page-title', 'Edit Document')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Edit Document: {{ $document->original_name }}</span>
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label d-block">Current Document</label>
                        <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light mb-3">
                            <i class="bi bi-file-earmark-text fs-3 text-primary"></i>
                            <div class="flex-grow-1">
                                <strong class="d-block">{{ $document->original_name }}</strong>
                                <small class="text-muted">{{ number_format($document->file_size / 1024 / 1024, 2) }} MB</small>
                            </div>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>

                        <label class="form-label" for="file">Upload New File to Replace Current (Optional)</label>
                        <input type="file" name="file" id="file" class="form-control">
                        <div class="form-text text-danger mt-1">
                            <i class="bi bi-exclamation-triangle-fill"></i> Uploading a new file will permanently delete and replace the current file. 
                            Supported formats: PDF, DOCX, XLSX, JPG, PNG (Max size: 25 MB).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="category">Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">-- Select Category --</option>
                            <option value="site_photos" {{ $document->category === 'site_photos' ? 'selected' : '' }}>Site Photos</option>
                            <option value="quotation_pdf" {{ $document->category === 'quotation_pdf' ? 'selected' : '' }}>Quotation Pdf</option>
                            <option value="boq_pdf" {{ $document->category === 'boq_pdf' ? 'selected' : '' }}>Boq Pdf</option>
                            <option value="drawings" {{ $document->category === 'drawings' ? 'selected' : '' }}>Drawings</option>
                            <option value="agreements" {{ $document->category === 'agreements' ? 'selected' : '' }}>Agreements</option>
                        </select>
                    </div>

                    @php
                        $customers = \App\Models\Customer::orderBy('name')->get();
                        $leads = \App\Models\Lead::orderBy('customer_name')->get();
                        $opportunities = \App\Models\Opportunity::orderBy('opportunity_number')->get();
                        $quotations = \App\Models\Quotation::orderBy('quotation_number')->get();

                        $presetModule = null;
                        $presetId = $document->documentable_id;

                        $selectedType = $document->documentable_type;
                        $targetBlock = '';
                        if ($selectedType === 'App\Models\Customer') {
                            $targetBlock = 'customer_block';
                        } elseif ($selectedType === 'App\Models\Lead') {
                            $targetBlock = 'lead_block';
                        } elseif ($selectedType === 'App\Models\Opportunity') {
                            $targetBlock = 'opp_block';
                        } elseif ($selectedType === 'App\Models\Quotation') {
                            $targetBlock = 'quot_block';
                        }
                    @endphp

                    <div class="mb-3">
                        <label class="form-label" for="module_select">Associated Module <span class="text-danger">*</span></label>
                        <select id="module_select" class="form-select" required>
                            <option value="">-- Select Module --</option>
                            <option value="App\Models\Customer" data-target="customer_block" {{ $selectedType === 'App\Models\Customer' ? 'selected' : '' }}>Customer</option>
                            <option value="App\Models\Lead" data-target="lead_block" {{ $selectedType === 'App\Models\Lead' ? 'selected' : '' }}>Lead</option>
                            <option value="App\Models\Opportunity" data-target="opp_block" {{ $selectedType === 'App\Models\Opportunity' ? 'selected' : '' }}>Opportunity</option>
                            <option value="App\Models\Quotation" data-target="quot_block" {{ $selectedType === 'App\Models\Quotation' ? 'selected' : '' }}>Quotation</option>
                        </select>
                    </div>

                    <!-- Hidden inputs submitted to the backend -->
                    <input type="hidden" name="documentable_type" id="documentable_type" value="{{ $selectedType }}">
                    <input type="hidden" name="documentable_id" id="documentable_id" value="{{ $presetId }}">

                    <!-- Customer Dropdown Block -->
                    <div class="mb-3 module-block {{ $targetBlock === 'customer_block' ? '' : 'd-none' }}" id="customer_block">
                        <label class="form-label" for="customer_id">Select Customer <span class="text-danger">*</span></label>
                        <select id="customer_id" class="form-select selector-input">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ ($selectedType === 'App\Models\Customer' && $c->id == $presetId) ? 'selected' : '' }}>{{ $c->name }} ({{ $c->city ?? 'No City' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lead Dropdown Block -->
                    <div class="mb-3 module-block {{ $targetBlock === 'lead_block' ? '' : 'd-none' }}" id="lead_block">
                        <label class="form-label" for="lead_id">Select Lead <span class="text-danger">*</span></label>
                        <select id="lead_id" class="form-select selector-input">
                            <option value="">-- Choose Lead --</option>
                            @foreach($leads as $l)
                                <option value="{{ $l->id }}" {{ ($selectedType === 'App\Models\Lead' && $l->id == $presetId) ? 'selected' : '' }}>{{ $l->lead_number }} - {{ $l->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Opportunity Dropdown Block -->
                    <div class="mb-3 module-block {{ $targetBlock === 'opp_block' ? '' : 'd-none' }}" id="opp_block">
                        <label class="form-label" for="opp_id">Select Opportunity <span class="text-danger">*</span></label>
                        <select id="opp_id" class="form-select selector-input">
                            <option value="">-- Choose Opportunity --</option>
                            @foreach($opportunities as $o)
                                <option value="{{ $o->id }}" {{ ($selectedType === 'App\Models\Opportunity' && $o->id == $presetId) ? 'selected' : '' }}>{{ $o->opportunity_number }} - {{ $o->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quotation Dropdown Block -->
                    <div class="mb-3 module-block {{ $targetBlock === 'quot_block' ? '' : 'd-none' }}" id="quot_block">
                        <label class="form-label" for="quot_id">Select Quotation <span class="text-danger">*</span></label>
                        <select id="quot_id" class="form-select selector-input">
                            <option value="">-- Choose Quotation --</option>
                            @foreach($quotations as $q)
                                <option value="{{ $q->id }}" {{ ($selectedType === 'App\Models\Quotation' && $q->id == $presetId) ? 'selected' : '' }}>{{ $q->quotation_number }} - {{ $q->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any details or description...">{{ $document->notes }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const moduleSelect = document.getElementById('module_select');
        const docTypeInput = document.getElementById('documentable_type');
        const docIdInput = document.getElementById('documentable_id');
        const blocks = document.querySelectorAll('.module-block');

        moduleSelect.addEventListener('change', function () {
            // Hide all sub-selection blocks
            blocks.forEach(block => block.classList.add('d-none'));
            docTypeInput.value = this.value;
            docIdInput.value = '';

            const selectedOption = this.options[this.selectedIndex];
            const targetId = selectedOption.getAttribute('data-target');

            if (targetId) {
                const targetBlock = document.getElementById(targetId);
                targetBlock.classList.remove('d-none');
                
                // Clear any previous selection in other blocks
                blocks.forEach(block => {
                    const selectEl = block.querySelector('select');
                    if (selectEl) selectEl.value = '';
                });
            }
        });

        // Set the final documentable_id from whichever select is visible and changed
        document.querySelectorAll('.selector-input').forEach(select => {
            select.addEventListener('change', function () {
                docIdInput.value = this.value;
            });
        });
    });
</script>
@endpush
@endsection
