
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

    .alt td {
        background-color: #EAF2D3;
    }

    @media print {
        html, body {
            width: 210mm;
            height: 297mm;
        }

        .pagebreak {
            break-before: page;
        }
    }
</style>

<?php foreach( $config['poker']  as $game => $c): ?>

<h2>
    <?php echo ucfirst($game) ?>
</h2>
<br>

Paytable:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td style="text-align: right">Bet level</td>
        <?php foreach ($c['pay'] as $cp => $val): ?>
            <td rowspan="2"> <?php echo $cp ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td>Combination</td>
    </tr>

    <?php foreach ($c['level'] as $num => $name): ?>
        <tr>
            <td ><?php echo $name ?> </td>
            <td ><?php echo $c['pay'][1][$num] ?> </td>
            <td ><?php echo $c['pay'][2][$num] ?> </td>
            <td ><?php echo $c['pay'][3][$num] ?> </td>
            <td ><?php echo $c['pay'][4][$num] ?> </td>
            <td ><?php echo $c['pay'][5][$num] ?> </td>


        </tr>


    <?php endforeach; ?>

</table>



    Video poker game is played with one deck of cards (52 cards).
    A game consists of two rounds. Five cards are being dealt. At the end of the first round the player can select which cards to hold.
    After the first round, if the game maintains Auto Hold mode, the player will be offered which cards are best for him/her to hold, as those cards are automatically marked.
    The player can deselect those cards and mark different ones, as he wishes.
    The rest of the cards are “disposed”. A second round starts, after the selection of the cards that will be held is made.
    The “disposed” cards are replaced with different cards from the deck of cards.
    If there is a winning combination, at the end of the second round, the player wins that amount. Malfunction voids all pays and plays.




<div style="clear:both"></div>

<br><br>

<?php endforeach; ?>


<h2>
    Keno Fast
</h2>
<br>

Paytable:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td style="text-align: right">Selected</td>
        <?php for($i=1; $i<=10; $i++): ?>
            <td rowspan="2"> <?php echo $i ?></td>
        <?php endfor; ?>
    </tr>
    <tr>
        <td>Coincided</td>
    </tr>

    <?php for($i=0; $i<=10; $i++): ?>
    <tr>
        <td><?php echo $i ?></td>
        <td><?php echo $config['keno']['pay'][1][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][2][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][3][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][4][$i] ?? '-'; ?></td>

        <td><?php echo $config['keno']['pay'][5][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][5][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][7][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][8][$i] ?? '-'; ?></td>
        <td><?php echo $config['keno']['pay'][9][$i] ?? '-'; ?></td>

        <td><?php echo $config['keno']['pay'][10][$i] ?? '-'; ?></td>
    </tr>
    <?php endfor; ?>


</table>



Player wager by choosing numbers ranging from 1 through 80. Player can choose from 1 to 10 numbers. After player make wager, 20 numbers are drawn at random. Winning is calculated according to the paytable depending on the numbers chosen by the player and the matches between the drawn numbers and the selected numbers. Malfunction voids all pays and plays.




<div style="clear:both"></div>


<br><br>


<h2>
    Rock Paper Scissors
</h2>
<br>

Paytable:
<br>
2 hands:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td>draw</td>
        <td>draw</td>
        <td>win</td>
        <td>absolute</td>
    </tr>
    <tr>
        <td>1st hand</td>
        <td>1</td>
        <td>1.9</td>
        <td>0</td>
    </tr>
    <tr>
        <td>2nd hand</td>
        <td>0</td>
        <td>2.9</td>
        <td>0</td>
    </tr>
</table>
<br><br>
3 hands:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td>draw</td>
        <td>draw</td>
        <td>win</td>
        <td>absolute</td>
    </tr>
    <tr>
        <td>1st hand</td>
        <td>1</td>
        <td>1.9</td>
        <td>0</td>
    </tr>
    <tr>
        <td>2nd hand</td>
        <td>0</td>
        <td>2.9</td>
        <td>0</td>
    </tr>
    <tr>
        <td>3rd hand</td>
        <td>0</td>
        <td>0</td>
        <td>8.7</td>
    </tr>
</table>

<br><br>
4 hands:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td>draw</td>
        <td>draw</td>
        <td>win</td>
        <td>absolute</td>
    </tr>
    <tr>
        <td>1st hand</td>
        <td>0</td>
        <td>3.7</td>
        <td>0</td>
    </tr>
    <tr>
        <td>2nd hand</td>
        <td>1</td>
        <td>1.85</td>
        <td>0</td>
    </tr>
    <tr>
        <td>3rd hand</td>
        <td>2</td>
        <td>0</td>
        <td>0</td>
    </tr>
    <tr>
        <td>4th hand</td>
        <td>0</td>
        <td>0</td>
        <td>26</td>
    </tr>
</table>


    4 hands:
        [draw=>0,win=>3.7,absolute=>0],
        [draw=>1,win=>1.85,absolute=>0],
        [draw=>2,win=>0,absolute=>0],
        [draw=>0,win=>0,absolute=>26],
    ],



<br><br>
5 hands:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td>draw</td>
        <td>draw</td>
        <td>win</td>
        <td>absolute</td>
    </tr>
    <tr>
        <td>1st hand</td>
        <td>0</td>
        <td>5.2</td>
        <td>0</td>
    </tr>
    <tr>
        <td>2nd hand</td>
        <td>1</td>
        <td>1.8</td>
        <td>0</td>
    </tr>
    <tr>
        <td>3rd hand</td>
        <td>1.53</td>
        <td>0</td>
        <td>0</td>
    </tr>
    <tr>
        <td>4th hand</td>
        <td>0.5</td>
        <td>3.5</td>
        <td>0</td>
    </tr>
    <tr>
        <td>5th hand</td>
        <td>0</td>
        <td>0</td>
        <td>78</td>
    </tr>
</table>


<br><br>
5 hands:
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td>draw</td>
        <td>draw</td>
        <td>win</td>
        <td>absolute</td>
    </tr>
    <tr>
        <td>1st hand</td>
        <td>0</td>
        <td>7.6</td>
        <td>0</td>
    </tr>
    <tr>
        <td>2nd hand</td>
        <td>1</td>
        <td>1.7</td>
        <td>0</td>
    </tr>
    <tr>
        <td>3rd hand</td>
        <td>1.3</td>
        <td>0</td>
        <td>0</td>
    </tr>
    <tr>
        <td>5th hand</td>
        <td>0.5</td>
        <td>4.6</td>
        <td>0</td>
    </tr>
    <tr>
        <td>6th hand</td>
        <td>0</td>
        <td>0</td>
        <td>234</td>
    </tr>
</table>


	Game has three possible outcomes: a draw, a win or a loss. The player who decides to choose a stone will beat the other player who chose scissors, but will lose to the one who played paper. If the player chooses paper, he will lose to the player who chose scissors, but will win the one who chose stone. The player who chose scissors wins over the player who chooses paper, but loses to the player who chooses stone. If the players choose the same form the result of the game is a draw. If there are all three forms in the round the result is a draw. There can be multiple winners in a round. If only one player wins, it is an absolute victory. Malfunction voids all pays and plays.


<div style="clear:both"></div>



<br><br>


<h2>
    Spinner
</h2>
<br>

Paytable:
<br>

<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td>draw</td>
        <td>draw</td>
        <td>win</td>
        <td>absolute</td>
    </tr>
    <tr>
        <td>1st hand</td>
        <td>1</td>
        <td>1.9</td>
        <td>0</td>
    </tr>
    <tr>
        <td>2nd hand</td>
        <td>0</td>
        <td>2.9</td>
        <td>0</td>
    </tr>
</table>
<br><br>



Game has three possible outcomes: a draw, a win or a loss. The player who decides to choose a stone will beat the other player who chose scissors, but will lose to the one who played paper. If the player chooses paper, he will lose to the player who chose scissors, but will win the one who chose stone. The player who chose scissors wins over the player who chooses paper, but loses to the player who chooses stone. If the players choose the same form the result of the game is a draw. Malfunction voids all pays and plays.


<div style="clear:both"></div>

<br><br>