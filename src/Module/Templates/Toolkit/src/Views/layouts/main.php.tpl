<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo asset()->url('images/favicon.ico') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/10.1.2/jsoneditor.min.css"
          rel="stylesheet"
          type="text/css">

    <?php assets("css") ?>
</head>
<body class="has-fixed-sidenav">

<hearder>
    <nav class="navbar-fixed teal accent-4">
        <span class="navbar-logo"><?php echo $title ?></span>
        <a href="#" data-target="sidenav-left" class="sidenav-trigger left">
            <i class="material-icons black-text">menu</i>
        </a>
    </nav>

    <?php echo partial('partials/navbar') ?>

</hearder>

<main>
    <div id="toolkit-content">
        <?php echo view() ?>
    </div>
</main>

<?php echo debugbar() ?>

<?php assets("js") ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/10.1.2/jsoneditor.min.js"></script>
</body>
</html>