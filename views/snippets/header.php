<!DOCTYPE html>
<html lang="en">
<head>
<?php if(isset($metatags)) { foreach ($metatags as $property => $content) { ?>
<meta <?= substr($property, 0, 2) == "og" ? "property" : "name" ?>="<?= $property ?>" content="<?= $content ?>">
<?php } } ?>	
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if(isset($cssfiles)) { foreach ($cssfiles as $url) { ?>
<link rel="stylesheet" href="<?= $url ?>">
<?php }}  if(isset($jsfiles)) { foreach ($jsfiles as $url) { ?>
<script type="text/javascript" src="<?= $url ?>"></script>
<?php }} ?>
<title><?=$config["title"] ?? "Untitled Page" ?> - <?=$_ENV["APP_NAME"]?></title>
</head>
<body>
    