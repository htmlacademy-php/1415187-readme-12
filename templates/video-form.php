<section class="adding-post__video tabs__content <?php if ($form_type == 'video'):?>tabs__content--active<?php endif; ?>">
                    <h2 class="visually-hidden">Форма добавления видео</h2>
                    <form class="adding-post__form form" action="#" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="form-type" name="form-type" value="video">
                        <div class="form__text-inputs-wrapper">
                        <div class="form__text-inputs">
                            <div class="adding-post__input-wrapper form__input-wrapper">
                                <label class="adding-post__label form__label" for="video-heading">Заголовок <span class="form__input-required">*</span></label>
                                <div class="form__input-section <?php if (!empty($field_error['heading'])):?>form__input-section--error<?php endif; ?>">
                                <input class="adding-post__input form__input" id="video-heading" type="text" name="heading" placeholder="Введите заголовок" value=<?= $field_value['heading'] ?>>
                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Ошибка</h3>
                                    <p class="form__error-desc"><?= $field_error['heading'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="adding-post__input-wrapper form__input-wrapper">
                            <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                            <div class="form__input-section <?php if (!empty($field_error['video-url'])):?>form__input-section--error<?php endif; ?>">
                            <input class="adding-post__input form__input" id="video-url" type="text" name="video-url" placeholder="Введите ссылку" value=<?= $field_value['video-url'] ?>>
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка</h3>
                                <p class="form__error-desc"><?= $field_error['video-url'] ?></p>
                            </div>
                            </div>
                        </div>
                        <div class="adding-post__input-wrapper form__input-wrapper">
                            <label class="adding-post__label form__label" for="video-tags">Теги</label>
                            <div class="form__input-section <?php if (!empty($field_error['tags'])):?>form__input-section--error<?php endif; ?>">
                            <input class="adding-post__input form__input" id="video-tags" type="text" name="tags" placeholder="Введите теги" value=<?= $field_value['tags'] ?>>
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка</h3>
                                <p class="form__error-desc"><?= $field_error['tags'] ?></p>
                            </div>
                            </div>
                        </div>
                        </div>
                        <?php if (!empty($field_error)):?>
                            <div class="form__invalid-block">
                                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                <ul class="form__invalid-list">
                                    <?php foreach($field_error as $field => $error): ?>
                                        <li class="form__invalid-item"><?= $field_error_codes[$field] ?> . <?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="adding-post__buttons">
                        <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                        <a class="adding-post__close" href="javascript:history.back()">Закрыть</a>
                    </div>
                    </form>
                </section>