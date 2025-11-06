<?php

class Controller_Admin1_Weekactivity extends Controller_Admin1_Base
{


    public function action_index()
    {

        if (arr::get($_GET, 'xls', 'no') == 'go') {
            return $this->excel();
        }

        $timeFrom = isset($_GET['timeFrom']) ? $_GET['timeFrom'] : date('Y-m-d', time() - 60 * 60 * 24);

        list($year, $month, $day) = explode('-', $timeFrom);
        $date = mktime(0, 0, 0, $month, $day, $year);

        $sql = 'select date,value 
                from bets_avg
                where date<=:date   
                    and date>=:dateEnd
                    order by date';

        $rawData = db::query(1, $sql)->param(':date', $date + 60 * 60 * 24)
            ->param(':dateEnd', $date - 60 * 60 * 24 * 6)
            ->execute()
            ->as_array('date');


        $data = [];
        foreach ($rawData as $row) {
            $data[date('Y-m-d', $row['date'])][$row['date']] = $row['value'];
        }


        $view = new View('admin1/weekactivity/index');
        $view->timeFrom = $timeFrom;
        $view->date = $date;
        $view->data = $data;

        $this->template->content = $view;

    }

    public function excel()
    {

        $timeFrom = isset($_GET['timeFrom']) ? $_GET['timeFrom'] : date('Y-m-d', time() - 60 * 60 * 24);

        list($year, $month, $day) = explode('-', $timeFrom);
        $date = mktime(0, 0, 0, $month, $day, $year);

        $sql = 'select date,value 
                from bets_avg
                where date<=:date   
                    and date>=:dateEnd
                    order by date';

        $rawData = db::query(1, $sql)->param(':date', $date + 60 * 60 * 24)
            ->param(':dateEnd', $date - 60 * 60 * 24 * 28)
            ->execute()
            ->as_array('date');


        $data = [];
        foreach ($rawData as $row) {
            $data[date('Y-m-d', $row['date'])][$row['date']] = $row['value'];
        }


        $xlsx = [];
        $row = [__('Time') . '/' . __('Date')];

        for ($i = 0; $i <= 27; $i++) {
            $row[] = date('D d-m-Y', $date - 60 * 60 * 24 * $i);
        }
        $xlsx[] = $row;

        for ($i = 0; $i < 60 * 24; $i++) {

            $row = [(floor($i / 60) < 10 ? '0' : '') . floor($i / 60) . ':' . (($i % 60) < 10 ? '0' : '') . $i % 60];

            for ($j = 0; $j <= 27; $j++) {
                $row[] = $data[date('Y-m-d', $date - 60 * 60 * 24 * $j + 60 * $i)][$date - 60 * 60 * 24 * $j + 60 * $i] ?? 0;
            }
            $xlsx[]=$row;
        }



        $writer = new XLSXWriter();
        $writer->writeSheet($xlsx, 'Sheet1');            // no headers
        $this->response->headers('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->headers('Content-Disposition', 'attachment;filename="weekActivity' . date('Y-m-d', $date) . '.xlsx"');
        $this->response->headers('Cache-Control', 'max-age=0');
        $this->response->body($writer->writeToString());
        $this->auto_render = false;
        return null;
    }


}
