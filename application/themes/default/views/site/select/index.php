<?php foreach($games as $brand=>$game): ?>
    <?php foreach($game as $name=>$g): ?>
    <?php $games_cnf = Kohana::$config->load('games'); ?>
    <?php if(!isset($games_cnf[$name])): ?>
    <?php echo $g['visible_name']; ?>
    <?php else: ?>
    <a href="/games/<?php echo $brand; ?>/<?php echo $name; ?>"><?php echo $g['visible_name']; ?></a>
    <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>
