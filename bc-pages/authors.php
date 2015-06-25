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

$(window).load(function() {
    $("#pass-notes").hide();
    var state = "up";
    $("#additional-details").click(function(){
        if (state != "up")
         {
            $("#more-details").slideUp("slow");
            state = "up";
            $("#additional-details span").html("Show");
         }
        else
         {
            $("#more-details").slideDown("slow");
            state = "down";
            $("#additional-details span").html("Hide");
         }
    });

    $("#loading").ajaxStop( function() { $(this).fadeOut("slow"); });
});

function edit_author(id) {
    $("#loading").show();
    $.post("'.BRIGGLE_INC.'helper-ajax.php", { action: "get_user", id: id },
    function(data){
        $("#author-caption").html("Edit "+data.name);
        $("#author-id").val(data.ID);
        $("#author-login").val(data.login);
        $("#author-name").val(data.name);
        $("#author-email").val(data.email);
            $("#more-details").slideDown("slow");
            $("#pass-notes").show();
            $("#additional-details span").html("Hide");
        $("#resize").html(data.notes);
        $("#author-notify").val(data.notify);
        $("#author-type").val(data.type);
        $("#author-delete").show();
        $("#author-reset").val("Cancel Editing");
        $("#author-submit").val("Edit Author");
    }, "json");
}

function reset_author() {
    if ( $("#author-id") != "" ) {
        $("#author-caption").html("Add an Author");
        $("#author-id").val("");
        $("#author-name").val("");
        $("#author-login").val("");
        $("#author-email").val("");
            $("#more-details").slideUp("slow");
            $("#pass-notes").hide();
            $("#additional-details span").html("Show");
        $("#resize").html("");
        $("#author-notify").val("0");
        $("#author-type").val("");
        $("#author-reset").val("Reset");
        $("#author-delete").hide();
        $("#author-submit").val("Add Author");
        return true;
    }
    return false;
}
');

?>

<?php $t->head(); ?>

<?php $authors = $controller->_authors; ?>

<div id="authors">

    <h3 class="center">List of Authors</h3>

<?php foreach ($authors as $author): ?>
    <div class="author<?php echo ($i++ % 2 ? ' alt': '') ?>">
        <div class="author-name"><span><?php echo stripslashes($author['name']); ?></span> <span>(<?php echo get_type($author['type']); ?>)</span></div>
        <?php if ( $author['ID'] != 1 ): ?>
        <div class="author-actions"><?php if ( ($user->get('type') == 3) || ( $user->get('type') == 2 && $author['type'] != 3 ) ): ?><a href="javascript:;" onclick="edit_author(<?php echo $author['ID']; ?>)">Edit</a><?php endif; ?></div><?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if ( $user->get('type') > 1 ): ?>
    <h3 class="center" id="author-caption">Add an Author</h3>
    <?php if ( $controller->errors ) get_message($controller->errors , "error", true); ?>
    <fieldset>
    <form action="<?php echo BRIGGLE_DIR.'authors'; ?>" method="post" class="form">
        <div id="loading"></div>
        <input type="hidden" id="author-id" name="u_ID" value="" />

        <div class="form-entry">
        <label for="name">Name</label>
        <input type="text" id="author-name" name="name" class="text" value="" />
        </div>

        <div class="form-entry">
        <label for="login">Login</label>
        <input type="text" id="author-login" name="login" class="text" value="" />
        </div>

        <div class="form-entry">
        <label for="email">Email</label>
        <input type="text" id="author-email" name="email" class="text" value="" />
        </div>

        <div class="form-entry">
        <label for="pass">Password</label>
        <input type="password" name="pass" class="text w50" /><small id="pass-notes">Leave blank to keep current password.</small>
        </div>

        <div class="form-entry blank">
        <label>&nbsp;</label>
        <a href="javascript:;" id="additional-details"><span>Show</span> Additional Details</a>
        </div>

        <div id="more-details" class="hide">
        <div class="form-entry">
            <label for="notes">Notes <span>A short bio or other details about the author.</span></label>
            <textarea name="notes" cols="50" rows="5" class="h100" id="resize"></textarea>
        </div>

        <div class="form-entry">
            <label for="notify">Notifications <span>Recieve a notification when a post is created.</span></label>
            <select name="notify" id="author-notify" class="text w25">
            <option value="0">None</option>
            <option value="1">Email</option>
            </select>
        </div>

        <div class="form-entry">
            <label for="type">Type <span>Authors cannot add new users. Editors can add authors and edit posts.</span></label>
            <select name="type" id="author-type" class="text w50">
            <option value="1">Author</option>
            <option value="2">Editor</option>
            <?php if ($user->get('type') == 3): ?><option value="3">Administrator</option><?php endif; ?>
            </select>
        </div>
        </div>

        <div class="form-footer">
        <input type="reset" value="Reset" id="author-reset" class="reset" onclick="reset_author();"/> <input type="submit" name="delete-author" value="Delete Author" id="author-delete" class="large red button hide" onclick="return confirm('Are you sure you want to delete this user?');" /> <input type="submit" name="add-author" value="Add Author" id="author-submit" class="large green button" />
        </div>

    </form>
    </fieldset>
<?php endif; ?>

</div>

<?php $t->foot(); ?>