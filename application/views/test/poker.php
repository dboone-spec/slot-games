<span style="font-size: 20pt">Bet cost:	<?php echo $bet ?></span><br>
<span style="font-size: 20pt">Coin:	<?php echo $lines ?></span><br>
<table>
         
        
    <?php foreach($c['level'] as $num=>$lName): ?>
        <tr> 
            <td>
                <?php echo $lName.' '.card::sample($lName)?>
            </td>
            
            <td> <?php echo $c['pay'][$lines][$num]*$bet ?> </td>
           
            
            
        </tr>
    <?php endforeach; ?>
    </table>