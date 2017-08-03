<style type="text/css">
    .login {margin-top: 25px;}
    .login_form {background: #f1f1f1; height: 35px; padding: 10px 0 0 10px; width: 560px;}
    .login_form ul li {float: left; padding-right: 20px;}
    .login_form ul li label {position: relative; top: -3px; font-weight: bold;}
    .login_form ul li input {border: 1px solid #dfdfdf; width: 145px;}

    .error {color: red;}
</style>

<div class="login">
    <p class="pb_20">Once registered, enter your EZWebPlayer account information below:</p>
    <?php if (!empty ($msg)) :?>
        <p class="error"><?php echo $msg; ?></p>
    <?php endif; ?>
    <div class="login_form">
        <form method="post" action="">
            <ul>
                <li>
                    <label for="email">E-Mail:</label>
                    <input name="userLogin" id="email" />
                </li>
                <li>
                    <label for="password">Password:</label>
                    <input name="userPass" id="password" type="password" />
                </li>
                <li><input type="submit" value="Log in" class="w_60" name="login" /></li>
            </ul>
        </form>
    </div>
</div>