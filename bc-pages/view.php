<?php

$t->add_script("js","jquery");
$t->add_script("js","global");
$t->add_script("js","jquery.color");
$t->add_script("js","jquery.resize");
$t->add_script("custom",'
$("#resize").autoResize({
    onResize : function() {
        $(this).css({opacity:0.8});
    },
    animateCallback : function() {
        $(this).css({opacity:1});
    },
    animateDuration : 300,
    extraSpace : 0
});

function delete_comment(id,p_id) {
    var comments = parseInt($("#comment-count").html());
    $("#comment-count").delay().fadeOut("slow");
    comments -= 1;
    if ( confirm("Are you sure you want to delete this comment?") ) {
    $.post("'.BRIGGLE_INC.'helper-ajax.php", { action: "delete_comment", id: id , p_id: p_id },
    function(data){
        if ( data.result == "true" ) {
            $("#comment-"+id).delay().slideUp("slow");
            $("#comment-count").html(comments);
            $("#comment-count").delay().fadeIn("slow");
        } else {
            alert("There was an error deleting the comment.");
        }
    }, "json");
    }
 }
');

?>

<?php $t->head(); ?>

<?php $post = $controller->_post; $comments = $controller->_comments; ?>

<div id="view">
    <div class="post">
        <?php if ( $controller->errors ) get_message($controller->errors , "error"); ?>
        <div class="post-body">
            <?php echo format_post($post['content']); ?>
            <?php if ( count($post['uploads']) > 0 ): ?>
                <div class="post-uploads">
                <?php foreach ( $post['uploads'] as $upload): ?>
                    <img src="<?php echo $upload['directory']; ?>" alt="<?php echo $upload['name']; ?>" title="<?php echo $upload['name']; ?>" class="post-image" />
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <h3>
            By <?php echo ( $post['u_ID'] == $user->get('ID') ? 'You' : $post['author'] ); ?> <small>|</small>
            <span><?php echo relative($post['date']); ?></span> <small>|</small>
            <?php echo '<span id="comment-count">'.count($comments).'</span> Comment'.(($post['comments'] == 1) ? '' : 's'); ?>
            <?php echo ($user->get('type') > 1 || $post['u_ID'] == $user->get('ID') ? ' <small>|</small> <a href="'.BRIGGLE_DIR.'edit/'.$post['ID'].'">Edit Post</a>' : ''); ?>
        </h3>

        <div id="comments">

        <?php if ( !empty($comments) ): ?>

            <?php foreach ($comments as $comment): ?>
                <div class="comment<?php echo ($i++ % 2 ? '': ' alt') ?>" id="comment-<?php echo $comment['ID']; ?>">
                    <?php if ($user->get('type') > 1 || $user->get('ID') == $comment['u_ID'] ): ?><a href="javascript:;" class="comment-delete" onclick="delete_comment(<?php echo $comment['ID']; ?>,<?php echo $post['ID']; ?>);">x</a><?php endif; ?>
                    <div class="comment-meta">
                        <span class="comment-author"><?php echo ( $comment['u_ID'] == $user->get('ID') ? 'You' : $comment['author'] ); ?></span>
                        <span class="comment-date"><?php echo relative($comment['date']); ?></span>
                    </div>
                    <div class="comment-body"><?php echo format_post($comment['comment']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ( $user->get('type') > 0 ): ?>
        <h3>Add a Comment</h3>
        <fieldset>
        <form action="<?php echo BRIGGLE_DIR.'view/'.$post['ID']; ?>" method="post" class="comment-form">

            <div class="form-entry">
                <textarea name="comment" cols="50" rows="10" id="resize"></textarea>
            </div>

            <div class="form-footer">
                <input type="submit" name="submit-comment" value="Submit Comment" class="large green button" />
            </div>

        </form>
        </fieldset>
        <?php endif; ?>

        </div>
    </div>
</div>

<?php $t->foot(); ?>
