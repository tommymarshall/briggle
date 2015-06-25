<?php

$t->add_script("js","jquery");
$t->add_script("js","global");

?>

<?php $t->head(); ?>

<?php $author = $controller->_author; ?>

<div id="account">

    <p>Here you can modify your contact information, notification settings, and other information about yourself. The last time this was modified was <strong><?php echo relative($author['modified']); ?></strong>.</p>
    <?php if ( $controller->errors ) get_message($controller->errors , "error"); ?>
    <fieldset>
    <form action="<?php echo BRIGGLE_DIR.'account'; ?>" method="post" class="form">

        <div class="form-entry">
        <label for="name">Name</label>
        <input type="text" name="name" class="text" value="<?php echo $author['name']; ?>" />
        </div>

        <div class="form-entry">
        <label for="login">Login</label>
        <input type="text" name="login" class="text" value="<?php echo $author['login']; ?>" />
        </div>

        <div class="form-entry">
        <label for="email">Email</label>
        <input type="text" name="email" class="text" value="<?php echo $author['email']; ?>" />
        </div>

        <div class="form-entry">
        <label for="pass">Password <span>Leave blank to keep current.</span></label>
        <input type="password" name="pass" class="text w50" />
        </div>

        <div class="form-entry">
            <label for="notes">Notes <span>A short bio or other details about yourself.</span></label>
            <textarea name="notes" cols="50" rows="5" class="h100" id="resize"><?php echo html_entity_decode($author['notes']); ?></textarea>
        </div>

        <div class="form-entry">
            <label for="notify">Notifications <span>Recieve a notification when a post is created.</span></label>
            <select name="notify" class="text w25">
            <option value="0"<?php echo ($author['notify'] == 0 ? ' selected="selected"': ''); ?>>None</option>
        <option value="1"<?php echo ($author['notify'] == 1 ? ' selected="selected"': ''); ?>>Email</option>
            </select>
        </div>

        <div class="form-footer">
        <input type="submit" name="update-account" value="Update Account" class="large green button" />
        </div>

    </form>
    </fieldset>

</div>

<?php $t->foot(); ?>