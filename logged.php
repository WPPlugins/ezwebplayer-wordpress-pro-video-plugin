<style type="text/css">
    .logged {margin: 25px 0 0 10px; width: 400px;}
    .logged p {padding-bottom: 5px;}
    .logged ul {margin: 10px 0 0 0; width: 250px;}
    .logged ul li {padding-bottom: 5px;}
    .logged ul li label {position: relative; top: 3px;}
    .logged ul li input {float: right; border: 1px solid #dfdfdf; width: 120px;}

    p.green { padding: 0 0  3px 5px !important; border: 1px solid green !important; color: green !important; background-color: greenyellow !important;}
</style>

<div class="logged">
    <p>Logged in as <?php echo $userEmail ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url() . 'admin.php/admin.php?page=ezwebplayer.php&action=logout'?>">Log Out</a></p>
    <p>
        <a href="<?php echo admin_url() . 'admin.php?page=/ezwebplayer.php&action=recent'; ?>">Recently posted videos</a> |
        <a href="<?php echo admin_url() . 'admin.php?page=ezwebplayer-wordpress-pro-video-plugin/ezwebplayer.php&action=post_form' ?>">Post a video</a>
    </p>

    <p class="pt_20">If you need to update your password please do so below.</p>

    <?php if (!empty ($msg)) :?>
        <p class="<?php echo ($error) ? 'error' : 'green'; ?>"><?php echo $msg; ?></p>
    <?php endif; ?>
    <form action="" method="post">
        <input name="email" type="hidden" value="<?php echo $userEmail ?>">
        <ul>
            <li>
                <label for="old">Old Password</label>
                <input name="oldpassword" id="old" type="password" />
                <div class="clear"></div>
            </li>
            <li>
                <label for="new">New Password</label>
                <input name="newpassword" id="new" type="password" />
                <div class="clear"></div>
            </li>
            <li><input type="submit" value="Submit" class="w_60" name="change_password"/></li>
        </ul>
    </form>
</div>