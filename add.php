<?php

require_once('helpers.php');
require_once('functions.php');
require_once('db.php');

$validation_rules = [
    'text' => [
        'heading' => 'filled|length_heading',
        'content' => 'filled|length_content'
    ],
    'photo' => [
        'heading' => 'filled|length_heading',
        'photo-url' => 'filled|correct_url|image_url_content',
        'photo-file' => 'img_loaded'
    ],
    'link' => [
        'heading' => 'filled|length_heading',
        'link-url' => 'filled|correct_url'
    ],
    'quote' => [
        'heading' => 'filled|length_heading',
        'content' => 'filled',
        'quote-author' => 'filled'
    ],
    'video' => [
        'heading' => 'filled|length_heading',
        'video-url' => 'filled|correct_url|youtube_url'
    ],
];

$field_error_codes = [
    'heading' => 'Заголовок',
    'content' => 'Текст поста',
    'link-url' => 'Ссылка',
    'photo-url' => 'Ссылка на изображение',
    'video-url' => 'Ссылка YouTube',
    'photo-file' => 'Файл фото',
    'quote-author' => 'Автор'
];

$form_type = 'photo';
$select_content_types_query = 'SELECT * FROM content_types';
$content_types_mysqli = mysqli_query($con, $select_content_types_query);
$content_types = mysqli_fetch_all($content_types_mysqli, MYSQLI_ASSOC);
$post_types = array_column($content_types, 'id', 'type_class');

if ((count($_POST) > 0) && isset($_POST['form-type'])){
    $form_type = $_POST['form-type'];

    foreach ($_POST as $field_name => $field_value) {
        $form['values'][$field_name] = $field_value;
    }
    
    $form['values']['photo-file'] = $_FILES['photo-file'];
    $form['errors'] = validate($form['values'], $validation_rules[$form_type], $con);

    if (empty($form['errors']['photo-file'])) {
        unset($form['errors']['photo-url']);
        unset($form['values']['photo-url']);
    }
    elseif (empty($form['errors']['photo-url'])) {
        unset($form['errors']['photo-file']);
        unset($form['values']['photo-file']);
    }

    $form['errors'] = array_filter($form['errors']);

    if (empty($form['errors'])) {
        switch ($form_type) {
            case 'quote':
                form_add_post_quote($con, $_POST['heading'], $post_types[$form_type], $_POST['content'], $_POST['quote-author']);
                break;
            case 'text':
                form_add_post_text($con, $_POST['heading'], $post_types[$form_type], $_POST['content']);
                break;
            case 'link':
                var_dump($form['values']);
                form_add_post_link($con, $_POST['heading'], $post_types[$form_type], $_POST['link-url']);
                break;
            case 'video':
                form_add_post_video($con, $_POST['heading'], $post_types[$form_type], $_POST['content'], $_POST['youtube_url']);
                break;
            case 'photo':
                form_add_post_photo($con, $_POST['heading'], $post_types[$form_type], $_POST['content'], $_FILES['photo-file']);
        }

        $post_id = mysqli_insert_id($con);

        if (!empty($_POST['tags'])) {
            form_add_post_tags($con, $post_id, array_unique(explode(' ', $_POST['tags'])));
        }

        $URL = '/post.php?id=' . $post_id;
        header("Location: $URL");
    }
}

$page_content = include_template('adding-post.php', [
                                                    'content_types' => $content_types,
                                                    'form_values' => $form['values'],
                                                    'form_errors' => $form['errors'],
                                                    'field_error_codes' => $field_error_codes,
                                                    'form_type' => $form_type
                                                    ]);

print($page_content);