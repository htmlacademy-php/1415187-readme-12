<div class="post-video__block">
  <div class="post-video__preview">
    <?= $post['youtube_url'] ? embed_youtube_video($post['youtube_url']) : '' ?>
  </div>
</div>