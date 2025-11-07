
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>EGT Portal</title>
	<meta name="viewport" content="width=device-width,height = device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <?php if(th::isMobile()): ?>
	<base href="<?php echo URL::base('http'); ?>/games/egt/mobile/" target="_blank" >
    <?php else: ?>
        <?php if(false && auth::user()->office->localhost): ?>
            <base href="<?php echo auth::user()->office->localhost; ?>/games/egt/html5/" target="_blank" >
        <?php else: ?>
            <base href="<?php echo URL::base('http'); ?>/games/egt/html5/" target="_blank" >
        <?php endif; ?>
    <?php endif; ?>
	<style type="text/css" media="screen">
		html, body, body.sidebars { width:100%; height:100%; margin:0; padding:0;}
	</style>
	<script src="../js/jquery.js"></script>
	<script>
		//document.domain = "";

        function setUserAgent(window, userAgent) {
            // Works on Firefox, Chrome, Opera and IE9+
            if (navigator.__defineGetter__) {
                navigator.__defineGetter__('userAgent', function () {
                    return userAgent;
                });
            } else if (Object.defineProperty) {
                Object.defineProperty(navigator, 'userAgent', {
                    get: function () {
                        return userAgent;
                    }
                });
            }
            // Works on Safari
            if (window.navigator.userAgent !== userAgent) {
                var userAgentProp = {
                    get: function () {
                        return userAgent;
                    }
                };
                try {
                    Object.defineProperty(window.navigator, 'userAgent', userAgentProp);
                } catch (e) {
                    window.navigator = Object.create(navigator, {
                        userAgent: userAgentProp
                    });
                }
            }
        }
        if(navigator.userAgent.indexOf("Terra")+1>0) {
            setUserAgent(window,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0');
        }


        <?php if(th::isMobile() && !arr::get($_GET,'closeurl')): ?>

            var clursear = '?closeurl=/';

            if(window.location.search!=clursear) {
                    window.location.search = clursear;
            }
        <?php endif; ?>

        function onExitGamePlatformEGT() {
            window.location = '/';
        }

		$(function(){

			var connectionParams = {
//				tcpHost: "ws://127.0.0.1",
//				tcpPort: "8080",
				tcpHost: "wss://ws.mangobet.org",
				tcpPort: "2053",
				sessionKey: "<?php echo Cookie::get('egt_session_key'); ?>",
				lang: "<?php echo I18n::$lang; ?>",
				gameIdentificationNumber: <?php echo $egtgame->gameIdentificationNumber; ?>
			};

			var additionalParams = {
				base: "https://free.egtmgs.com//html5/"
			};
            <?php if(th::isMobile()): ?>
			var additionalParams = {
				base: "https://free.egtmgs.com//mobile/"
			};
            <?php endif; ?>
			$.ajax({
				type: "GET",
				crossDomain: "true",
                <?php if(th::isMobile()): ?>
				url: "../init/init_mobile_cf_test.js",
                <?php else: ?>
				url: "../init/init_desktop_cf_test.js",
                <?php endif; ?>
				dataType: "script",
				contentType: "text/plain",
				success: function() {
                    <?php if(th::isMobile()): ?>
                    EGT.initMobile(connectionParams);
                    <?php else: ?>
					initDesktopHtml(connectionParams);
                    <?php endif;?>
                }
			});

		});
	</script>
</head>
<body>
<?php if(count($jackpots)): ?>
<?php echo block::gamejp(); ?>
<?php endif; ?>
<?php echo block::rfid_listen(); ?>
</body>
</html>


