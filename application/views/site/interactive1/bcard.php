
<style>
    .mt100 {
        margin-top: 100px;
    }

    .containers {
        max-width: 400px;
        margin: 50px auto;
    }

    .top {
        font-size: 30px;
        line-height: 1.3;
        display: flex;
    }
    .top__content {
        flex: 0 0 50%;
    }
    .top__title {
        color: #fff;
    }
    .top__text {
        font-size: 18px;
        margin: 15px 0px 0px 0px;
    }
    .top__image {
        margin: 0px 0px 0px 30px;
    }
    .top__logo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        display: inline-block;
        cursor: default;
    }
    .top__logo img {
        width: 150px;
        position: absolute;
        top: 0;
        left: 0;
    }
    @media (max-width: 400px) {
        .containers {
            max-width: 310px;
            width: 300px;
        }
        .top__image {
            margin: 0px 0px 0px 20px;
        }
        .top__logo {
            width: 100px;
            height: 100px;
        }
        .top__logo img {
            width: 100px;
        }
    }

    h3 {
        font-weight: 500;
        margin: 0px 0px 20px 0px;
    }

    .bottom__list {
        margin: 0px 0px 5px 0px;
    }

    .bottom__list span {
        margin: 0px 5px 0px 0px;
    }

    .bottom__list li {
        display: flex;
    }

    .bottom__list a {
        display: block;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

</style>

<div class="container">
    <div class="row mt100 py-12">
        <div class="col-12">
            <h2 class="font-weight-normal text-7 mt-2 mb-0 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="200">
                <strong class="font-weight-extra-bold">Contact</strong> Us
            </h2>
        </div>
    </div>
    <div class="top containers">
        <div class="top__content">
            <div class="top__title">
                    <div class="top__name">OLEKSII</div>
                    <div class="top__surname">BILETSKYI</div>
            </div>
            <div class="top__text">Sales Development Manager</div>
        </div>
        <div class="top__image">
            <a href="#" class="top__logo">
                <img src="/theme/interactive1/img/photo_website.jpg" alt="personal photo">
            </a>
        </div>
    </div>
    <div class="bottom containers">
        <h3>Contacts</h2>
        <ul class="bottom__list">
            <li><span>Phone:</span><a href="tel:+905380164152">+90 538 016 4152</a></li>
            <li><span>Mail:</span><a href="mailto:oleksii@site-domain.com">oleksii@site-domain.com</a></li>
            <li><span>Telegram:</span><a href="https://t.me/OleksiiBiletskyi">https://t.me/OleksiiBiletskyi</a></li>
            <li><span>LinkedIn:</span><a href="https://www.linkedin.com/in/oleksii-biletskyi-8b4248318/ ">https://www.linkedin.com/in/oleksii-biletskyi-8b4248318/</a></li>
            <li><span>Skype:</span><a href="live:.cid.df9fe1dd627d4085">live:.cid.df9fe1dd627d4085</a></li>
            <li><span>Website:</span><a href="https://site-domain.com">https://site-domain.com</a></li>
        </ul>
    </div>
</div>
