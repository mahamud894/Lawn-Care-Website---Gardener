{{-- Gardener portal login (optional). $team is null on the "add" form. --}}
<div class="border rounded p-3 mb-3 bg-light">
    <h6 class="fw-bold mb-1"><i class="bi bi-phone me-1"></i> Gardener Portal Login <span class="text-muted fw-normal small">(optional)</span></h6>
    <p class="small text-muted mb-3">Give this member a login so they can see their assigned jobs and mark them done from their phone.</p>

    @if(isset($team) && $team->user)
        <div class="alert alert-success py-2 small mb-3">
            Login active: <strong>{{ $team->user->email }}</strong>
        </div>
    @endif

    <div class="row g-2">
        <div class="col-md-6">
            <label for="login_email" class="form-label small">Login e-mail</label>
            <input type="email" name="login_email" id="login_email" class="form-control @error('login_email') is-invalid @enderror"
                   value="{{ old('login_email', isset($team) ? $team->user?->email : '') }}" placeholder="gardener@example.com">
            @error('login_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="login_password" class="form-label small">
                {{ isset($team) && $team->user ? 'New password (leave empty to keep)' : 'Password' }}
            </label>
            <input type="password" name="login_password" id="login_password" class="form-control @error('login_password') is-invalid @enderror" autocomplete="new-password">
            @error('login_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    @if(isset($team) && $team->user)
        <div class="form-check mt-2">
            <input type="checkbox" name="remove_login" value="1" id="remove_login" class="form-check-input">
            <label for="remove_login" class="form-check-label small text-danger">Remove this login</label>
        </div>
    @endif
</div>
