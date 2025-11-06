<?php

class Bot {

    protected $config;
    protected $keyboard;
    public $office_id;
    protected  $user=null;
    protected $chatId;

    public $reply_message_id;

    public $enableGames=[

//        "bluestar100"=>"100 Blue Star", //!need to resize icons!
//        "pharaoh2"=>"Pharaoh II",
//        "6icepepper"=>"Ice Pepper 6 reels",
        "timemachine2"=>"Time Machine II",
        "icecream100"=>"100 Ice Cream",
        "megashine"=>"Mega Shine",
        "bigfive"=>"Big Five",
        "megashine100"=>"100 Mega Shine",
        "wizard"=>"Wizard",
//        "aislot"=>"AI",
        "bitcoin"=>"Bitcoin",
        "icecream"=>"Ice Cream",
//        "6icecream100"=>"100 Ice Cream 6 reels",
//        "6icepepper40"=>"40 Ice Pepper 6 reels",
//        "dreamcatcher100"=>"100 Dream catcher", //!need to resize icons!
        "icepepper100"=>"100 Ice Pepper",
        "infinitygems"=>"Infinity gems",
//        "doubleice"=>"Double Ice",
//        "casino"=>"Casino",
        "extraspin"=>"Extra spin",
        "arabiannights2"=>"Arabian nights 2",
        "megaice"=>"Ice Fruits",
        "aladdin"=>"Aladdin",
        "arabiannights"=>"Arabian nights",
//        "iceiceice"=>"Ice ice ice",
        "bigfoot40"=>"40 Big Foot",
        "gems50"=>"50 Gems",
//        "6megaice40"=>"40 Ice Fruits 6 reels",
//        "6megaice"=>"Ice Fruits 6 reels",
//        "6icecream"=>"Ice Cream 6 reels",
        "icepepper"=>"Ice Pepper",
        "wildwest"=>"Wild West",
        "happysanta50"=>"50 Happy Santa",
        "happysanta"=>"Happy Santa",
        "bigfoot"=>"Big Foot",
        "shiningstars100"=>"100 Shining Stars",
        "aroundtheworld"=>"Around the World",
        "megaice100"=>"100 Ice Fruits",
        "extraspin3"=>"Extra spin III",
        "dreamcatcher"=>"Dream catcher",
//        "6dreamcatcher"=>"Dream catcher 6 reels",
        "gems20"=>"Gems",
        "shiningstars"=>"Shining Stars",
        "leprechaun"=>"The leprechaun",
//        "jokers100"=>"100 Jokers", //!need to resize icons!
        "crystalskull100"=>"100 Crystal skull",
//        "6bluestar40"=>"40 Blue Star 6 reels",
//        "besthottest40"=>"40 Lucky Clover", //!need to resize icons!
        "applesshine50"=>"50 Apple's shine",
        "applesshine"=>"Apple's shine",
//        "6bluestar"=>"Blue Star 6 reels",
        "crystalskull"=>"Crystal skull",
//        "double"=>"Double Hot",
        "hotpepper100"=>"100 Hot Pepper",
//        "6dreamcatcher100"=>"100 Dream catcher 6 Reels",
//        "6hotpepper40"=>"40 Hot Pepper 6 reels",
        "megahot20"=>"Mega hot",
//        "hothothot5"=>"5 Hot hot hot",
        "coolblizzard"=>"Blue Star",
        "firefighters"=>"Firefighters",
        "bankofny"=>"Grand Theft",
        "hotpepper"=>"Hot Pepper",
//        "6hotpepper"=>"Hot Pepper 6 reels",
//        "tesla"=>"Tesla",
        "besthottest20"=>"20 Lucky Clover",
//        "6superhot40"=>"40 Super Hot 6 reels",
        "megahot100"=>"100 Mega Hot",
//        "bookofset"=>"Book of Set",
        "crown"=>"Crown",
        "anonymous"=>"Anonymous",
        //"piratesgold"=>"Pirate Gold", кривой thumb
        "greenhot"=>"Green Hot",
//        "besthottest100"=>"100 Lucky Clover", //!need to resize icons!
        "besthottest5"=>"5 Lucky Clover",
        "cherryhot"=>"Cherry Hot",
        "iceqween"=>"Ice Queen",
        "jokers20"=>"Jokers",
        "luckyhot10"=>"Lucky Hot",
//        "6luckyclover20"=>"20 Lucky Clover 6",
//        "6luckyclover40"=>"40 Lucky Clover 6",
        "stalker"=>"Stalker",
//        "6superhot5"=>"Super Hot 6 reels",
//        "tropichot"=>"Tropic Hot",
        "sevenhot20"=>"Seven Hot",

            ];


    public function __construct($config,$chatId) {

        if (!is_array($config)){
            $config= Kohana::$config->load('tgbot/'.$config);
        }
        $this->config=$config;
        $this->office_id=$this->config['office_id'];
        $this->chatId=$chatId;

    }

    public function getInfoText() {
        $info_text=arr::get($this->config,'info_text','Hi! I\'m bot which allow You to play brilliance AGT games right here! Enjoy!'.PHP_EOL.'https://site-domain.com');

        $cache_key = 'tg_info_text_'.$this->office_id;
        dbredis::instance()->select(5);

        $cache_time=Date::HOUR;

        $apitext='';

        if (!$apitext = dbredis::instance()->get($cache_key)) {

            $api=gameapi::instance(0);
            $apitext=$api->getInfoTextTG($this->office_id);

            if(!$apitext) {
                $apitext='';
            }

            dbredis::instance()->set($cache_key, $apitext);
            dbredis::instance()->expire($cache_key, $cache_time);
        }

        dbredis::instance()->select(0);

        if(!empty($apitext)) {
            $info_text=$apitext;
        }

        return $info_text;
    }

    public function gameExists($game,$use_key=false){
        if($use_key) {
            return isset($this->enableGames[$game]);
        }
        return in_array($game,$this->enableGames);
    }

    public function gameId($gameName){
        return array_keys($this->enableGames,$gameName)[0];

    }

    public function gameName($gameId){

        return arr::get($this->enableGames,$gameId);
    }

    public function user(){
        if (is_null($this->user)){
            $this->user=new Model_User(['office_id'=> $this->office_id, 'tg_id'=>(string) $this->chatId]);
        }
        return $this->user;

    }

    protected function betAmount(){

        if (empty($this->user()->tg_bet)){
            return 5;
        }

        return th::number_format($this->user()->tg_bet);
    }




    public function keyboard($name,$data=[]){

        $this->keyboard=null;


        if ($name=='main' || $name=='quality'){
            $quality=$data['quality']??dbredis::instance()->get('imageQuality'.$this->chatId);
            if(empty($quality)) {
                $quality='low';
            }

            $new_quality=($quality=='low')?'high':'low';

            $keyboard= [
                    'keyboard' => [
                                    [
                                        ['text' => 'Games'],

                                    ],
                                    [
                                        ['text' => 'Balance'],
                                        ['text' => 'Set '.$new_quality.' quality'],
                                    ],
                                    [
                                        ['text' => 'Information'],
                                        ['text' => 'Go to website'],
                                    ]

                                ],
                    'resize_keyboard'=>true

                        ];

        }


        if ($name=='gameSelect'){

            $keyboard[]=[ ['text' => "Main menu"] ];
            $size=3;
            $row=[];
            foreach ($this->enableGames as $game){
                $row[]=['text' => $game];
                if (count($row)==$size){
                    $keyboard[]=$row;
                    $row=[];
                }
            }
            if (count($row)>0){
                $keyboard[]=$row;
            }
            $keyboard[]=[ ['text' => "Main menu"] ];


            $keyboard= [
                    'keyboard' => $keyboard,
                    'resize_keyboard'=>true
                        ];

        }


        if ($name == 'game'){

            $keyboard= [
                    'keyboard' => [
                                    [
                                        ['text' => 'Spin '.$this->betAmount()],

                                    ],
                                    [
                                        ['text' => 'Bet'],
                                        ['text' => 'Info'],
                                        ['text' => 'Main menu'],
                                    ]


                                ],
                    'resize_keyboard'=>true

                        ];

        }

        if ($name=='gameDouble'){



            $keyboard= [
                    'keyboard' => [
                                    [
                                        ['text' => 'Double'],

                                    ],

                                    [
                                        ['text' => 'Spin '.$this->betAmount()],

                                    ],
                                    [
                                        ['text' => 'Bet'],
                                        ['text' => 'Info'],
                                        ['text' => 'Main menu'],
                                    ]

                                ],
                    'resize_keyboard'=>true

                        ];

        }
        if ($name=='gameFG'){

            $text='Spin free game';


            $keyboard= [
                    'keyboard' => [
                                    [
                                        ['text' => $text],
                                    ],
                                    [
                                        ['text' => 'Info'],
                                        ['text' => 'Main menu'],
                                    ]

                                ],
                    'resize_keyboard'=>true

                        ];

        }
        if ($name=='double'){
            $keyboard= [
                    'keyboard' => [
                                    [
                                        ['text' => 'Red'],
                                        ['text' => 'Black'],

                                    ],

                                    [
//                                        ['text' => 'Spin '.$this->betAmount()],

                                    ],
                                    [
                                        ['text' => 'Collect'],
                                        ['text' => 'Main menu'],
                                    ]

                                ],
                    'resize_keyboard'=>true

                        ];

        }

        if ($name=='bet'){

            $keyboard= [
                    'keyboard' => [
                                    [
                                        ['text' => '1'],
                                        ['text' => '2'],
                                        ['text' => '3'],
                                        ['text' => '5'],

                                    ],
                                    [
                                        ['text' => '10'],
                                        ['text' => '15'],
                                        ['text' => '25'],
                                        ['text' => '35'],

                                    ],
                                    [
                                        ['text' => '50'],
                                        ['text' => '75'],
                                        ['text' => '100'],
                                        ['text' => '200'],
                                    ],


                                    [
                                        ['text' => 'Back to game'],
                                    ]

                                ],
                    'resize_keyboard'=>true

                        ];

            /*
            'keyboard' => [


                                    ['text' => 'Spin '.$this->betAmount],

                                ],
                    'resize_keyboard'=>true

                        ];
            */
        }



        $this->keyboard=json_encode($keyboard);

        return $this;


    }





    public function sendText($text){

         $data =
            array(
                'chat_id' => $this->chatId,
                'text' => $text
            );

        $this->sendAction();

        return $this->tgSend('sendMessage',$data);

    }

    public function sendHTML($text){

         $data =
            array(
                'chat_id' => $this->chatId,
                'text' => $text,
                'parse_mode'=>'HTML'
            );

        $this->sendAction();

        return $this->tgSend('sendMessage',$data);

    }


     public function sendAction($type='typing'){

         $data =
            array(
                'chat_id' => $this->chatId,
                'action' => $type,

            );

        return $this->tgSend('sendChatAction',$data);

    }

     public function sendPhoto($url,$caption=null){

         $data =
            array(
                'chat_id' => $this->chatId,
                'photo' => $url,

            );
        if (!empty ($caption)){
            $data['caption'] =$caption;
        }


        $this->sendAction('upload_photo');

        return $this->tgSend('sendPhoto',$data);

    }


    public function tgSend($method,$data=[],$repeat=false){

        if (!empty ($this->keyboard)){
            $data['reply_markup'] =$this->keyboard;
        }

//        $data['reply_to_message_id'] = $this->reply_message_id;

        $url="https://api.telegram.org/bot{$this->config['token']}/$method";

        $request_id=guid::create();

        logfile::create(date('Y-m-d H:i:s') . ' REQUEST ['.$request_id.']: '. $url .PHP_EOL,'botout');

        logfile::create(date('Y-m-d H:i:s') . ' DATA ['.$request_id.']: '. print_r($data,1) .PHP_EOL,'botout');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        logfile::create(date('Y-m-d H:i:s') . ' RESPONSE ['.$request_id.']: '. $result .PHP_EOL,'botout');

        $jresult=json_decode($result,1);

        if($jresult['ok']==false && !$repeat) {
            //need resend
            $this->tgSend($method,$data,true);
            Kohana::$log->add(Log::ALERT,'RESENDING TG REQUEST');
        }

        return $result;

    }


    //https://api.telegram.org/bot2116860625:AAHwEizW_MHGyP9NDSE0X_h5B2vtupZ9Ys8/webhookinfo
    public function setCallback($url){


        $data=[
            'url'=>$url,
            "allowed_updates"=> json_encode(["callback_query",'message'])
        ];

        return $this->tgSend('setWebhook',$data);

    }

    public function init(){


    }



}

