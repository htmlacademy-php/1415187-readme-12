<div class="post-video__block">
    <div class="post-video__preview">
        <?= !empty($post['youtube_url'] ?? null) ? embed_youtube_video($post['youtube_url'], 758, 380) : '' ?>
    </div>
</div>
