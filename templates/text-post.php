<p><?=htmlspecialchars(cut_text($post['content']))?></p>
<?php if (mb_strlen(htmlspecialchars($post['content'])) > 300): ?>
<a class="post-text__more-link" href="#">Читать далее</a>
<?php endif; ?>