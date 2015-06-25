<?php

$t->add_script("js","jquery");
$t->add_script("js","global");

?>

<?php $t->head(); ?>

<?php $author = $controller->_author; ?>

<div id="settings">
    <div class="post">
    <fieldset>
    <form action="<?php echo BRIGGLE_DIR.'settings'; ?>" method="post" class="form">

        <div class="form-entry">
        <label for="company">Company</label>
        <input type="text" name="company" class="text" value="<?php echo $setting['company']; ?>" />
        </div>

        <div class="form-entry">
            <label for="private">Private</label>
            <select name="private" class="text w50">
        <option value="no"<?php echo ($setting['private'] == "no" ? ' selected="selected"': ''); ?>>No, Make it public</option>
        <option value="yes"<?php echo ($setting['private'] == "yes" ? ' selected="selected"': ''); ?>>Yes, Keep it private</option>
            </select>
        </div>

        <div class="form-entry">
            <label for="password">Password <span>Guest password. Letters and numbers only.</span></label>
            <input type="text" name="password" class="text w50" value="<?php echo $setting['password']; ?>" />
        </div>

        <div class="form-entry">
            <label for="theme">Theme</label>
            <select name="theme" class="text w50">
        <?php foreach ( get_themes() as $theme ): ?>
            <option value="<?php echo $theme; ?>"<?php echo ($setting['theme'] == $theme ? ' selected="selected"': ''); ?>><?php echo $theme; ?></option>
        <?php endforeach; ?>
            </select>
        </div>

        <div class="form-entry">
            <label for="per_page">Per Page <span>Number of posts per page.</span></label>
            <select name="per_page" class="text w25">
            <option value="5"<?php echo ($setting['per_page'] == 5 ? ' selected="selected"': ''); ?>>5</option>
        <option value="10"<?php echo ($setting['per_page'] == 10 ? ' selected="selected"': ''); ?>>10</option>
        <option value="15"<?php echo ($setting['per_page'] == 15 ? ' selected="selected"': ''); ?>>15</option>
        <option value="25"<?php echo ($setting['per_page'] == 25 ? ' selected="selected"': ''); ?>>25</option>
        <option value="50"<?php echo ($setting['per_page'] == 50 ? ' selected="selected"': ''); ?>>50</option>
            </select>
        </div>

        <div class="form-footer">
        <input type="submit" name="update-settings" value="Update Settings" class="large green button" />
        </div>

    </form>
    </fieldset>
    </div>
</div>

<?php $t->foot(); ?>