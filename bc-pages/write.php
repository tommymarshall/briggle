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

$(function() {
    $("input:file").live("change",function (){
        $("#file-list").append("<input type=\"file\" name=\"files[]\" /><br />")
    });
});
');

?>

<?php $t->head(); ?>

<?php $post = $controller->_post; ?>

<div id="write">
    <div class="post">
        <?php if ( $controller->errors ) get_message($controller->errors , "error"); ?>
        <fieldset>
        <form action="<?php echo BRIGGLE_DIR.'write'; ?>" enctype="multipart/form-data" method="post" class="form">

            <div class="form-entry">
                <label for="title">Title</label>
                <input type="text" name="title" class="text w75" value="<?php echo htmlentities($_POST['title']); ?>" />
            </div>

            <div class="form-entry">
                <label for="content">Content</label>
                <textarea name="content" cols="50" rows="10" id="resize"><?php echo htmlentities($_POST['content']); ?></textarea>
            </div>

            <div class="form-entry">
                <label for="files">&nbsp;</label>
                <div id="file-list">
                <input type="file" name="files[]" /><br />
                </div>
            </div>

            <div class="form-footer">
                <input type="reset" value="Reset" class="reset" /> <input type="submit" name="write-post" value="Submit Post" class="large green button" />
            </div>

        </form>
        </fieldset>
    </div>
</div>

<?php $t->foot(); ?>