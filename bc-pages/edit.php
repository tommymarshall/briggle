<?php

$t->add_script("js","jquery");
$t->add_script("js","global");
$t->add_script("js","jquery.resize");
$t->add_script("custom",'
$("#resize").autoResize({
    onResize : function() {
        $(this).css({opacity:0.8});
    },
    animateCallback : function() {
        $(this).css({opacity:1});
    },
    animateDuration : 500,
    extraSpace : 20
});
$("#resize").keydown();

$(function() {
    $("input:file").live("change",function (){
        $("#file-list").append("<input type=\"file\" name=\"files[]\" /><br />")
    });
});
');

?>

<?php $t->head(); ?>

<?php $post = $controller->_post; ?>

<div id="edit">
    <div class="post">
        <p>You are now in editing mode. After you are done editing the title and content just hit the update Update Post button. This article was last updated on <strong><?php echo relative($post['date']); ?></strong>.</p>
        <?php if ( $controller->errors ) get_message($controller->errors , "error"); ?>
        <fieldset>
        <form action="<?php echo BRIGGLE_DIR.'edit/'.$post['ID']; ?>" enctype="multipart/form-data" method="post" class="form">

            <div class="form-entry">
                <label for="title">Title</label>
                <input type="text" name="title" class="text w75" value="<?php echo stripslashes(html_entity_decode($post['title'])); ?>" />
            </div>

            <div class="form-entry">
                <label for="content">Content</label>
                <textarea name="content" cols="50" rows="10" id="resize"><?php echo stripslashes(html_entity_decode($post['content'])); ?></textarea>
            </div>

            <?php if ( count($post['uploads']) > 0 ): ?>
            <div class="form-entry">
                <label for="current_uploads">Images</label>
                <?php foreach ( $post['uploads'] as $upload): ?>
                    <div class="post-image-box">
                        <img src="<?php echo $upload['directory']; ?>" alt="<?php echo $upload['name']; ?>" title="<?php echo $upload['name']; ?>" class="post-image-small" /><br /><label class="for-remove-image"><input type="checkbox" name="current_uploads[]" value="<?php echo $upload['ID']; ?>" /> Remove Image</label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="form-entry">
                <label for="files">&nbsp;</label>
                <div id="file-list">
                <input type="file" name="files[]" /><br />
                </div>
            </div>

            <div class="form-footer">
                <input type="submit" name="delete-post" value="Delete Post" class="large red button"  onclick="return confirm('Are you sure you want to delete this post?');" /> <input type="submit" name="edit-post" value="Update Post" class="large green button" />
            </div>

        </form>
        </fieldset>
    </div>
</div>

<?php $t->foot(); ?>