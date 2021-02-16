<div class="popular">
    <div class="post-link__wrapper">
        <a class="post-link__external" href="http://<?=htmlspecialchars($post['content'])?>" title="Перейти по ссылке">
            <div class="post-link__info-wrapper">
                <div class="post-link__icon-wrapper">
                    <img src="https://www.google.com/s2/favicons?domain=<?=htmlspecialchars($post['content'])?>" alt="Иконка">
                </div>
                <div class="post-link__info">
                    <h3><?=htmlspecialchars($post['heading'])?></h3>
                </div>
            </div>
            <span><?=htmlspecialchars($post['content'])?></span>
        </a>
    </div>
</div>