<p>
    We have sent you a two factor authentication code to your email address. Please enter the code below.

    <form method="POST" action="{{ route('dragonzap.two_factor_authentication.login') }}">
        @csrf
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

</p>