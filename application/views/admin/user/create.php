<?php if(count($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <?php echo $error; ?>
    <?php endforeach; ?>
<?php else: ?>
    <style>
        table, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 7px;
        }
    </style>
    <table>
        <tr>
            <td><?php echo __('Логин') ?></td>
            <td><?php echo $login ?></td>
        </tr>
        <tr>
            <td><?php echo __('Пароль') ?></td>
            <td><?php echo $password ?></td>
        </tr>
    </table>
    <?php if($print): ?>
        <script>
            window.onload = function () {
                this.print();
            }
        </script>
    <?php endif; ?>
<?php endif; ?>