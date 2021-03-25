<div class="post-video__block">
  <div class="post-video__preview">
    <?= $post['youtube_url'] ? embed_youtube_video($post['youtube_url']) : '' ?>
    <img src="img/coast-medium.jpg" alt="Превью к видео" width="360" height="188">
  </div>
  <a href="post-details.php?id=?<?= $post['id'] ?>" class="post-video__play-big button">
    <svg class="post-video__play-big-icon" width="14" height="14">
      <use xlink:href="#icon-video-play-big"></use>
    </svg>
    <span class="visually-hidden">Запустить проигрыватель</span>
  </a>
</div>