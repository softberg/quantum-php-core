<?php $error = session()->getFlash('error') ?>
<?php if ($error): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (is_array($error)): ?>
                <?php foreach ($error as $field => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                        M.toast({html: "<?php echo addslashes($message); ?>", classes: 'red toast-left'});
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                M.toast({html: "<?php echo addslashes($error); ?>", classes: 'red toast-left'});
            <?php endif; ?>
        });
    </script>
<?php endif; ?>
