@extends('layouts.app')
@section('title', 'Quotation Details')
@section('page-title', 'Quotation Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Quotation #{{ $quotation->quotation_number }} (v{{ $quotation->version_number }})</span>
                <div>
                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    @if($quotation->items && count($quotation->items) > 0)
                        <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="btn btn-success btn-sm me-1">
                            <i class="bi bi-printer"></i> Print / Export PDF
                        </a>
                    @endif
                    <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit Quotation
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Status Quick Changer -->
                <div class="mb-4 p-3 bg-light rounded d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small d-block">Current Status</span>
                        @php $cls = ['draft'=>'secondary','sent'=>'primary','approved'=>'success','rejected'=>'danger'][$quotation->status] ?? 'secondary' @endphp
                        <span class="badge bg-{{ $cls }} text-uppercase fs-6">{{ $quotation->status }}</span>
                    </div>
                    <form action="{{ route('quotations.status', $quotation) }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select form-select-sm" style="width: 150px;">
                            <option value="draft" {{ $quotation->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ $quotation->status === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="approved" {{ $quotation->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $quotation->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Update Status</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th class="w-30 bg-light">Quotation ID</th>
                                <td><strong class="text-primary">{{ $quotation->quotation_number }}</strong></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Version</th>
                                <td><code>v{{ $quotation->version_number }}</code></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Customer Name</th>
                                <td>{{ $quotation->customer_name }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Quotation Date</th>
                                <td>{{ $quotation->quotation_date ? $quotation->quotation_date->format('d M Y') : '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Quotation Value</th>
                                <td>
                                    @if($quotation->quotation_value)
                                        <strong class="text-success">₹{{ number_format($quotation->quotation_value, 2) }}</strong>
                                    @else –
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Associated Opportunity</th>
                                <td>
                                    @if($quotation->opportunity)
                                        <a href="{{ route('opportunities.show', $quotation->opportunity) }}" class="fw-semibold text-primary text-decoration-none">
                                            {{ $quotation->opportunity->opportunity_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">No associated opportunity</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Prepared By</th>
                                <td>{{ $quotation->preparedBy?->name ?? '–' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Notes / Terms</th>
                                <td>{!! nl2br(e($quotation->notes)) ?: '<span class="text-muted">No notes or terms provided.</span>' !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($quotation->items && count($quotation->items) > 0)
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-list-stars me-1"></i> Quotation Items & Pricing</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Item Name</th>
                                        <th>Desc / Location</th>
                                        <th class="text-end" style="width: 85px;">GST</th>
                                        <th class="text-end" style="width: 70px;">Qty</th>
                                        <th class="text-end" style="width: 110px;">Rate (₹)</th>
                                        <th class="text-end" style="width: 85px;">Disc (%)</th>
                                        <th class="text-end" style="width: 120px;">Amount (₹)</th>
                                        <th class="text-end" style="width: 110px;">CGST (₹)</th>
                                        <th class="text-end" style="width: 110px;">SGST (₹)</th>
                                        <th class="text-end" style="width: 120px;">Total (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subTotal = 0;
                                        $totalDiscount = 0;
                                        $totalTaxable = 0;
                                        $totalCgst = 0;
                                        $totalSgst = 0;
                                    @endphp
                                    @foreach($quotation->items as $index => $item)
                                        @php
                                            $qty = floatval($item['quantity'] ?? 0);
                                            $rate = floatval($item['rate'] ?? 0);
                                            $disc = floatval($item['discount'] ?? 0);
                                            $gst = floatval($item['gst_rate'] ?? 0);
                                            
                                            $gross = $qty * $rate;
                                            $discAmt = $gross * ($disc / 100);
                                            $net = $gross - $discAmt;
                                            $cgst = $net * ($gst / 2) / 100;
                                            $sgst = $net * ($gst / 2) / 100;
                                            $rowTotal = $net + $cgst + $sgst;
                                            
                                            $subTotal += $gross;
                                            $totalDiscount += $discAmt;
                                            $totalTaxable += $net;
                                            $totalCgst += $cgst;
                                            $totalSgst += $sgst;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $item['name'] }}</td>
                                            <td class="text-muted small">{{ $item['description'] ?: '–' }}</td>
                                            <td class="text-end">{{ $gst }}%</td>
                                            <td class="text-end">{{ $qty }}</td>
                                            <td class="text-end">₹{{ number_format($rate, 2) }}</td>
                                            <td class="text-end">{{ $disc > 0 ? $disc.'%' : '–' }}</td>
                                            <td class="text-end">₹{{ number_format($net, 2) }}</td>
                                            <td class="text-end text-success">₹{{ number_format($cgst, 2) }}</td>
                                            <td class="text-end text-success">₹{{ number_format($sgst, 2) }}</td>
                                            <td class="text-end fw-semibold">₹{{ number_format($rowTotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    @php
                                        $totalPreRound = $totalTaxable + $totalCgst + $totalSgst;
                                        $grandTotal = round($totalPreRound);
                                        $roundUp = $grandTotal - $totalPreRound;
                                    @endphp
                                    <tr class="fw-bold">
                                        <td colspan="7" class="text-end">Sub Total:</td>
                                        <td class="text-end">₹{{ number_format($subTotal, 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold text-danger">
                                        <td colspan="7" class="text-end">Discount:</td>
                                        <td class="text-end">₹{{ number_format($totalDiscount, 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="7" class="text-end">Taxable Amount:</td>
                                        <td class="text-end">₹{{ number_format($totalTaxable, 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold text-success">
                                        <td colspan="7" class="text-end">Total CGST:</td>
                                        <td></td>
                                        <td class="text-end">₹{{ number_format($totalCgst, 2) }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold text-success">
                                        <td colspan="7" class="text-end">Total SGST:</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end">₹{{ number_format($totalSgst, 2) }}</td>
                                        <td></td>
                                    </tr>
                                    <tr class="fw-bold text-muted">
                                        <td colspan="7" class="text-end">Round Up:</td>
                                        <td colspan="4" class="text-end">₹{{ ($roundUp >= 0 ? '+' : '') . number_format($roundUp, 2) }}</td>
                                    </tr>
                                    <tr class="fw-bold table-primary fs-5">
                                        <td colspan="7" class="text-end">Grand Total (INR):</td>
                                        <td colspan="4" class="text-end">₹{{ number_format($grandTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Attached Documents Section -->
                <div class="mt-4 border-top pt-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-paperclip me-1"></i> Attached Documents</h6>
                    @if($quotation->documents->count() > 0)
                        <div class="list-group">
                            @foreach($quotation->documents as $doc)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-secondary text-uppercase me-2" style="font-size: 0.65rem;">
                                            {{ str_replace('_', ' ', $doc->category) }}
                                        </span>
                                        <a href="{{ route('documents.show', $doc) }}" class="fw-semibold text-primary text-decoration-none small">
                                            {{ $doc->original_name }}
                                        </a>
                                        <span class="text-muted small ms-2">({{ number_format($doc->file_size / 1024, 1) }} KB)</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('documents.download', $doc) }}" class="btn btn-xs btn-outline-primary btn-sm"><i class="bi bi-download"></i></a>
                                        <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Delete this file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No documents attached to this quotation.</p>
                    @endif
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="text-muted small">Created By: {{ $quotation->createdBy?->name ?? 'System' }}</span>
                    <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Quotation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
