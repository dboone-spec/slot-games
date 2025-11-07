<div class="rules-content">
    <div class="row">
        <div class="col col-md-2 text-big">1</div>
        <div class="col col-md-6" translate="moon_rule1">Make your bet before the flight begins</div>
        <div class="col col-md-4">
            <img src="/games/agt/moon/img/rule1.jpg?v=1"/>
        </div>
    </div>
    <div class="row">
        <div class="col col-md-2 text-big">2</div>
        <div class="col col-md-6" translate="moon_rule2">Take a risk and wait for odds to go high</div>
        <div class="col col-md-4">
            <img src="/games/agt/moon/img/rule2.jpg?v=1"/>
        </div>
    </div>
    <div class="row">
        <div class="col col-md-2 text-big">3</div>
        <div class="col col-md-6" translate="moon_rule3">Cash out before it flies away</div>
        <div class="col col-md-4">
            <img src="/games/agt/moon/img/rule3.jpg?v=1"/>
        </div>
    </div>
    <div class="rowbig row">
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule4">
                You have an option to place two or three bets on the same flight by clicking the plus
                button.
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule5">
                Each bet can be set manually or use auto for a pre-setted bet and collect multiplier.
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule6">
                "Auto bet" and "auto cash out out" functions activate after marking when a new round start.
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule7">
                If the internet connection is interrupted when the bet is active, the game will
                automatically cash out with the current multiplier, and the winning amount will be added to
                your balance.
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule8">
                The winnings are rounded down to 2 digits after the point.
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule9">
                The maximum winnings
                is <?php echo th::float_format($maxWin, $currency->mult ?? 2); ?><?php echo $currency->sym(); ?>
                . The game will will be automatically cash out when this value is reached.
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule10">
                The minimum stake
                is <?php echo th::float_format($minBet, $currency->mult ?? 2); ?><?php echo $currency->sym(); ?>
                .
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule11">
                The maximum stake
                is <?php echo th::float_format($maxBet, $currency->mult ?? 2); ?><?php echo $currency->sym(); ?>
                .
            </div>
        </div>
        <div class="row">
            <div class="col col-md-2">&checkmark;</div>
            <div class="col col-md-10" translate="moon_rule12">
                Malfunction voids all pays and plays.
            </div>
        </div>
        <?php if (auth::user()->office->showfakeversion): ?>
            <div class="row">
                <div class="col col-md-2" style="white-space: nowrap;font-size: 0.4em;color: gold;">Version:
                    11.540. <?php echo isset($game->rtp) ? 'RTP: ' . $game->rtp : 'ads' ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>