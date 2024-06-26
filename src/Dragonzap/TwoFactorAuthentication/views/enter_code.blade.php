<p>
    We have sent you a two factor authentication code to your email address. Please enter the code below. <a
        href="{{ route('dragonzap.two_factor_generate_code') }}">Click here</a> to request a new code.

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
        <label for="code">Code</label>
        <input type="text" name="code" id="code" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Confirm Identity</button>
</form>

You can edit this file at "resources/views/vendor/dragonzap_2factor/enter_code.blade.php" once the views have been
published with vendor:publish

</p>
