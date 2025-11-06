<?php

class Model_Newsletter extends ORM
    {

        public function labels()
        {
            return [
                'to' => __('Кому'),
                'title' => __('Название'),
                'msrc' => __('Метка'),
                'need_to_send' => __('Нужно отправить'),
                'sended' => __('Отправлено'),
                'opened' => __('Открыто'),
                'message' => __('Письмо'),
            ];
        }

}
    