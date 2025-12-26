@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Account Details')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Payments /</span> Account Details
        </h4>

        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <h5 class="card-header">Bank Account Details</h5>
                    <div class="card-body">
                        <form action="{{ route('trainer.payments.account-details.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="account_holder_name" class="form-label">Account Holder Name *</label>
                                    <input class="form-control" type="text" id="account_holder_name"
                                        name="account_holder_name"
                                        value="{{ old('account_holder_name', $bankDetails->account_holder_name ?? '') }}"
                                        placeholder="Use name as per bank records" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="bank_name" class="form-label">Bank Name *</label>
                                    <input class="form-control" type="text" name="bank_name" id="bank_name"
                                        value="{{ old('bank_name', $bankDetails->bank_name ?? '') }}"
                                        placeholder="e.g. HDFC Bank" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="account_number" class="form-label">Account Number *</label>
                                    <input class="form-control" type="text" name="account_number" id="account_number"
                                        value="{{ old('account_number', $bankDetails->account_number ?? '') }}"
                                        placeholder="Enter account number" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="ifsc_code" class="form-label">IFSC Code *</label>
                                    <input class="form-control text-uppercase" type="text" name="ifsc_code" id="ifsc_code"
                                        value="{{ old('ifsc_code', $bankDetails->ifsc_code ?? '') }}"
                                        placeholder="Enter IFSC Code" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="upi_id" class="form-label">UPI ID (Optional)</label>
                                    <input class="form-control" type="text" name="upi_id" id="upi_id"
                                        value="{{ old('upi_id', $bankDetails->upi_id ?? '') }}"
                                        placeholder="e.g. name@okaxis" />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection