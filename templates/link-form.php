<section class="adding-post__link tabs__content <?php if ($form_type == 'link'):?>tabs__content--active<?php endif; ?>">
    <h2 class="visually-hidden">Форма добавления ссылки</h2>
    <form class="adding-post__form form" action="#" method="post">
        <input type="hidden" id="form-type" name="form-type" value="link">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
            <div class="adding-post__input-wrapper form__input-wrapper">
                <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
                <div class="form__input-section <?php if ($field_error['heading'] != ''):?>form__input-section--error<?php endif; ?>">
                    <input class="adding-post__input form__input" id="link-heading" type="text" name="heading" placeholder="Введите заголовок" value=<?= $field_value['heading'] ?>>
                    <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                    <div class="form__error-text">
                        <h3 class="form__error-title">Ошибка</h3>
                        <p class="form__error-desc"><?= $field_error['heading'] ?></p>
                    </div>
                </div>
            </div>
            <div class="adding-post__textarea-wrapper form__input-wrapper">
                <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                <div class="form__input-section <?php if (!empty($field_error['link-url'])):?>form__input-section--error<?php endif; ?>">
                <input class="adding-post__input form__input" id="post-link" type="text" name="link-url" placeholder="Введите ссылку" value=<?= $field_value['link-url'] ?>>
                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                <div class="form__error-text">
                    <h3 class="form__error-title">Ошибка</h3>
                    <p class="form__error-desc"><?= $field_error['link-url'] ?></p>
                </div>
                </div>
            </div>
            <div class="adding-post__input-wrapper form__input-wrapper">
                <label class="adding-post__label form__label" for="link-tags">Теги</label>
                <div class="form__input-section <?php if (!empty($field_error['tags'])):?>form__input-section--error<?php endif; ?>">
                <input class="adding-post__input form__input" id="link-tags" type="text" name="tags" placeholder="Введите теги" value=<?= $field_value['tags'] ?>>
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
                            <li class="form__invalid-item"><?= $field_error_codes[$field] ?>. <?= $error ?></li>
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