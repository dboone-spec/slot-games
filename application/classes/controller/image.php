<?php

class Controller_Image extends Controller
{

	public function action_backuptop() {
        $files = $this->rglob('/var/www/agt/www/' . 'games' . DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . '*.png');

        $destination = '/var/www/agt/www/'.'backuptop';

        if(!is_dir($destination)) {
            mkdir($destination);
        }

        foreach($files as $file) {

            if(strpos($file,'top.png')===FALSE || strpos($file,'common')!==FALSE) {
                continue;
            }

            $paths = explode(DIRECTORY_SEPARATOR,$file);
            if(!is_dir($destination.DIRECTORY_SEPARATOR.$paths[count($paths)-3])) {
                mkdir($destination.DIRECTORY_SEPARATOR.$paths[count($paths)-3]);
            }

            if(!is_dir($destination.DIRECTORY_SEPARATOR.$paths[count($paths)-3].DIRECTORY_SEPARATOR.'ui')) {
                mkdir($destination.DIRECTORY_SEPARATOR.$paths[count($paths)-3].DIRECTORY_SEPARATOR.'ui');
            }

            copy($file,$destination.DIRECTORY_SEPARATOR.$paths[count($paths)-3].DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR.'top.png');
        }
    }

    public function action_ezgif()
    {
        $url = 'https://ezgif.com/apng-to-webp?url=' . $file;

        $out = DOCROOT . '';

        $p      = new Parser();
        $p->get($url);
        $action = $p->html()->find('.form.ajax-form',0)->action;
        $token  = $p->html()->find('[name=token]',0)->value;
        $file   = $p->html()->find('[name=file]',0)->value;

        $p1 = new Parser();
        $p1->post($action,['token' => $token,'file' => $file]);

        $p2 = new Parser();

        file_put_contents($out,$p2->get($p1->html()->find('.save',1)->href));
    }

    public function rglob($pattern,$flags = 0)
    {
        $files = glob($pattern,$flags);
        foreach(glob(dirname($pattern) . '/*',GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files,$this->rglob($dir . '/' . basename($pattern),$flags));
        }
        return $files;
    }

    public function action_unpackerreels()
    {
        $jsons = $this->rglob(DOCROOT . 'games' . DIRECTORY_SEPARATOR . 'egt' . DIRECTORY_SEPARATOR . 'html5' . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR . 'reelImages*.json');
        foreach($jsons as $iu => $file)
        {
            $file = str_replace('/',DIRECTORY_SEPARATOR,$file);
            $json = json_decode(file_get_contents($file),1);

            if(!isset($json['meta']))
                continue;

            $f  = explode(DIRECTORY_SEPARATOR,$file);
            $ff = explode('.',$f[count($f) - 1]);

            $game_code = $f[count($f) - 3];

            $e = new Model_Egtgame(['code'=>$game_code]);

            if(!$e->loaded()) {
                echo 'ALERT '.$game_code.PHP_EOL;
                continue;
            }

            $game_name = $e->game->name;

            $dir = DOCROOT.'sloticons'.DIRECTORY_SEPARATOR.$game_name;

            if(!is_dir($dir))
            {
                mkdir($dir,02777);
                chmod($dir,02777);
            }

            $i = explode('?',$json['meta']['image']);

            $png = str_replace($ff[0].'.json',$i[0],$file);


            $small_origin=false; //если true - берет 18х18

            foreach($json['frames'] as $name => $frame)
            {
                if($small_origin && strpos($name,'s')===false) {
                    continue;
                }

                if(!$small_origin && strpos($name,'s')!==false) {
                    continue;
                }


                $exx = explode('.',$name);
                $newname = ((int) str_replace('s','',$exx[0])).'.'.$exx[1];


                $if = Image::factory($png);
                $if->crop($frame['frame']['w'],$frame['frame']['h'],$frame['frame']['x'],$frame['frame']['y']);

                if(!$small_origin) {
                     $if->resize(40,40);
                }

                $filename       = basename($i[0]);
                $file_extension = strtolower(substr(strrchr($filename,"."),1));

                switch($file_extension)
                {
                    case "gif": $ctype = "image/gif";
                        break;
                    case "png": $ctype = "image/png";
                        break;
                    case "jpeg":
                    case "jpg": $ctype = "image/jpeg";
                        break;
                    default:
                }
                echo $name . '<br />';

                //            header('Content-type: ' . $ctype);
                $if->save($dir.DIRECTORY_SEPARATOR.$newname);
                //            exit;
            }

        }
    }

    //silver fox unpack
    public function action_unpacsf()
    {

        $jsons = glob(DOCROOT . 'RES' . DIRECTORY_SEPARATOR . '*.json');

        foreach($jsons as $iu => $file)
        {
            $json = json_decode(file_get_contents($file),1);

            if(!isset($json['meta']))
                continue;

            $f  = explode(DIRECTORY_SEPARATOR,$file);
            $ff = explode('.',$f[count($f) - 1]);

            $dir_name = $ff[0];

            $dir_path = DOCROOT . 'RES_unpack' . DIRECTORY_SEPARATOR . $dir_name;

            if(!is_dir($dir_path))
            {
                mkdir($dir_path,02777);
                chmod($dir_path,02777);
            }



            //create dir


            $i = explode('?',$json['meta']['image']);


            foreach($json['frames'] as $name => $frame)
            {
                $if = Image::factory('RES/' . $i[0]);
                $if->crop($frame['frame']['w'],$frame['frame']['h'],$frame['frame']['x'],$frame['frame']['y']);


                $filename       = basename($i[0]);
                $file_extension = strtolower(substr(strrchr($filename,"."),1));

                switch($file_extension)
                {
                    case "gif": $ctype = "image/gif";
                        break;
                    case "png": $ctype = "image/png";
                        break;
                    case "jpeg":
                    case "jpg": $ctype = "image/jpeg";
                        break;
                    default:
                }
                echo $name . '<br />';
                //            header('Content-type: ' . $ctype);
                $if->save($dir_path . DIRECTORY_SEPARATOR . str_replace('/','',$name) . '.' . $file_extension);
                //            exit;
            }
        }
    }

    //from texture packer
    public function action_unpacker()
    {

        $jsons = glob(DOCROOT . '20super' . DIRECTORY_SEPARATOR . '*.json');

        foreach($jsons as $iu => $file)
        {
            $json = json_decode(file_get_contents($file),1);

            if(!isset($json['meta']))
                continue;

            $f  = explode(DIRECTORY_SEPARATOR,$file);
            $ff = explode('.',$f[count($f) - 1]);

            $dir_name = $ff[0];

            $dir_path = DOCROOT . 'unpack' . DIRECTORY_SEPARATOR . $dir_name;

            if(!is_dir($dir_path))
            {
                mkdir($dir_path,02777);
                chmod($dir_path,02777);
            }



            //create dir


            $i = explode('?',$json['meta']['image']);


            foreach($json['frames'] as $name => $frame)
            {
                $if = Image::factory('20super/' . $i[0]);
                $if->crop($frame['frame']['w'],$frame['frame']['h'],$frame['frame']['x'],$frame['frame']['y']);


                $filename       = basename($i[0]);
                $file_extension = strtolower(substr(strrchr($filename,"."),1));

                switch($file_extension)
                {
                    case "gif": $ctype = "image/gif";
                        break;
                    case "png": $ctype = "image/png";
                        break;
                    case "jpeg":
                    case "jpg": $ctype = "image/jpeg";
                        break;
                    default:
                }
                echo $name . '<br />';
                //            header('Content-type: ' . $ctype);
                $if->save($dir_path . DIRECTORY_SEPARATOR . str_replace('/','',$name) . '.' . $file_extension);
                //            exit;
            }
        }
    }

    public function action_gameswebp()
    {

        //results: igrosoft webp: 13mb; png: 27mb;

        $files = $this->rglob(DOCROOT . 'games' . DIRECTORY_SEPARATOR . '*.png');
        $y     = 0;

        th::vd($files);

        foreach($files as $f)
        {
            $file  = str_replace(DOCROOT . 'games','',$f);
            $paths = explode("/",$file);
            if(in_array($paths[1],['igrosoft','novomatic']))
            {
                if(!in_array('images',$paths))
                {
                    continue;
                }

                $y++;

                $br = 0;

                $minipaths = [];
                $destpaths = [];

                foreach($paths as $i => $dir)
                {

                    if($i == 0)
                    {
                        $prevpaths = [];
                        $br        = 0;
                    }

                    if($dir == 'images')
                    {
                        $br          = 1;
                        $prevpaths[] = $dir;
                        $newpaths    = [];
                        continue;
                    }
                    else
                    {
                        if(count($paths) - 1 != $i)
                        {
                            $prevpaths[] = $dir;
                        }
                    }

                    if($br == 1)
                    {
                        $newdir = DOCROOT . 'games' . implode(DIRECTORY_SEPARATOR,$prevpaths) . DIRECTORY_SEPARATOR . 'webp' . implode(DIRECTORY_SEPARATOR,$newpaths);

                        if(!is_dir($newdir))
                        {
                            mkdir($newdir,02777);
                            chmod($newdir,02777);
                        }
                    }

                    //action
                    if($i == count($paths) - 1)
                    {

                        $r = explode('/',$file);

                        $out = str_replace('png','webp',$newdir . DIRECTORY_SEPARATOR . $r[count($r) - 1]);

                        if(file_exists($out))
                        {
                            continue;
                        }

                        var_dump($out);
                        exit;

                        if($r[count($r) - 2] == 'firefox')
                        {



                            $url = 'https://ezgif.com/apng-to-webp?url=' . $file;

                            $p      = new Parser();
                            $p->get($url);
                            $action = $p->html()->find('.form.ajax-form',0)->action;
                            $token  = $p->html()->find('[name=token]',0)->value;
                            $file   = $p->html()->find('[name=file]',0)->value;

                            $p1 = new Parser();
                            $p1->post($action,['token' => $token,'file' => $file]);

                            $p2 = new Parser();

                            file_put_contents($out,$p2->get($p1->html()->find('.save',1)->href));

                            /*
                              $s = 'C:\\libwebp-1.0.2-windows-x64\\bin\\apng2webp.exe -q100 "'.DOCROOT.'games'.$file.'"';
                              exec($s);
                              $fff = str_replace('/',DIRECTORY_SEPARATOR,str_replace('.png','.webp',DOCROOT.'games'.$file));
                              if(!file_exists($fff)) {
                              echo 'ERROR: '.$out.PHP_EOL;
                              $s = '"C:\\Program Files (x86)\\reaConverter 7 Pro\\cons_rcp.exe" -s "'.DOCROOT.'games'.$file.'" -o "'.$out.'"';
                              exec($s);
                              exit;
                              continue;
                              }
                              rename(str_replace('/',DIRECTORY_SEPARATOR,str_replace('.png','.webp',DOCROOT.'games'.$file)), $out);
                             */
                            continue;
                        }

                        if(file_exists($out))
                        {
                            continue;
                        }
                        $s = 'C:\\libwebp-1.0.2-windows-x64\\bin\\cwebp.exe -quiet -pass 10 -alpha_method 1 -alpha_filter best -m 6 -mt -lossless -q 100 ' . DOCROOT . 'games' . $file . ' -o ' . $out;
                        exec($s);
                    }
                }
            }
        }
    }

    public function action_gamesaudio()
    {

        $format = 'ogg';

        $files = $this->rglob(DOCROOT . 'games' . DIRECTORY_SEPARATOR . '*.' . $format);
        $y     = 0;

        foreach($files as $f)
        {
            $file  = str_replace(DOCROOT . 'games','',$f);
            $paths = explode("/",$file);
            if(in_array($paths[1],['igrosoft','novomatic']))
            {
                if(!in_array('audio',$paths))
                {
                    continue;
                }

                $y++;

                $br = 0;

                $minipaths = [];
                $destpaths = [];

                foreach($paths as $i => $dir)
                {

                    if($i == 0)
                    {
                        $prevpaths = [];
                        $br        = 0;
                    }

                    if($dir == 'audio')
                    {
                        $br          = 1;
                        $prevpaths[] = $dir;
                        $newpaths    = [];
                        continue;
                    }
                    else
                    {
                        if(count($paths) - 1 != $i)
                        {
                            $prevpaths[] = $dir;
                        }
                    }

                    if($br == 1)
                    {
                        $newdir = DOCROOT . 'games' . implode(DIRECTORY_SEPARATOR,$prevpaths) . DIRECTORY_SEPARATOR . 'compressed' . implode(DIRECTORY_SEPARATOR,$newpaths);

                        if(!is_dir($newdir))
                        {
                            mkdir($newdir,02777);
                            chmod($newdir,02777);
                        }
                    }

                    //action
                    if($i == count($paths) - 1)
                    {

                        $r = explode('/',$file);

                        $out = $newdir . DIRECTORY_SEPARATOR . $r[count($r) - 1];

                        if(file_exists($out))
                        {
                            continue;
                        }

                        $s = 'ffmpeg -y -i ' . DOCROOT . 'games' . $file . ' -acodec libvorbis -vn ' . $out;
                        exec($s);
                        echo $file . PHP_EOL;
                    }
                }
            }
        }
    }

    public function action_gamesjwebp()
    {

        //results: igrosoft webp: 13mb; png: 27mb;

        $files = $this->rglob(DOCROOT . 'games' . DIRECTORY_SEPARATOR . '*.jpg');
        $y     = 0;

        foreach($files as $f)
        {
            $file  = str_replace(DOCROOT . 'games','',$f);
            $paths = explode("/",$file);
            if(in_array($paths[1],['igrosoft','novomatic']))
            {
                if(!in_array('images',$paths))
                {
                    continue;
                }

                $y++;

                $br = 0;

                $minipaths = [];
                $destpaths = [];

                foreach($paths as $i => $dir)
                {

                    if($i == 0)
                    {
                        $prevpaths = [];
                        $br        = 0;
                    }

                    if($dir == 'images')
                    {
                        $br          = 1;
                        $prevpaths[] = $dir;
                        $newpaths    = [];
                        continue;
                    }
                    else
                    {
                        if(count($paths) - 1 != $i)
                        {
                            $prevpaths[] = $dir;
                        }
                    }

                    if($br == 1)
                    {
                        $newdir = DOCROOT . 'games' . implode(DIRECTORY_SEPARATOR,$prevpaths) . DIRECTORY_SEPARATOR . 'webp' . implode(DIRECTORY_SEPARATOR,$newpaths);

                        if(!is_dir($newdir))
                        {
                            mkdir($newdir,02777);
                            chmod($newdir,02777);
                        }
                    }

                    //action
                    if($i == count($paths) - 1)
                    {

                        $r = explode('/',$file);

                        $out = str_replace('jpg','webp',$newdir . DIRECTORY_SEPARATOR . $r[count($r) - 1]);

                        if(file_exists($out))
                        {
                            continue;
                        }
                        $s = 'C:\\libwebp-1.0.2-windows-x64\\bin\\cwebp.exe -quiet -pass 10 -alpha_method 1 -alpha_filter best -m 6 -mt -lossless -q 100 ' . DOCROOT . 'games' . $file . ' -o ' . $out;
                        exec($s);
                    }
                }
            }
        }
    }

    public function action_index()
    {

        $i = $this->request->param('id');

        $w = 140;
        $h = 140;

        $dir = "f:\\temp\\src\\$i\\";

        $files = scandir($dir);



        $iw     = $w * 5;
        $ih     = $h * ceil((count($files) - 2) / 5);
        $img    = Image::factory('none.png');
        $img->resize($iw,$ih,IMAGE::NONE);
        $x      = 0;
        $y      = 0;
        $idle   = [];
        $blur   = [];
        $active = [];
        $unblur = [];
        $k      = 0;

        foreach($files as $file)
        {
            if($file == '.' or $file == '..')
            {
                continue;
            }
            $k++;
            if($k == 1)
            {
                $idle[]   = "$x,$y,$w,$h";
                $blur[]   = "$x,$y,$w,$h";
                $unblur[] = "$x,$y,$w,$h";
            }
            if($k > 1 and $k <= 5)
            {
                $blur[]   = "$x,$y,$w,$h";
                $unblur[] = "$x,$y,$w,$h";
            }
            if($k > 5)
            {
                $active[] = "$x,$y,$w,$h";
            }
            $image = Image::factory($dir . $file);
            $img->watermark($image,$x,$y);

            $x += $w;
            if($x >= $iw)
            {
                $x = 0;
                $y += $h;
            }
        }
        $img->save("$i.png");



        $unblur = array_reverse($unblur);


        $idle   = implode(",\r\n		",$idle);
        $blur   = implode(",\r\n		",$blur);
        $unblur = implode(",\r\n		",$unblur);
        $active = implode(",\r\n		",$active);

        $str = "	idle: [$idle],
	blur:[$blur],
	unblur:[$unblur],
	win:[$active]";

        file_put_contents("$i.txt",$str);
    }

    public function action_checkmail()
    {
        if($mail_id = $this->request->param('id'))
        {
            $n = new Model_Newsletter($mail_id);
            if($n->loaded() && $n->opened == 0)
            {
                $n->opened = time();
                $n->save();
            }
        }
    }

}
