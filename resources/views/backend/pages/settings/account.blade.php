@extends('backend.layouts.master')

@section('title')
{{ __('Account') }}
@endsection

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right"></div>
                <h4 class="page-title">{{ __('Account') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('users.change-password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group input-group-merge">

                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                <div class="input-group-text" data-password="false">
                                    <span class="password-eye"></span>
                                </div>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                       

                        <div class="mb-3">
                            <label for="confirm_password">{{ __('Confirm Password') }}</label>
                            <div class="input-group input-group-merge">

                            <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm-password" name="confirm_password">
                            <div class="input-group-text" data-confirm-password="false">
                                    <span class="password-eye"></span>
                                </div>
                            </div>    
                            @error('confirm_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    // Show success message in SweetAlert
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3000
    });
    @endif

    // Show validation errors in SweetAlert
    @if($errors-> any())
    let errorMessages = "";
    @foreach($errors-> all() as $error)
    errorMessages += "• {{ $error }}\n";
    @endforeach

    Swal.fire({
        icon: 'error',
        title: 'Validation Errors!',
        text: errorMessages,
        confirmButtonColor: '#d33'
    });
    @endif
</script>
@endpush