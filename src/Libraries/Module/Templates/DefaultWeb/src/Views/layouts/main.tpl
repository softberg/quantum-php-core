<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title ?></title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="shortcut icon" href="<?php echo asset()->url('shared/images/favicon.ico') ?>">
        
        <?php assets("css") ?>
    </head>
    <body>

        <main><?php echo view() ?></main>

        <?php echo debugbar() ?>

        <?php assets("js") ?>
    </body>
</html>