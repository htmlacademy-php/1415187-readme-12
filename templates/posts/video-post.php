<div class="post-video__block">
    <div class="post-video__preview">
        <?php if (in_array(explode('?', $_SERVER['REQUEST_URI'])[0], ['/feed.php', '/profile.php'])) : ?>
            <?= !empty($post['youtube_url'] ?? null) ? embed_youtube_cover(htmlspecialchars($post['youtube_url']), 758,
                380) : '' ?>
        <?php else : ?>
            <?= !empty($post['youtube_url'] ?? null) ? embed_youtube_cover(htmlspecialchars($post['youtube_url']), 358,
                120) : '' ?>
        <?php endif; ?>
    </div>
    <a href="post.php?id=<?= $post['id'] ?>" class="post-video__play-big button">
        <svg class="post-video__play-big-icon" width="14" height="14">
            <use xlink:href="#icon-video-play-big"></use>
        </svg>
        <span class="visually-hidden">Запустить проигрыватель</span>
    </a>
</div>
