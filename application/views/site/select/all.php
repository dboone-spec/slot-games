<!DOCTYPE html>

<html>
    <head>
        <title>Select a game</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
           body{
                background-color: black;
                border: solid 10px #fff;
                border-radius: 35px;
                margin: 0;
                padding: 0;
          }

          .container {
                display: flex;
                flex-wrap: wrap;
                margin: 0 auto;
                border: 25px solid blue;
                border-radius: 25px;
                box-shadow: 0 0 45px 2px #00f inset;
          }

          .container .item {
                width: 400px;
                flex: 1 1 25%;
                box-sizing: border-box;
                padding: 50px 20px;
          }

          @media all and (orientation:portrait) {
              .container .item {
                  flex: 1 1 100%;
              }
          }
          .logout_btn {
                position: fixed;
                color: #fff;
                font-size: 20px;
                background: #000;
                padding: 10px;
                border: 2px solid #fff;
                border-radius: 25px;
                right: 2%;
                top: 4%;
          }
        </style>
    </head>
    <body>
        <?php if(auth::$user_id && auth::user()->office_id!=777): ?>
        <a class="logout_btn" href="/login/logout">Logout</a>
        <?php endif; ?>
        <div class="container">
            <?php foreach($games as $game): ?>
                <div class="item">
                    <?php if(!isset($game['brand']) || $game['brand']!='none') :?>
                        <?php if(!auth::user()->office->gameapiurl): ?>
                        <a href="/games/<?php echo ($game['brand'] ?? 'agt'); ?>/<?php echo $game['name']; ?>">
                        <?php else: ?>
                        <a href="/play/<?php echo $game['name']; ?>">
                        <?php endif; ?>
                    <?php  endif;?>
                            <picture>
                                <source type="image/webp" srcset="<?php echo UTF8::str_ireplace('.png','.webp',$game['image']); ?>">
                                <source type="image/png" srcset="<?php echo $game['image']; ?>">
                                <img src="<?php echo UTF8::str_ireplace('.png','.webp',$game['image']); ?>" style="width: 100%">
                            </picture>
                        <p style="color:#FFF; font-size: 16pt;"><?php echo $game['visible_name'] ?> </p>
                    <?php if(!isset($game['brand']) || $game['brand']!='none') :?>
                        </a>
                    <?php  endif;?>
                </div>
            <?php endforeach; ?>
        </table>
    </body>
</html>
