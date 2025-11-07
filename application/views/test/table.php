<span style="font-size: 20pt">Bet cost:	<?php echo $bet ?></span><br>
<span style="font-size: 20pt">Lines:	<?php echo $lines ?></span>
<br>
 <table>
            <tr>

                <td style="width: 300px;font-size: 20pt">
                    Combination
                </td>
                <td style="font-size: 20pt">
                    Fixed Odds
                </td>
            </tr>
        </table>    

<?php foreach($c['pay'] as $sym=>$pay): ?>
    <?php foreach ($pay as $count=>$win): ?>
            <?php if ($win==0) {continue;} ?>
        <table>
            <tr>

                <td style="width: 300px">
            <?php for($i=1;$i<=$count;$i++):?>
                    <image src="/games/agt/images/games/<?php echo $game ?>/icons/small_<?php echo $sym?>.png" />
            <?php endfor;?>
                </td>
                <td>
                    <span style="font-size: 20pt"> <?php echo $win*$bet*( in_array($sym,$c['anypay']) ? $lines:1) ?> </span>
                </td>
            </tr>
        </table>    
    <?php endforeach; ?>
                   
                   <br><br>
<?php endforeach; ?>
<br>



<?php foreach($c['wild'] as $sym): ?>        
      <image src="/games/agt/images/games/<?php echo $game ?>/icons/small_<?php echo $sym?>.png" />   
      substitutes for symbols exepts 
      <?php foreach($c['anypay'] as $sym): ?>        
        <image src="/games/agt/images/games/<?php echo $game ?>/icons/small_<?php echo $sym?>.png" />   
      <?php endforeach; ?>           
      <?php if ($c['wild_multiplier']>1): ?>
        and multiplies win by <?php echo $c['wild_multiplier'] ?>
      <?php endif;?>
      
<?php endforeach; ?>           
<br>



<?php foreach($c['scatter'] as $sym): ?>        
      <?php foreach($c['free_games'] as $count=>$free): ?>
            <?php if ($free==0) {continue;} ?>
            <?php for($i=1;$i<=$count;$i++):?>
                    <image src="/games/agt/images/games/<?php echo $game ?>/icons/small_<?php echo $sym?>.png" />
            <?php endfor;?>
            <?php echo $free ?> Bonus Bets 
            <?php if ($c['free_multiplier']>1) :?>
                (during Bonus Bets all winnings are multiplies by <?php echo $c['free_multiplier'] ?>)
            <?php endif; ?>
            
            <br>         
      <?php endforeach; ?>           

      
<?php endforeach; ?>           
                   

        
        