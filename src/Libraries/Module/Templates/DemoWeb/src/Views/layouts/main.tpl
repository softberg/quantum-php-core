<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title ?></title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="shortcut icon" href="<?php echo asset()->url('images/favicon.ico') ?>">
        <?php assets("css") ?>
    </head>
    <body>
        <header><?php echo partial('partials/navbar') ?></header>
        
        <main><?php echo view() ?></main>
        
        <footer class="page-footer"><?php echo partial('partials/footer') ?></footer>
        
        <?php echo debugbar() ?>
        
        <?php assets("js") ?>
    </body>
</html>