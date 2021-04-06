<main class="page__main page__main--search-results">
   <h1 class="visually-hidden">Страница результатов поиска</h1>
   <section class="search">
      <h2 class="visually-hidden">Результаты поиска</h2>
      <div class="search__query-wrapper">
         <div class="search__query container">
            <span>Вы искали:</span>
            <span class="search__query-text"><?= $keywords ? htmlspecialchars($keywords) : '' ?></span>
         </div>
      </div>
      <div class="search__results-wrapper">
         <?php if (count($posts) == 0) : ?>
         <div class="search__no-results container">
            <p class="search__no-results-info">К сожалению, ничего не найдено.</p>
            <p class="search__no-results-desc">
               Попробуйте изменить поисковый запрос или просто зайти в раздел &laquo;Популярное&raquo;, там живет самый крутой контент.
            </p>
            <div class="search__links">
               <a class="search__popular-link button button--main" href="popular.php">Популярное</a>
               <a class="search__back-link" href="javascript:history.back()">Вернуться назад</a>
            </div>
         </div>
         <?php else: ?>
         <div class="container">
            <div class="search__content">
               <?php foreach ($posts as $post) : ?>
               <article class="search__post post <?= $post['type_class'] ? 'post-' . $post['type_class'] : '' ?>">
                  <header class="post__header post__author">
                     <a class="post__author-link" href="profile.php?id=<?= $post['author_id'] ?? '' ?>" title="Автор">
                        <div class="post__avatar-wrapper">
                           <?php if (isset($post['avatar'])) : ?>
                           <img class="post__author-avatar" src="img/<?= $post['avatar'] ?>"
                              alt="Аватар пользователя" width="60" height="60">
                           <?php endif; ?>
                        </div>
                        <div class="post__info">
                           <b class="post__author-name"><?= ($post['username']) ? htmlspecialchars($post['username']) : '' ?></b>
                           <?php if (isset($post['dt_add'])) : ?>
                           <time class="post__time"
                              datetime="<?= $post['dt_add'] ?? '' ?>"><?= time_difference($post['dt_add'], $now_time) . ' назад' ?></time>
                           <?php endif; ?>
                        </div>
                     </a>
                  </header>
                  <h2>
                     <a href="post.php?id=<?= $post['id'] ?>"><?= $post['heading'] ? htmlspecialchars($post['heading']) : '' ?></a>
                  </h2>
                  <div class="post__main">
                     <?=include_template('posts/' . $post['type_class'] . '-post.php', ['post' => $post]) ?>
                  </div>
                  <footer class="post__footer post__indicators">
                     <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" href="like.php?id=<?= $post['id'] ?? '' ?>"
                           title="Лайк">
                           <svg class="post__indicator-icon" width="20" height="17">
                              <use xlink:href="#icon-heart"></use>
                           </svg>
                           <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                              <use xlink:href="#icon-heart-active"></use>
                           </svg>
                           <span><?= $post['likes'] ?? '' ?></span>
                           <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--comments button" href="post.php?id=<?= $post['id'] ?? '' ?>"
                           title="Комментарии">
                           <svg class="post__indicator-icon" width="19" height="17">
                              <use xlink:href="#icon-comment"></use>
                           </svg>
                           <span><?= $post['comments'] ?? '' ?></span>
                           <span class="visually-hidden">количество комментариев</span>
                        </a>
                     </div>
                  </footer>
               </article>
               <?php endforeach; ?>
            </div>
         </div>
         <?php endif; ?>
      </div>
   </section>
</main>
