
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,18,0" width="100%" height="100%" id="test" align="middle">
<param name="allowFullScreen" value="true" />
<param name="movie" value="/resources/igrosoft/<?php if(arr::get($game,'type')==1) {echo $name.'/preloader.swf?game='.$name.'.swf&game_xcasle=181&game_yscale=168&game_x=0&game_y=0';} else {echo $name.'/index.swf';}?>" />
<param name="bgcolor" value="#000000" />
<embed src="/resources/igrosoft/<?php if(arr::get($game,'type')==1) {echo $name.'/preloader.swf?game='.$name.'.swf&game_xcasle=181&game_yscale=168&game_x=0&game_y=0';} else {echo $name.'/index.swf';}?>" allowFullScreen="true" bgcolor="#000000" width="100%" height="100%"name="game" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
		





