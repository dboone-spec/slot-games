<?php

    class jackpot
    {

        protected $_min_value = array();
        protected $_max_value = array();
        protected $_min_bet = array();
        protected $_max_bet = array();
        protected $_avg = array();
        protected $_oborot = array();
        protected $_profit = array();
        protected $_counts = array();
        protected $_sum = array();
        protected $_offices = [];
        protected $_games_z = 0.9;
        protected $_game = null;
        protected $_count = 1;
        protected $_procent = 5;
        protected static $_instance = null;
        public static $display_refresh_period = 15; //обновлять данные по jackpot-ам на мониторах 1 раз в Х сек
        public static $display_show_winner_period = 300; //отображать выигравших jackpot за последние Х сек

        public static function instance()
        {
            if (!self::$_instance)
            {
                self::$_instance = new jackpot();
            }
            return self::$_instance;
        }


        public function toFile()
        {

            if ($this->_office->id)
            {
                $sql = "SELECT type, current, office_id, game FROM jackpots WHERE office_id = :office_id AND active = 1 ORDER BY type";

                $data = array();
                foreach (db::query(Database::SELECT, $sql)->param(':office_id', $this->_office->id)->execute() as $jp)
                {
                    if (!isset($data[$jp['game']]['jp']))
                    {
                        $data[$jp['game']]['jp'] = array();
                    }
                    $data[$jp['game']]['jp'][] = array(
                        'type' => $jp['type'],
                        'current' => $jp['current'],
                    );
                }

                $sql = <<<SQL
				SELECT
					CASE WHEN ac.user_id > 0 THEN u.visible_name
					ELSE left(ac.ticket_id::varchar, 1) || CASE WHEN char_length(ac.ticket_id::varchar) > 2 THEN repeat('*', (char_length(ac.ticket_id::varchar)-2)) ELSE '' END ||right(ac.ticket_id::varchar, 1)
					END as winner,
					ac.user_id as user,
					ac.ticket_id as ticket,
					ac.amount,
					ac.created,
					ac.game
				FROM additional_charges ac
				LEFT JOIN users u ON u.id = ac.user_id
				WHERE
					ac.office_id = :office_id
					AND
					ac.type = 'jackpot'
					AND ac.created > :from
				ORDER BY ac.id DESC
SQL;
                foreach (db::query(Database::SELECT, $sql)->param(':office_id', $this->_office->id)->param(':from', time() - self::$display_show_winner_period)->execute() as $r)
                {
                    if (!isset($data[$r['game']]['win']))
                    {
                        $data[$r['game']]['win'] = array();
                    }
                    $data[$r['game']]['win'][] = array(
                        'winner' => $r['winner'],
                        'user' => $r['user'],
                        'ticket' => $r['ticket'],
                        'created' => $r['created'],
                        'amount' => $r['amount'],
                    );
                }

                $data = json_encode($data);

                file_put_contents(DOCROOT . $this->_office->jp_folder() . $this->_office->hash . ".json", $data);
            }
        }

    }
    