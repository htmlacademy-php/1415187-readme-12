<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= htmlspecialchars($post['heading'] ?? '') ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper">
                <div class="post-details__main-block post post--details">
                    <div class="post-<?= $post['type_class'] ?>">
                        <?= include_template('posts/' . $post['type_class'] . '-details.php', ['post' => $post]) ?>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button"
                               href="like.php?id=<?= $post['id'] ?>" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                     height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post['likes'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button"
                               href="/post.php?id=<?= $post['id'] . '&showall=1' ?>" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post['comments'] ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button"
                               href="repost.php?id=<?= $post['id'] ?>" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span><?= $post['reposts'] ?></span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                            <?php if (isset($post['author_original'])) : ?>
                                <a class="post__indicator" href="post.php?id=<?= $post['original_post'] ?>"
                                   title="Перейти к посту автора">
                                    <span>Пост автора <?= htmlspecialchars($post['author_original'] ?? '') ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                        <span class="post__view"><?= $post['view_count'] ?> просмотров</span>
                    </div>
                    <?php if (!empty($post[0])) :
                        $tags = explode(',', $post[0]); ?>
                        <ul class="post__tags">
                            <?php foreach ($tags as $tag) : ?>
                                <li><a href="search.php?keywords=<?= urlencode('#' . $tag) ?>">#<?= $tag ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="comments">
                        <form class="comments__form form" action="comment.php" method="post">
                            <input type="hidden" name="post-id" value="<?= $post['id'] ?>">
                            <div class="comments__my-avatar">
                                <?php if (isset($user['avatar'])) : ?>
                                    <img class="comments__picture" src="img/<?= $user['avatar'] ?>"
                                         alt="Аватар пользователя">
                                <?php endif; ?>
                            </div>
                            <div
                                class="form__input-section <?= !empty($comment_errors) ? 'form__input-section--error' : '' ?>">
                                <textarea class="comments__textarea form__textarea form__input" name="comment"
                                          placeholder="Ваш комментарий"><?= (!empty($comment_errors)) ? htmlspecialchars($comment_text ?? '') : '' ?></textarea>
                                <label class="visually-hidden">Ваш комментарий</label>
                                <?php if (!empty($comment_errors)) : ?>
                                    <button class="form__error-button button" type="button">!</button>
                                    <div class="form__error-text">
                                        <h3 class="form__error-title">Ошибка валидации</h3>
                                        <p class="form__error-desc"><?= isset($comment_errors[0]) ? $comment_errors['post-id'] : $comment_errors['comment'] ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <?php if (!$show_all) :
                                    $comments = array_slice($comments, 0, 3);
                                endif; ?>
                                <?php foreach ($comments as $comment) : ?>
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link" href="#">
                                                <?php if (isset($comment['avatar'])) : ?>
                                                    <img class="comments__picture" src="img/<?= $comment['avatar']; ?>"
                                                         alt="Аватар пользователя <?= htmlspecialchars($comment['author_name'] ?? '') ?>">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name"
                                                   href="profile.php?id=?<?= $comment['author_id'] ?>">
                                                    <span><?= htmlspecialchars($comment['author_name'] ?? '') ?></span>
                                                </a>
                                                <time class="comments__time" datetime="<?= $comment['dt_add'] ?>">
                                                    <?= time_difference($comment['dt_add'], $now_time) . ' назад' ?>
                                                </time>
                                            </div>
                                            <p class="comments__text">
                                                <?= htmlspecialchars($comment['content'] ?? '') ?>
                                            </p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (($count_comments > 3) && (!$show_all)) : ?>
                                <a class="comments__more-link" href="/post.php?id=<?= $post['id'] . '&showall=1' ?>">
                                    <span>Показать все комментарии</span>
                                    <sup class="comments__amount"><?= $count_comments ?></sup>
                                </a>
                            <?php endif;
                            if (($count_comments > 3) && ($show_all)) : ?>
                                <a class="comments__more-link" href="/post.php?id=<?= $post['id'] . '&showall=' ?>">
                                    <span>Скрыть комментарии</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link"
                               href="profile.php?id=?<?= $post['author_id'] ?>">
                                <?php if (!empty($author['avatar'] ?? null)) : ?>
                                    <img class="post-details__picture user__picture" src="img/<?= $author['avatar'] ?>"
                                         alt="Аватар пользователя">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="profile.php?id=<?= $author['id'] ?>">
                                <span><?= htmlspecialchars($author['username'] ?? '') ?></span>
                            </a>
                            <time class="post-details__time user__time"
                                  datetime="<?= $author['dt_add'] ?? '' ?>"><?= time_difference(
                                      $author['dt_add'],
                                      $now_time
                                  ) . ' на сайте' ?></time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span
                                class="post-details__rating-amount user__rating-amount"><?= $author['followers'] ?></span>
                            <span
                                class="post-details__rating-text user__rating-text"><?= get_noun_plural_form(
                                    $author['followers'],
                                    'подписчик',
                                    'подписчика',
                                    'подписчиков'
                                ); ?></span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount"><?= $author['posts'] ?></span>
                            <span
                                class="post-details__rating-text user__rating-text"><?= get_noun_plural_form(
                                    $author['posts'],
                                    'публикация',
                                    'публикации',
                                    'публикаций'
                                ); ?></span>
                        </p>
                    </div>
                    <?php if ($user['id'] !== $author['id']) : ?>
                        <div class="post-details__user-buttons user__buttons">
                            <a class="user__button user__button--subscription button button--main"
                               href="subscribe.php?id=<?= $author['id'] ?>"><?= $user['subscribed'] ? 'Отписаться' : 'Подписаться' ?></a>
                            <a class="user__button user__button--writing button button--green" href="messages.php">Сообщение</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>
