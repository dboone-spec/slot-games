<?php

class tz {

    public static $timezones = array(
        '-11' => "(GMT-11:00) Midway Island, Samoa",
        '-10' => "(GMT-10:00) Hawaii",
        '-9' => "(GMT-09:00) Alaska",
        '-8' => "(GMT-08:00) Pacific Time (US &amp; Canada), Tijuana",
        '-7' => "(GMT-07:00) Arizona, Mountain Time (US &amp; Canada), Chihuahua, Mazatlan",
        '-6' => "(GMT-06:00) Mexico City, Monterrey, Saskatchewan, Central Time (US &amp; Canada)",
        '-5' => "(GMT-05:00) Eastern Time (US &amp; Canada), Indiana (East), Bogota, Lima",
        '-4' => "(GMT-04:00) Atlantic Time (Canada), La Paz, Santiago",
        '-3' => "(GMT-03:30) Newfoundland, Buenos Aires, Greenland",
        '-2' => "(GMT-02:00) Stanley",
        '-1' => "(GMT-01:00) Azores, Cape Verde Is.",
        '0' => "(GMT) Casablanca, Dublin, Lisbon, London, Monrovia",
        '1' => "(GMT+01:00) Amsterdam, Belgrade, Berlin, Bratislava, Brussels, Budapest, Copenhagen, Ljubljana, Madrid, Paris, Prague, Rome, Sarajevo, Skopje, Stockholm, Vienna, Warsaw, Zagreb",
        '2' => "(GMT+02:00) Athens, Bucharest, Cairo, Harare, Helsinki, Istanbul, Jerusalem, Kyiv, Minsk, Riga, Sofia, Tallinn, Vilnius",
        '3' => "(GMT+03:00) Moscow, Baghdad, Kuwait, Nairobi, Riyadh",
        '4' => "(GMT+04:00) Baku, Volgograd, Muscat, Tbilisi, Yerevan",
        '5' => "(GMT+05:00) Karachi, Tashkent",
        '6' => "(GMT+06:00) Ekaterinburg, Almaty, Dhaka",
        '7' => "(GMT+07:00) Novosibirsk, Bangkok, Jakarta",
        '8' => "(GMT+08:00) Krasnoyarsk, Chongqing, Hong Kong, Kuala Lumpur, Perth, Singapore, Taipei, Ulaan Bataar, Urumqi",
        '9' => "(GMT+09:00) Irkutsk, Seoul, Tokyo",
        '10' => "(GMT+10:00) Yakutsk, Brisbane, Canberra, Guam, Hobart, Melbourne, Port Moresby, Sydney",
        '11' => "(GMT+11:00) Vladivostok",
        '12' => "(GMT+12:00) Magadan, Auckland, Fiji",
    );
   
    public static function lst() {
        return self::$timezones;
    }
}
