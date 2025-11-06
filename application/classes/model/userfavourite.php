<?php

class Model_Userfavourite extends ORM
{

    protected $_table_name = 'users_favourite';

    public function checkuser($userid)
    {
        $result = db::query(Database::SELECT,"SELECT user_id FROM users_favourite WHERE user_id = :user_id")
                ->param(':user_id',$userid)
                ->execute()
                ->as_array();
        return $result;
    }

    public function addgame($userid,$game)
    {
        $sql = <<<SQL
                INSERT INTO users_favourite (user_id, games) VALUES (:user_id, :games)
SQL;

        db::query(Database::INSERT,$sql)
                ->param(':user_id',$userid)
                ->param(':games',$game)
                ->execute();
    }

    public function updgames($userid,$game)
    {
        $sql = <<<SQL
                UPDATE users_favourite SET
                    user_id = :user_id,
                    games = :games
                WHERE user_id = :user_id
SQL;

        db::query(Database::UPDATE,$sql)
                ->param(':user_id',$userid)
                ->param(':games',$game)
                ->execute();
    }

    public function getgames($userid)
    {
        $result = db::query(Database::SELECT,"SELECT games FROM users_favourite WHERE user_id = :user_id")
                ->param(':user_id',$userid)
                ->execute()
                ->as_array();
        return $result;
    }
    public function gamesrating()
    {
        $result = db::query(Database::SELECT,"SELECT games FROM users_favourite")// 
                ->execute()
                ->as_array();
        return $result;
    
    }

    public function getgamestmp()
    {
        $result = db::query(Database::SELECT,"SELECT games FROM users_favourite WHERE user_name = 'temporary'")
                ->execute()
                ->as_array();
        return $result;
    }

    public function updgamestmp($game)
    {
        $sql = <<<SQL
                UPDATE users_favourite SET
                    games = :games
                WHERE user_name = 'temporary'
SQL;
        db::query(Database::UPDATE,$sql)
                ->param(':games',$game)
                ->execute();
    }

    public function emptygamestmp()
    {
        $sql = <<<SQL
                UPDATE users_favourite SET
                    games = NULL
                WHERE user_name = 'temporary'
SQL;

        db::query(Database::UPDATE,$sql)
                ->execute();
    }
    public function checkusertmp()
    {
        $result = db::query(Database::SELECT,"SELECT user_id FROM users_favourite WHERE user_name = 'temporary'")
                ->execute()
                ->as_array();
        return $result;
    }
     public function addgametmp($userid,$game)
    {
        $sql = <<<SQL
                INSERT INTO users_favourite (user_id, games, user_name) VALUES (:user_id, :games, 'temporary')
SQL;
        db::query(Database::INSERT,$sql)
                ->param(':user_id',$userid)
                ->param(':games',$game)
                ->execute();
    }

}
