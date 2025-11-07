<style>
    .popup-close {
        right: 22%;
    }
</style>
<div id="baner-popup" class="popup-body" >

    <div class="popup-body popup-sm popup-dark">

        <div class="popup-close"></div>

        <div class="popup-content" style="width: 100%;"><!-- add here class of popup -->
            <div class="popup-border">
                <div class="light-header active">
                    <div class="light-header__twinkle_1"></div>
                    <div class="light-header__twinkle_2"></div>
                    <span class="light-header__title"></span>
                </div>

                <div class="popup-center contact">
                    <?php echo $content; ?>
                </div>

            </div>
            
            <script>
                $(document).ready(function () {
                    $('[data-nav=menu]').click(function () {
                        $('body').toggleClass('menu-open')
                    });
                })
            </script>
        </div>

    </div>


</div>