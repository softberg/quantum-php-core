<div class="row valign-wrapper comment-item">
    <div class="col s12">
        <div class="card-panel comment-box left-align">
            <div class="comment-box-header">
                <?php if (!empty($comment['author']['image'])): ?>
                    <img
                            src="<?php echo base_url() . '/uploads/' . $comment['author']['image'] ?>"
                            alt="<?php echo htmlspecialchars($comment['author']['firstname']) ?>"
                            class="circle responsive-img">
                <?php else: ?>
                    <i class="material-icons large left grey-text text-darken-2">person</i>
                <?php endif; ?>
                <span class="black-text">
                    <strong><?php echo $comment['author']['firstname'] . ' ' . $comment['author']['lastname'] ?></strong>
                </span>
            </div>
            <p class="grey-text text-darken-2">
                <?php echo nl2br($comment['content']) ?>
            </p>
            <p class="grey-text text-darken-1 comment-date-time">
                <i class="material-icons tiny">access_time</i>
                <small><?php echo $comment['date'] ?></small>
            </p>
        </div>
    </div>
</div>