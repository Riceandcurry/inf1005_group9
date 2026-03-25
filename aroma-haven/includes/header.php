<?php
$pageTitle = $pageTitle ?? 'Aroma Haven';
$bodyClass = $bodyClass ?? 'bg-oat';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <?php
  $tokensVersion = @filemtime(__DIR__ . '/../public/css/tokens.css') ?: time();
  $baseVersion = @filemtime(__DIR__ . '/../public/css/base.css') ?: time();
  $pagesVersion = @filemtime(__DIR__ . '/../public/css/pages.css') ?: time();
  $componentsVersion = @filemtime(__DIR__ . '/../public/css/components.css') ?: time();
  ?>
  <link rel="stylesheet" href="css/tokens.css?v=<?php echo $tokensVersion; ?>">
  <link rel="stylesheet" href="css/base.css?v=<?php echo $baseVersion; ?>">
  <link rel="stylesheet" href="css/components.css?v=<?php echo $componentsVersion; ?>">
  <link rel="stylesheet" href="css/pages.css?v=<?php echo $pagesVersion; ?>">
</head>
<body class="<?php echo htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8'); ?>">
