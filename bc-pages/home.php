<?php

$t->add_script("js","jquery");
$t->add_script("js","global");

?>

<?php $t->head(); ?>

<?php $posts = $controller->_posts; ?>

<div id="home">

<?php if ( empty($posts) ): ?>
    <?php get_message("You currently have no posts. To create one, simply click the <a href=\"write\">'Write Post'</a> button to the top right corner","info"); ?>
<?php endif; ?>
<?php foreach ($posts as $post): ?>
    <div class="post">
        <h2><a href="<?php echo BRIGGLE_DIR; ?>view/<?php echo $post['ID']; ?>"><?php echo stripslashes($post['title']); ?></a></h2>
        <div class="post-meta">
            By
            <span><?php echo ( $post['u_ID'] == $user->get('ID') ? 'You' : $post['author'] ); ?></span> |
            <span><?php echo relative($post['date']); ?></span> |
            <?php echo '<a href="'.BRIGGLE_DIR.'view/'.$post['ID'].'#comments">'.($post['comments'] .' Comment'.( ($post['comments'] == 1) ? '' : 's')); ?></a>
            <?php echo ( $user->get('type') > 1 || $post['u_ID'] == $user->get('ID') ? '| <a href="'.BRIGGLE_DIR.'edit/'.$post['ID'].'">Edit Post</a>' : ''); ?>
        </div>
        <div class="post-body">

            <?php if ($post['content'] == ''): ?>
                <?php if ( count($post['uploads']) > 0 ): ?>
                     <div class="post-uploads">
                     <?php foreach ( $post['uploads'] as $upload): ?>
                        <img src="<?php echo $upload['directory']; ?>" alt="<?php echo $upload['name']; ?>" title="<?php echo $upload['name']; ?>" class="post-image" />
                     <?php endforeach; ?>
                     </div>
                <?php endif; ?>
            <?php else: ?>

                <?php if ($post['uploads'] > 0):  ?>
                <a href="<?php echo BRIGGLE_DIR; ?>view/<?php echo $post['ID']; ?>" class="post-image-link"><img src="<?php echo $post['uploads']['0']['directory']; ?>" alt="<?php echo $post['uploads']['0']['name']; ?>" title="<?php echo $post['uploads']['0']['name']; ?>" /><?php echo (count($post['uploads']) > 1 ? '<br />'.(count($post['uploads'])-1) .' more image'.((count($post['uploads'])-1) == 1 ? '' : 's') : "" ); ?></a>
                <?php endif; ?>
                <?php echo format_post($post['content']); ?>

            <?php endif; ?>

        </div>
    </div>
<?php endforeach; ?>
</div>

<?php if ( !empty($posts) ): ?>
    <?php get_pages( 'posts' , (isset( $uri['2']) ? $uri['2'] : 1 ) ); ?>
<?php endif; ?>

<?php $t->foot(); ?>