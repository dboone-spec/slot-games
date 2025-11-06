
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Инструкции</h1>

            <div class="row">
                <div class="col-sm-12" >

                    <?php if( in_array(Person::$role,[ 'sa', 'gameman'])): ?>
                        <a href="/files/online/gameman.pdf" >
                            Инструкция менеджера (gameman)
                        </a><br>
                    <?php endif ?>

                    <?php if( in_array(Person::$role,[ 'sa', 'gameman','client'])): ?>
                        <a href="/files/online/client.pdf" >
                           Инструкция администратора (client)
                        </a><br>
                    <?php endif ?>

                    <?php if( in_array(Person::$role,[ 'sa', 'gameman','client','cashier'])): ?>
                        <a href="/files/online/cashier.pdf" >
                            Инструкция кассира
                        </a><br>
                    <?php endif ?>

                    <?php if( in_array(Person::$role,[ 'sa', 'gameman','client','cashier'])): ?>
                        <a href="/files/online/rfid.pdf" >
                            Инструкция по работе с RFID
                        </a><br>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Программы</h1>

            <div class="row">
                <div class="col-sm-12" >

                    <?php if( in_array(Person::$role,[ 'sa', 'gameman','client'])): ?>
                        <a href="/k/v2/mangopxe-x86_64_v4.zip" >
                           PXE
                        </a><br>
                        <a href="/k/v2/kiosk_flash_v4.iso" >
                           Live образ
                        </a><br>
                        <a href="https://sourceforge.net/projects/win32diskimager/files/latest/download" >
                           Win32 Disk Imager
                        </a><br>
                    <?php endif ?>

                    <?php if( in_array(Person::$role,[ 'sa', 'gameman','client','cashier'])): ?>
                        <a href="/k/v2/sl500_winclient.zip" >
                           Winclient
                        </a><br>
                        <a href="/k/v2/UsbDriver.zip" >
                           RFID drivers
                        </a><br>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>