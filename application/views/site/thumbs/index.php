<?php

?>

<h1>STATIC</h1>
<?php foreach($games as $g): ?>
<img src="/games/agt/sqthumb/<?php echo $g['name']; ?>.png?t=<?php echo time();?>" width="250" height="250" alt="<?php echo $g['visible_name'];?>" />
<?php endforeach; ?>
<hr/>
<h1>ANIMATION</h1>
<?php foreach($games as $g): ?>
<img src="/games/agt/sqthumb/<?php echo $g['name']; ?>.webp?t=<?php echo time();?>" width="250" height="250" alt="<?php echo $g['visible_name'];?>" />
<?php endforeach; ?>
