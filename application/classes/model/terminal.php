<?php

class Model_Terminal extends Model_User
{
    protected $_table_name = 'users';

    public function labels()
    {
        $l = [
                'id' => __('ИД'),
                'name' => __('Логин'),
                'email' => __('Эл. почта'),
                'last_login' => __('Посл. вход'),
                'visible_name' => __('Name'),
                'created' => __('Дата регистрации'),
                'amount' => __('Баланс'),
                'getspam' => 'Спам?',
                'bonus' => __('Бонусов'),
                'bonusbreak' => __('Отыгрыш'),
                'bonuscurrent' => __('Отыграно'),
                'phone' => __('Телефон'),
                'phone_confirm' => __('Тел. подтвержден'),
                'sum_win' => __('Выиграно'),
                'sum_amount' => __('Ставок'),
                'sum_in' => __('Ввел'),
                'sum_out' => __('Вывел'),
                'sum_bonus' => __('Бонусов всего'),
                'email_confirm' => __('Почта подтверждена'),
                'msrc' => __('Метка'),
                'dsrc' => __('Домен'),
                'referal_link' => __('Реф. ссылка'),
                'comp_current' => __('Компоинтов'),
                'updated' => __('Обновлен'),
                'blocked' => __('Status'),
                'blocked_text' => __('Текст указанный при блокировке'),
                'code' => __('Код для восстановления пароля'),
                'partner' => __('Партнер'),
                'last_bonus_type' => __('Тип последнего бонуса'),
                'last_bonus' => __('Сумма последнего бонуса (>0 если польз. не забрал)'),
                'autopay' => __('Автовыплата'),
                'email_valid' => 'Валидная почта?',
                'balances' => 'Балансы (Баланс/Бонусов)',
                'phone_code' => __('Код подтверждения для телефона'),
                'last_confim_email' => __('Последнее подтверждение почты'),
                'sum_diff' => __('Вин'),
        ];


        if(person::$role != 'sa')
        {
            $l['balances'] = __('Баланс');
            $l['office_id'] = __('ППС');
        }

        return $l;
    }

    public function __construct($id = NULL)
    {
        //todo проверить если в юзерс добавить коммент, непоедет ли админка. вроде были проблемы с этим полем
        if(!isset($this->_table_columns['comment'])) {
            $this->_table_columns['comment'] = [
                    'type' => 'string',
                    'column_name' => 'comment',
                    'column_default' => '',
                    'is_nullable' => '1',
                    'data_type' => 'character varying',
                    'character_maximum_length' => '',
                    'numeric_precision' => '',
                    'numeric_scale' => '',
                    'datetime_precision' => '',
            ];
        }
        if(!isset($this->_table_columns['rfid'])) {
            $this->_table_columns['rfid'] = [
                    'type' => 'string',
                    'column_name' => 'rfid',
                    'column_default' => '',
                    'is_nullable' => '1',
                    'data_type' => 'character varying',
                    'character_maximum_length' => '',
                    'numeric_precision' => '',
                    'numeric_scale' => '',
                    'datetime_precision' => '',
            ];
        }
        return parent::__construct($id);
    }
}
