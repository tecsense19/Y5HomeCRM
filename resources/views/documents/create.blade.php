@extends('layouts.app')
@section('title', 'Upload Document')
@section('page-title', 'Upload Document')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Upload New Document</span>
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                        <label class="form-label" for="file">Choose File <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="file" class="form-control" required>
                        <div class="form-text">Supported formats: PDF, DOCX, XLSX, JPG, PNG (Max size: 25 MB).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="category">Category <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <option value="site_photos">Site Photos</option>
                            <option value="quotations">Quotations</option>
                            <option value="drawings">Drawings</option>
                            <option value="agreements">Agreements</option>
                        </select>
                    </div>

                    @php
                        $customers = \App\Models\Customer::orderBy('name')->get();
                        $leads = \App\Models\Lead::orderBy('customer_name')->get();
                        $opportunities = \App\Models\Opportunity::orderBy('opportunity_number')->get();
                        $quotations = \App\Models\Quotation::orderBy('quotation_number')->get();

                        $presetModule = request()->query('module');
                        $presetId = request()->query('id');

                        $selectedType = '';
                        $targetBlock = '';
                        if ($presetModule === 'customer') {
                            $selectedType = 'App\Models\Customer';
                            $targetBlock = 'customer_block';
                        } elseif ($presetModule === 'lead') {
                            $selectedType = 'App\Models\Lead';
                            $targetBlock = 'lead_block';
                        } elseif ($presetModule === 'opportunity') {
                            $selectedType = 'App\Models\Opportunity';
                            $targetBlock = 'opp_block';
                        } elseif ($presetModule === 'quotation') {
                            $selectedType = 'App\Models\Quotation';
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
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any details or description..."></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-arrow-up"></i> Upload
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
