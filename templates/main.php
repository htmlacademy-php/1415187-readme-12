    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link sorting__link--active" href="#">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link" href="#">
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
                        <a class="filters__button filters__button--ellipse filters__button--all <?php if ($post_type == ''):?>filters__button--active <?php endif; ?>" href="index.php?">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach($content_types as $content_type): ?>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--<?=$content_type['type_class']?> button <?php if ($post_type == $content_type['id']):?>filters__button--active <?php endif; ?>" href="index.php?post_type=<?=$content_type['id']?>">
                            <span class="visually-hidden"><?=$content_type['type_name']?></span>
                            <?php $size_ico = filter_size_ico($content_type['type_class']);?>
                            <svg class="filters__icon" width="<?=$size_ico['w']?>" height="<?=$size_ico['h']?>">
                                <use xlink:href="#icon-filter-<?=$content_type['type_class']?>"></use>
                            </svg>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <?php foreach($popular_posts as $post_index => $post): ?>
                <article class="popular__post post post-<?=$post['type_class']?>">
                    <header class="post__header">
                        <h2><a href="post.php?id=<?=$post['id']?>" title="Открыть страницу поста <?=$post['heading'];?>"><?=$post['heading'];?></a></h2>
                    </header>
                    <div class="post__main">
                    <?php $content = include_template($post['type_class'] . '-post.php', ['post' => $post]);
                        print($content); ?>
                    </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Профиль <?=$post['username']?>">
                            <div class="post__avatar-wrapper">
                                <img class="post__author-avatar" src="img/<?=$post['avatar']?>" width="40" height="40" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?=htmlspecialchars($post['username'])?></b>
                                <?php $post_time = new DateTime(generate_random_date($post_index)) ?>
                                <time class="post__time" datetime="<?=$post_time->format('Y-m-d H:i:s')?>" title="<?=$post_time->format('d.m.Y H:i')?>"><?=time_difference($post_time, $now_time)?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?=$post['view_count']?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
            <?php endforeach; ?>
        </div>
    </div>