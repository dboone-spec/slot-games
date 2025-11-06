<?php


class Model_Bonus_Code extends ORM {


	protected $_primary_key = 'name';
	protected $_created_column = array('column' => 'created', 'format' => true);


    public function labels() {
        return [
            'name' => 'Код',
            'count' => 'Количество',
            'bonus' => 'Бонус(для all, unique_user %)',
            'created' => 'Создан',
            'type' => 'Тип',
            'min_sum_pay' => 'Минимальная сумма пополнения',
            'game' => 'Игра',
            'spins' => 'Спины',
            'lines' => 'Линии',
            'bet' => 'Ставка(на линию)',
            'show' => 'Показывать в платежке?',
            'sort_index' => 'Сортировка для платежки',
            'vager' => 'Вейджер',
            'time' => 'Действителен до',
        ];
    }

    public function check_pay_sum($amount, $user_id) {
        if($this->type == 'bezdep' OR $amount >= $this->min_sum_pay) {
            return true;
        }
        return $this->min_sum_pay;
    }

    public function bind_freespins($user_id) {
        if(!in_array($this->type, ['freespin', 'bonus_freespin', 'fixed_freespin', 'bezdep_freespin'])) {
            return false;
        }

        $user = new Model_User($user_id);
        $user->activate_freespins($this->id, -1);
    }

    public function pay_prize_bonus() {
        if($this->type != 'fixed_prize') {
            return false;
        }

        if($this->user_id <=0) {
            return false;
        }

        database::instance()->begin();

        try {

            $entered_code = $this->add_code_entered($this->user_id,$this->id);

            $sql='UPDATE users SET
                    bonus=bonus+:bonus,
                    sum_bonus=sum_bonus+:bonus,
                    bonusbreak=bonusbreak+:bonusbreak
                    WHERE id=:uid';

            db::query(1,$sql)
                ->param(':bonus', $this->bonus)
                ->param(':bonusbreak', $this->bonus * $this->vager)
                ->param(':uid', $this->user_id)
                ->execute();

            if($entered_code->loaded()) {
                $entered_code->used = 1;
                $entered_code->save();

                $bonus = new Model_Bonus();
                $bonus->user_id = $this->user_id;
                $bonus->bonus = $this->bonus;
                $bonus->type = $this->type;
                $bonus->payed = 1;
                $bonus->log = json_encode([
                    "bonus_name" => $this->name,
                    "id" => $this->id,
                    "code_type" => $this->type,
                    "bonus" => $this->bonus
                ]);
                $bonus->save();
            }

            database::instance()->commit();
        } catch (Exception $ex) {
            database::instance()->rollback();
            throw $ex;
        }
        return true;
    }

    public function pay_bezdep_bonus() {
        if(!in_array($this->type,['bezdep','bezdep_freespin'])) {
            return false;
        }
        
        if($this->type=='bezdep_freespin') {
            $this->bind_freespins($this->user_id);
            return true;
        }

        database::instance()->begin();

        try {
            $sql='UPDATE users SET
                    bonus=bonus+:bonus,
                    sum_bonus=sum_bonus+:bonus,
                    bonusbreak=bonusbreak+:bonusbreak
                    WHERE id=:uid';

            db::query(1,$sql)
                ->param(':bonus', $this->bonus)
                ->param(':bonusbreak', $this->bonus * $this->vager)
                ->param(':uid', auth::$user_id)
                ->execute();

            $entered_code = new Model_Bonus_Codeentered([
                "user_id" => auth::$user_id,
                "code_id" => $this->id,
            ]);

            if($entered_code->loaded()) {
                $entered_code->used = 1;
                $entered_code->save();
                auth::user()->reload();

                $bonus = new Model_Bonus();
                $bonus->user_id = auth::$user_id;
                $bonus->bonus = $this->bonus;
                $bonus->type = $this->type;
                $bonus->payed = 1;
                $bonus->log = json_encode([
                    "bonus_name" => $this->name,
                    "id" => $this->id,
                    "code_type" => $this->type,
                    "bonus" => $this->bonus
                ]);
                $bonus->save();
            }

            database::instance()->commit();
        } catch (Exception $ex) {
            database::instance()->rollback();
            throw $ex;
        }

        return true;
    }

    /*
     * формирование сообщения при удачном вводе бонус кода
     */
    public function success_text_message() {
        $message = "";

        $office = new Model_Office(OFFICE);
        $min_sum_pay = $this->min_sum_pay;

        switch ($this->type) {
            case "bezdep":
                $message = __("На Ваш счет начислено ").$this->bonus.__(" бонусов");
                break;
            case "bezdep_freespin":
                $game = new Model_Game(['name' => $this->game]);
                $message = __("Вам начислено ") .$this->spins.__(" фриспинов в игре ").$game->visible_name;
                break;
            case "freespin":
                $game = new Model_Game(['name' => $this->game]);

                $message = __("При следующем пополнении счета на сумму не менее "). $min_sum_pay. ' ' . $office->currency->code . '. '
                    . __("Вам будет начислено ") .$this->spins.__(" фриспинов в игре ").$game->visible_name;
                break;
            case "fixed":
                $message = __("При следующем пополнении счета на сумму не менее ").$min_sum_pay. ' ' . $office->currency->code . '. '
                    . __("Вам будет начислено ") .$this->bonus .__(" бонусов");
                break;
            case "bonus_freespin":
                $game = new Model_Game(['name' => $this->game]);

                $message = __("При следующем пополнении счета на сумму не менее ").$min_sum_pay. ' ' . $office->currency->code . '. '
                    .__("Вам будет начислен бонус в размере ") . $this->bonus * 100 . __("% от суммы пополнения")
                    . ' ' . __("и") . ' ' .$this->spins.__(" фриспинов в игре ").$game->visible_name;
                break;
            case "fixed_freespin":
                $game = new Model_Game(['name' => $this->game]);

                $message = __("При следующем пополнении счета на сумму не менее ").$min_sum_pay. ' ' . $office->currency->code . '. '
                    . __("Вам будет начислено ") .$this->bonus .__(" бонусов")
                    . ' ' . __("и") . ' ' .$this->spins.__(" фриспинов в игре ").$game->visible_name;
                break;
            default :
                $message = __("При следующем пополнении счета на сумму не менее ").$min_sum_pay. ' ' . $office->currency->code . '. '
                    .__("Вам будет начислен бонус в размере ") . $this->bonus * 100 . __("% от суммы пополнения");
        }

        return $message;
    }

    /*
     * TODO
     *
     * Выносил отдельно выплату бонусов
     * В model_payment нужно выпилить такой же
     * функционал и добавить заместо него эту функцию
     */
    public function pay($user_id, $amount=0) {
        $bonus=0;
		$vager=0;

        $bonus_code_entered = new Model_Bonus_Codeentered([
            "user_id" => $user_id,
            "code_id" => $this->id,
        ]);

        if(!$bonus_code_entered->loaded()) {
            $bonus_code_entered->user_id = $user_id;
            $bonus_code_entered->code_id = $this->id;
        }

        database::instance()->begin();

        switch ($this->type) {
            case 'freespin':
                $this->bind_freespins($user_id);
                break;
            case 'fixed':
                $bonus_code_entered->used = 1;

                $bonus=$this->bonus;
                $vager=$bonus*($this->vager);

                $bonus_model = new Model_Bonus();
                $bonus_model->user_id = $user_id;
                $bonus_model->bonus = $bonus;
                $bonus_model->type = 'payment';
                $bonus_model->payed = 1;
                $bonus_model->log = json_encode([
                    "bonus_code" => $this->name,
                    "bonus_id" => $this->id,
                    "bonus_payment_coeff" => $this->bonus,
                    "bonus" => $bonus,
                ]);
                $bonus_model->save();
                break;
            default :
                $bonus_code_entered->used = 1;

                $bonus=$amount*$this->bonus;
                $vager=$bonus*($this->vager);

                $bonus_model = new Model_Bonus();
                $bonus_model->user_id = $user_id;
                $bonus_model->bonus = $bonus;
                $bonus_model->type = 'payment';
                $bonus_model->payed = 1;
                $bonus_model->log = json_encode([
                    "bonus_code" => $this->name,
                    "bonus_id" => $this->id,
                    "bonus_payment_coeff" => $this->bonus,
                    "bonus" => $bonus,
                ]);
                $bonus_model->save();
                break;
        }

        $bonus_code_entered->save();

        $sql='update users
            set bonus=bonus+:bonus,
                sum_bonus=sum_bonus+:bonus,
                bonusbreak=bonusbreak+:vager
            where id=:uid';

        db::query(1,$sql)
            ->param(':bonus',$bonus)
            ->param(':vager',$vager)
            ->param(':uid',$user_id)
            ->execute();

		database::instance()->commit();

        return $bonus;
    }

    public function add_code_entered($user_id, $code_id, $ip='auto', $used=0) {
        $entered = new Model_Bonus_Codeentered([
            'user_id' => $user_id,
            'code_id' => $code_id,
        ]);

        if(!$entered->loaded()) {
            $entered->user_id = $user_id;
            $entered->code_id = $code_id;
            $entered->ip = $ip;
        }

        $entered->used = $used;
        $entered->save();
        return $entered->reload();
    }

    public function use_code($user_id, $ip='auto') {
        $this->add_code_entered($user_id, $this->id, $ip);

        $used = false;
        $bonus = 0;
        $vager = 0;

        switch ($this->type) {
            case 'freespin':
                $this->bind_freespins($user_id);
                $used = true;
                break;
            case 'fixed':
                $bonus = $this->bonus;
                $vager=$bonus*($this->vager);

                $sql='update users
                    set bonus=bonus+:bonus,
                        sum_bonus=sum_bonus+:bonus,
                        bonusbreak=bonusbreak+:vager
                    where id=:uid';

                database::instance()->begin();

                db::query(1,$sql)
                    ->param(':bonus',$bonus)
                    ->param(':vager',$vager)
                    ->param(':uid',$user_id)
                    ->execute();

                database::instance()->commit();

                $used = true;
        }

        return $used;
    }

    public function bind_daylyfs($user_id) {
        $user = new Model_User($user_id);
        $parent = $user->parent_acc();

        if(!$user->loaded() OR !$parent->dayly_freespins>0) {
            return false;
        }

        $this->add_code_entered($user_id, $this->id, 'auto', 1);

        $f = new Model_Freespin([
            "user_id" => $user_id,
            "code_id" => $this->id
        ]);

        if(!$f->loaded()) {
            $f->user_id = $user_id;
            $f->game = $this->game;
            $f->freespins_current = 0;
            $f->freespins_break = $parent->dayly_freespins;
            $f->bet = $this->bet;
            $f->lines = $this->lines;
            $f->vager = $this->vager;
            $f->active = -1;
            $f->payed = 1;
            $f->code_id = $this->id;
        } else {
            $f->active = -1;
            $f->freespins_break += $parent->dayly_freespins;
        }

        $bonus = $parent->dayly_freespins * $this->lines * $this->bet;

        try {
            database::instance()->begin();

            $sql='UPDATE users SET
                bonus=bonus+:bonus,
                sum_bonus=sum_bonus+:bonus,
                bonusbreak=bonusbreak+:bonusbreak
                WHERE id=:uid';

            db::query(1,$sql)
                ->param(':bonus', $bonus)
                ->param(':bonusbreak', $bonus * $this->vager)
                ->param(':uid', auth::$user_id)
                ->execute();

            $bonus_model = new Model_Bonus();
            $bonus_model->user_id = $user_id;
            $bonus_model->bonus = $bonus;
            $bonus_model->share_prize = $this->share_prize;
            $bonus_model->type = $this->type;
            $bonus_model->payed = 1;
            $bonus_model->log = json_encode([
                "bonus_name" => $this->name,
                "id" => $this->id,
                "code_type" => $this->type,
                "bonus" => $bonus
            ]);
            $bonus_model->save();

            $f->save();

            $history = new Model_Daylyhistory();
            $history->user_id = $parent->id;
            $history->type = 'pay_fs';
            $history->bonus = $parent->dayly_freespins;
            $history->save();

            $parent->dayly_freespins = 0;
            $parent->active_day = 0;
            $parent->reset_cashback = mktime(0,0,0);
            $parent->save();

            $user->freespin_code_active = $f->id;
            $user->save();

            database::instance()->commit();
        } catch (Database_Exception $e) {
            database::instance()->rollback();
        }
    }
}
