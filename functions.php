<?php

function cut_text (string $text, int $length = 300) {
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length + 1);
        $end = mb_strlen(strrchr($text, ' '));
        $text = mb_substr($text, 0, -$end) . '...';
        }
    return $text;
}

function time_difference ($post_time, $current_time) {
    date_default_timezone_set('Europe/Moscow');
    
    $diff = date_diff($current_time, $post_time);

    if ($diff->y > 0) {
        $relative_time = $diff->y . ' ' .
            get_noun_plural_form($diff->y, 'год', 'года', 'лет') . ' назад';
    } elseif ($diff->m > 0) {
        $relative_time = $diff->m . ' ' .
            get_noun_plural_form($diff->m, 'месяц', 'месяца', 'месяцев') . ' назад';
    } elseif ($diff->d > 6) {
        $relative_time = floor(($diff->d)/7) . ' ' .
            get_noun_plural_form(floor(($diff->d)/7),' неделю', ' недели', ' недель') . ' назад';
    } elseif ($diff->d > 0) {
        $relative_time = $diff->d . ' ' .
            get_noun_plural_form($diff->d, 'день', 'дня', 'дней') . ' назад';
    } elseif ($diff->h > 0) {
        $relative_time = $diff->h . ' ' .
            get_noun_plural_form($diff->h, 'час', 'часа', 'часов') . ' назад';
    } elseif ($diff->i > 0) {
        $relative_time = $diff->i . ' ' .
            get_noun_plural_form($diff->i, 'минуту', 'минуты', 'минут') . ' назад';
    } elseif ($diff->s >= 0) {
        $relative_time = 'Только что';
    } else {
        $relative_time = '';
    }
    return $relative_time;
}