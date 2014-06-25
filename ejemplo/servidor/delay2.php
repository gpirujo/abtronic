<?php

sleep((int) @$_GET['seconds']);

$content_types = array(
    'js' => 'text/javascript',
    'css' => 'text/css',
    'jpg' => 'image/jpeg',
);
$file_extension = pathinfo($_GET['file'], PATHINFO_EXTENSION);

header('Content-Type: ' . $content_types[$file_extension]);
header('Cache-Control: no-store');

readfile($_GET['file']);

?>
