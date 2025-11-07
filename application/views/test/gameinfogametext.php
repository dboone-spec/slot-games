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

<h1>
    Games:
</h1>
<?php foreach ($allGames as $game => $c) : ?>
    <h2>
        <?php echo $games[$game]['visible_name'] ?>
    </h2>
    <br>

    Paytable: <br>
    <table style="border-collapse: collapse; border: 1px solid black;">
        <tr>
            <td style="text-align: right">Сombinations of kind</td>
            <?php foreach ($c['pay'][0] as $cp => $val): ?>
                <td rowspan="2"> <?php echo $cp ?></td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td>Symbol's number</td>
        </tr>

        <?php foreach ($c['pay'] as $num => $pay): ?>
            <tr>
                <td> <?php echo $num . ' ' . $c['mark'][$num] ?>

                </td>
                <?php foreach ($pay as $val): ?>
                    <td> <?php echo $val ?></td>
                <?php endforeach; ?>


            </tr>
        <?php endforeach; ?>

    </table>

    <?php if (isset($c['scatter']) && count($c['scatter']) > 0 ): ?>

    Freegames:
    <table style="border-collapse: collapse; border: 1px solid black;">
        <tr>
            <td >Number of scatters on the screen</td>
            <?php foreach ($c['pay'][0] as $cp => $val): ?>
                <td > <?php echo $cp ?></td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td>FreeGames wins</td>
            <?php foreach ($c['pay'][0] as $cp => $val): ?>
                <td > <?php echo $c['free_games'][$cp] ?? 0; ?></td>
            <?php endforeach; ?>

        </tr>

    </table>
    <?php endif; ?>


    <?php if ($games[$game]['type'] == 'shuffle'): ?>
        <?php if ($game == 'monet'): ?>
            The playing field is three pictures by Claude Monet cut into 4 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'hokusai'): ?>
            The playing field is four pictures by Hokusai cut into 16 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'bosch'): ?>
            The playing field is three pictures by Hieronymus Bosch cut into 9 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'vangogh'): ?>
            The playing field is four pictures by Van Gogh cut into 16 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'klimt'): ?>
            The playing field is five pictures by Klimt cut into 16 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'vermeer'): ?>
            The playing field is three pictures by Vermeer cut into 9 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'renoir'): ?>
            The playing field is three pictures by Pierre Auguste Renoir cut into 6 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'munch'): ?>
            The playing field is four pictures by Munch cut into 9 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'rembrandt'): ?>
            The playing field is three pictures by Rembrandt van Rijn cut into 6 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'michelangelo'): ?>
            The playing field is three pictures by Michelangelo Buonarroti cut into 4 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'cezanne'): ?>
            The playing field is three pictures by Paul Cézanne cut into 12 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'rubens'): ?>
            The playing field is three pictures by Peter Paul Rubens cut into 4 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'gauguin'): ?>
            The playing field is three pictures by Paul Gauguin cut into 6 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

        <?php if ($game == 'manet'): ?>
            The playing field is three pictures by Édouard Manet cut into 12 pieces. The win depends on the number of pieces of one picture on the playing field. Malfunction voids all pays and plays.
        <?php endif; ?>

    <?php else: ?>
        All pays are left to right on adjacent reels, on selected lines, beginning with the leftmost reel, except scatters. Scatter wins are added to the payline wins. Highest payline add/or scatter wins only paid. Line wins are multiplied by the bet value on the winning line. Scatter wins are multiplied by the total bet value. Malfunction voids all pays and plays.
    <?php endif ?>


    <br>
    <?php if (isset($c['lines']) && count($c['lines']) > 0) : ?>
        Paylines configurations:<br>
        <?php foreach ($c['lines'] as $num => $lineQ): ?>
            <div class="pagebreak lineblock"
                 style="float: left; width: <?php echo count($lineQ) * 40; ?>px;margin: 4px;">
                Line <?php echo $num ?>:
                <table>
                    <?php foreach ($lineQ as $line): ?>
                        <tr>
                            <?php foreach ($line as $el): ?>
                                <td <?php if ($el == 1) : ?> style="background-color:green;" <?php endif; ?>> &nbsp;
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="clear:both"></div>

    <br><br>
<?php endforeach; ?>
