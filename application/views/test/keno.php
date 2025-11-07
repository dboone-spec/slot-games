<span style="font-size: 20pt">Bet cost:	<?php echo $bet ?></span><br>

<style>
      table {
        width: 70%;
        border-collapse: collapse;
      }
      td, th {
        border: 1px solid #98bf21;
        padding: 3px 7px 2px 7px;
      }
      th {
        text-align: left;
        padding: 5px;
        background-color: #A7C942;
        color: #fff;
      }
      .alt td { background-color: #EAF2D3; }
      
      @media print {
    .pagebreak {
       page-break-after: auto;
    } 
 }
    </style>    

<table>
        <tr> 
        <td style="text-align: right"> Selected count </td>
        <?php foreach($c['pay'] as $num=>$el): ?>
            <td rowspan="2"> <?php echo $num ?> </td>
        <?php endforeach; ?>    
            
        </tr>
        <tr> 
         <td> coincidences </td>
            
        
        </tr>

        
        <?php foreach($c['pay'] as $num=>$el): ?>
            <tr>
               <td> <?php echo $num ?> </td>
                <?php for($i=1;$i<=count($c['pay']);$i++): ?>
                    <td> <?php echo isset($c['pay'][$i][$num])? $c['pay'][$i][$num]*$bet: '-' ?> </td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>    
        
   
    </table>