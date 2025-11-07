<?php
class super_head{

static $css=array();
static $js=array();


static function css($file=null){
	if (empty($file)){
		$str='';
		foreach (array_unique(self::$css) as $css){
			$str.='<link rel="stylesheet" type="text/css" href="'.$css.'" media="all" />'."\r\n";
		} 
		return $str;
	}
	else{
		self::$css[]=$file;
	}
	return true;
}


static function js($file=null){
	if (empty($file)){
		$str='';
		foreach (array_unique(self::$js) as $js){
			$str.='<script type="text/javascript" src="'.$js.'"></script>'."\r\n";
		}
		return $str; 
	}
	else{
		self::$js[]=$file;
	}
	
	return true;
}



}