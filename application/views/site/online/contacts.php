<div class="container" style="height: 60px">

</div>
<div class="container">

    <!-- Text -->
    <!--	<div class="sixteen columns">
            <p>We create beautiful, usable, professional websites using best practice accessibility and the latest W3C web standards guidelines, resulting in semantic and seo friendly XHTML and CSS. If you’re interested in our services, please send us a message through the form below.</p>
            <br>
        </div>-->

    <!-- Contact Form -->
    <div class="twelve columns">
        <div class="headline no-margin"><h4>Contact Form</h4></div>

        <div class="form-spacer"></div>

        <!-- Success Message -->
        <div class="success-message">
            <div class="notification success closeable" id="notification_1" style="display: block;">
                <p><span>Success!</span> Your message has been sent.</p>
                <a class="close" href="#"></a></div>
        </div>

        <!-- Form -->
        <div id="contact-form">
            <form method="post" action="">

                <div class="field">
                    <label>Name:</label>
                    <input type="text" name="name" class="text">
                </div>

                <div class="field">
                    <label>Email: <span>*</span></label>
                    <input type="text" name="email" class="text">
                </div>

                <div class="field">
                    <label>Subject: <span>*</span></label>
                    <input type="text" name="subject" class="text">
                </div>

                <div class="field">
                    <label>Message: <span>*</span></label>
                    <textarea name="message" class="text textarea"></textarea>
                </div>

                <div class="field">
                    <input type="button" id="send" value="Send Message">
                    <div class="loading"></div>
                </div>

            </form>
        </div>


    </div>



    <div class="four columns google-map">

        <div class="headline no-margin"><h4>Our Contacts</h4></div>
        <b> Email: </b>
        <?php if(defined('KIOSK') && KIOSK): ?>
            mangobetorg@gmail.com
        <?php else: ?>
            <a href="mailto:mangobetorg@gmail.com">mangobetorg@gmail.com</a>
        <?php endif; ?>
        <br>
        <b>     Telegram:  </b>
        <?php if(defined('KIOSK') && KIOSK): ?>
            MangoBetOrg
        <?php else: ?>
            <a href="https://t.me/MangoBetOrg">MangoBetOrg</a>
        <?php endif; ?>
        <br>
        <b> Skype: </b>
        <?php if(defined('KIOSK') && KIOSK): ?>
            MangoBetOrg
        <?php else: ?>
            <a href="#">MangoBetOrg</a>
        <?php endif; ?>


</div>