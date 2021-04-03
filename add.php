<?php
require_once __DIR__ . '/libs/base.php';

$title = $site_name . ': Добавление публикации';

$validation_rules = [
    'text' => [
        'heading' => 'filled|length:10,50',
        'content' => 'filled|length:50,500',
    ],
    'photo' => [
        'heading' => 'filled|length:10,50',
        'photo-url' => 'filled|correct_url|image_url_content',
        'photo-file' => 'img_loaded',
    ],
    'link' => [
        'heading' => 'filled|length:10,50',
        'link-url' => 'filled|correct_url',
    ],
    'quote' => [
        'heading' => 'filled|length:10,50',
        'content' => 'filled',
        'quote-author' => 'filled',
    ],
    'video' => [
        'heading' => 'filled|length:10,50',
        'video-url' => 'filled|correct_url|youtube_url',
    ],
];

$field_error_codes = [
    'heading' => 'Заголовок',
    'content' => 'Текст поста',
    'link-url' => 'Ссылка',
    'photo-url' => 'Ссылка на изображение',
    'video-url' => 'Ссылка YouTube',
    'photo-file' => 'Файл фото',
    'quote-author' => 'Автор',
];

$img_folder = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;

$user = get_user();
if ($user === null) {
    header("Location: index.php");
    exit();
}

$content_types = get_content_types($connection);

$form = [
    'values' => [],
    'errors' => [],
];

$form_type = $_GET['tab'] ?? 'photo';

$post_types = array_column($content_types, 'id', 'type_class');

if (count($_POST) > 0 && isset($_POST['form-type'])) {
    $form_type = $_POST['form-type'];
    $form['values'] = $_POST;
    $form['values']['photo-file'] = $_FILES['photo-file'];
    $form['errors'] = validate($form['values'], $validation_rules[$_POST['form-type']], $connection);

    if ((empty($form['errors']['photo-file'])) && (!empty($form['errors']['photo-url']))) {
        $form = ignore_field($form, 'photo-url');
    } elseif ((!empty($form['errors']['photo-file'])) && (empty($form['errors']['photo-url']))) {
        $form = ignore_field($form, 'photo-file');
    }
    $form['errors'] = array_filter($form['errors']);
    if (empty($form['errors'])) {
        $file_url = ($form_type == 'photo') ? upload_file($form, $img_folder) : null;
        $post_id = save_post($connection, $form['values'], $post_types, $user, $file_url);
        add_tags($_POST['tags'], $post_id, $connection);

        $URL = '/post.php?id=' . $post_id;
        header("Location: $URL");
    }
}

$page_content = include_template(
    'add-template.php',
    [
        'content_types' => $content_types,
        'form_values' => $form['values'] ?? [],
        'form_errors' => $form['errors'] ?? [],
        'field_error_codes' => $field_error_codes,
        'form_type' => $form_type,
    ]
);

$layout_content = include_template(
    'layout.php',
    [
        'content' => $page_content,
        'user' => $user,
        'title' => $title,
    ]
);

print($layout_content);