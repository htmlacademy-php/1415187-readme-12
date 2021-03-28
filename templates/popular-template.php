<main class="page__main page__main--popular">
  <div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
  </div>
  <div class="popular container">
    <div class="popular__filters-wrapper">
      <div class="popular__sorting sorting">
        <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
        <ul class="popular__sorting-list sorting__list">
          <li class="sorting__item sorting__item--popular">
            <a class="sorting__link <?= ($sort == 'view_count') ? 'sorting__link--active' : '' ?> <?= $reverse ? 'sorting__link--reverse' : '' ?>"
              href="popular.php?sort=view_count<?= '&reverse=' . $reverse ?><?= $filter ? '&filter=' . $filter : '' ?>">
              <span>Популярность</span>
              <svg class="sorting__icon" width="10" height="12">
                <use xlink:href="#icon-sort"></use>
              </svg>
            </a>
          </li>
          <li class="sorting__item">
            <a class="sorting__link <?= ($sort == 'likes') ? 'sorting__link--active' : '' ?> <?= $reverse ? 'sorting__link--reverse' : '' ?>"
               href="popular.php?sort=likes<?= '&reverse=' . $reverse ?><?= $filter ? '&filter=' . $filter : '' ?>">
              <span>Лайки</span>
              <svg class="sorting__icon" width="10" height="12">
                <use xlink:href="#icon-sort"></use>
              </svg>
            </a>
          </li>
          <li class="sorting__item">
            <a class="sorting__link <?= ($sort == 'dt_add') ? 'sorting__link--active' : '' ?> <?= $reverse ? 'sorting__link--reverse' : '' ?>"
               href="popular.php?sort=dt_add<?= '&reverse=' . $reverse ?><?= $filter ? '&filter=' . $filter : '' ?>">
              <span>Дата</span>
              <svg class="sorting__icon" width="10" height="12">
                <use xlink:href="#icon-sort"></use>
              </svg>
            </a>
          </li>
        </ul>
      </div>
      <div class="popular__filters filters">
        <b class="popular__filters-caption filters__caption">Тип контента:</b>
        <ul class="popular__filters-list filters__list">
          <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
            <a class="filters__button filters__button--ellipse filters__button--all <?= ($filter == '') ? 'filters__button--active' : '' ?>" href="popular.php?">
            <span>Все</span>
            </a>
          </li>
          <?php foreach ($content_types as $content_type) : ?>
          <li class="popular__filters-item filters__item">
            <a class="button filters__button
              <?= $content_type['type_class'] ? 'filters__button--' . $content_type['type_class'] : '' ?>
              <?= ($filter == $content_type['type_class']) ? 'filters__button--active' : '' ?>"
              href="popular.php?filter=<?=$content_type['type_class'] ?>">
              <span class="visually-hidden"><?= $content_type['type_name'] ?></span>
              <?php $size_ico = filter_size_ico($content_type['type_class']);?>
              <svg class="filters__icon" width="<?= $size_ico['w'] ?>" height="<?= $size_ico['h'] ?>">
                <use xlink:href="#icon-filter-<?= $content_type['type_class'] ?>"></use>
              </svg>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php if (isset($posts)) : ?>
    <div class="popular__posts">
      <?php foreach($posts as $post) : ?>
      <article class="popular__post post post-<?= $post['type_class'] ?>">
        <header class="post__header">
          <h2><a href="post.php?id=<?=$post['id']?>" title="Открыть страницу поста <?= htmlspecialchars($post['heading']) ?>"><?= htmlspecialchars($post['heading']) ?></a></h2>
        </header>
        <div class="post__main">
          <?= include_template('posts/' . $post['type_class'] . '-post.php', ['post' => $post])?>
        </div>
        <footer class="post__footer">
          <div class="post__author">
            <a class="post__author-link" href="profile.php?id=<?= $post['author_id'] ?>" title="Профиль <?= htmlspecialchars($post['username']) ?>">
              <div class="post__avatar-wrapper">
                <img class="post__author-avatar" src="img/<?= $post['avatar'] ?>" width="40" height="40" alt="Аватар пользователя">
              </div>
              <div class="post__info">
                <b class="post__author-name"><?= htmlspecialchars($post['username']) ?></b>
                <time class="post__time" datetime="<?= $post['dt_add'] ?? '' ?>" title="<?= date_create_from_format('Y-m-d H:i:s', $post['dt_add'])->format('d.m.Y H:i') ?>"><?= time_difference($post['dt_add'], $now_time) . ' назад' ?></time>
              </div>
            </a>
          </div>
          <div class="post__indicators">
            <div class="post__buttons">
              <a class="post__indicator post__indicator--likes button" href="like.php?id=<?= $post['id'] ?>" title="Лайк">
                <svg class="post__indicator-icon" width="20" height="17">
                  <use xlink:href="#icon-heart"></use>
                </svg>
                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                  <use xlink:href="#icon-heart-active"></use>
                </svg>
                <span><?= $post['likes'] ?? '' ?></span>
                <span class="visually-hidden">количество лайков</span>
              </a>
              <a class="post__indicator post__indicator--comments button" href="comment.php?id=<?= $post['id'] ?>" title="Комментарии">
                <svg class="post__indicator-icon" width="19" height="17">
                  <use xlink:href="#icon-comment"></use>
                </svg>
                <span><?= $post['comments'] ?? '' ?></span>
                <span class="visually-hidden">количество комментариев</span>
              </a>
            </div>
          </div>
        </footer>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if ($total_posts > $page_limit) : ?>
    <div class="popular__page-links">
      <?php if ($page_number > 1) : ?>
      <a class="popular__page-link popular__page-link--prev button button--gray"
        href="popular.php?page=<?= $page_number - 1 ?><?= $filter ? '&filter=' . $filter : '' ?><?= $sort ? '&sort=' . $sort : '' ?>">
      Предыдущая страница
      </a>
      <?php endif; ?>
      <?php if ($page_number < ($total_posts / $page_limit)) : ?>
      <a class="popular__page-link popular__page-link--next button button--gray"
        href="popular.php?page=<?= $page_number + 1 ?><?= $filter ? '&filter=' . $filter : '' ?><?= $sort ? '&sort=' . $sort : '' ?>">
      Следующая страница
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</main>