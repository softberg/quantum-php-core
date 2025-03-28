<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo asset()->url('images/favicon.ico') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/10.1.2/jsoneditor.min.css" rel="stylesheet" type="text/css">

    <title>Logs</title>

    <?php assets("css") ?>
</head>
<body>

    <main>
        <?php echo view() ?>
    </main>


<?php assets("js") ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/10.1.2/jsoneditor.min.js"></script>
</body>

</html>