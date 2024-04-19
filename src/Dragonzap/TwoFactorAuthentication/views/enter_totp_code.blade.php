<p>

    @if (empty($totps))
        <p class="text-danger">You have TOTP authentication enabled but have not set up any TOTP authenticator apps.
            Please
            set up an authenticator app to use TOTP authentication. You will need to contact support to solve this
            account problem</p>
    @else
        @if ($errors->has('code'))
            <div class="alert alert-danger">
                {{ $errors->first('code') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('dragonzap.two_factor_confirm_code') }}">
            @csrf
            <div class="form-group">
                <label for="code">Code on your authenticator application</label>
                @if(count($totps) == 1)
                <input type="hidden" name="totp_id" value="{{ $totps[0]->id }}">
                @else
                <label for="totp_id">Select the authenticator</label>
                <select name="totp_id" id="totp_id" class="form-control">
                    @foreach($totps as $totp)
                    <option value="{{ $totp->id }}">{{ $totp->friendly_name }}</option>
                    @endforeach
                </select>

                @endif
                <input type="text" name="code" id="code" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Confirm Identity</button>
        </form>

    @endif

    You can edit this file at "resources/views/vendor/dragonzap_2factor/enter_totp_code.blade.php" once the views have
    been
    published with vendor:publish

</p>
