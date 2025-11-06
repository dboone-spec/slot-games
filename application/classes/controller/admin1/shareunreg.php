<?php

class Controller_Admin_Shareunreg extends Controller_Admin_Base
{

    public function action_index()
    {
        $view = new View('admin/shareunreg/index');

        $view->headers = [
                'id'    => 'ID',
                'name'  => 'Имя',
                'email' => 'Email',
                'sent'  => 'Отправляли рассылку?',
        ];

        $sql    = <<<SQL
                Select *
                    FROM "users_unreg" order by id desc  limit 100
SQL;
        $result = db::query(database::SELECT,$sql)->execute()->as_array();

        $count = key(db::query(1, 'select count(*) from users_unreg where sent = 0')->execute()->as_array('count'));


        $view->u_unreg = $result;
        $view->not_sent_count = $count;
        $view->dir=$this->dir;
        $this->template->content = $view;
    }

    public function action_import()
    {
        if(isset($_POST["submit"]))
        {
            $filename = $_FILES["file"]["tmp_name"];
            if($_FILES["file"]["size"] > 0)
            {
                $file          = fopen($filename,"r");
                $temp_filename = 'temp.csv';
                while($r             = fread($file,filesize($filename)))
                {
                    file_put_contents($temp_filename,$r);
                }
                fclose($file);

                foreach(file($temp_filename) as $f)
                {
                    $row   = explode(';',$f);
                    $sql   = <<<SQL
                        INSERT INTO users_unreg(name, email, sent)
                        VALUES (:name,:email, 0)
                        ON CONFLICT(email) DO update set name=:name, sent=0
SQL;
                    $name  = UTF8::trim($row[0]);
                    $email = UTF8::trim($row[1]);
                    if(!Valid::email($email))
                    {
                        continue;
                    }
                    $params = [
                            ':name'  => $name != '' ? $name : null,
                            ':email' => $email,
                    ];
                    db::query(database::INSERT,$sql)->parameters($params)->execute();
                }
                unlink($temp_filename);
            }
        }
        $this->request->redirect($this->dir . '/shareunreg');
    }

}
