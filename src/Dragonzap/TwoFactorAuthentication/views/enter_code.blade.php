<p>
    We have sent you a two factor authentication code to your email address. Please enter the code below.

    <form method="POST" action="">
        @csrf
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

You can edit this file at "resources/views/vendor/dragonzap_2factor/enter_code.blade.php" once the views have been published with vendor:publish

</p>