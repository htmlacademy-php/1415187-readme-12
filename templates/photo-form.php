<section class="adding-post__photo tabs__content <?php if ($form_type == 'photo'):?>tabs__content--active<?php endif; ?>">
    <h2 class="visually-hidden">Форма добавления фото</h2>
    <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
        <input type="hidden" id="form-type" name="form-type" value="photo">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="photo-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section <?php if (!empty($field_error['heading'])):?>form__input-section--error<?php endif; ?>">
                        <input class="adding-post__input form__input" id="photo-heading" type="text" name="heading" placeholder="Введите заголовок" value=<?= $field_value['heading'] ?>>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Ошибка</h3>
                            <p class="form__error-desc"><?= $field_error['heading'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                    <div class="form__input-section <?php if (!empty($field_error['photo-url'])):?>form__input-section--error<?php endif; ?>">
                        <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-url" placeholder="Введите ссылку" value=<?= $field_value['photo-url'] ?>>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title">Ошибка</h3>
                            <p class="form__error-desc"><?= $field_error['photo-url'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="photo-tags">Теги</label>
                    <div class="form__input-section <?php if (!empty($field_error['tags'])):?>form__input-section--error<?php endif; ?>">
                        <input class="adding-post__input form__input" id="photo-tags" type="text" name="tags" placeholder="Введите теги" value=<?= $field_value['tags'] ?>>
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
        <div class="adding-post__input-file-container form__input-container form__input-container--file">
            <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                    <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file" name="photo-file">
                    <div class="form__file-zone-text">
                        <span>Перетащите фото сюда</span>
                    </div>
                </div>
                <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" type="button">
                    <span>Выбрать фото</span>
                    <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                        <use xlink:href="#icon-attach"></use>
                    </svg>
                </button>
            </div>
            <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">
            </div>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="javascript:history.back()">Закрыть</a>
        </div>
    </form>
</section>