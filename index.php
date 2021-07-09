<?php
if (file_exists("debug")){
    if (file_exists("system/core/dev/core.php")) {
        include("system/core/dev/core.php");
    }else{
        die(base64_decode("PGgyIHN0eWxlPSdwb3NpdGlvbjogZml4ZWQ7bGVmdDogMjAlO3RvcDogMzAlOyc+5b2T5YmN54mI5pys5Li65Y+R6KGM54mI77yM5peg5rOV5L2/55So6LCD6K+V5qCH6K6wPGJyPueVtuWJjeeJiOacrOeCuueZvOihjOeJiOacrO+8jOeEoeazleS9v+eUqOiqv+ippuaomeiomDxicj5EZWJ1ZyBmbGFnIGNhbm5vdCBiZSB1c2VkIGJlY2F1c2UgdGhlIGN1cnJlbnQgdmVyc2lvbiBpcyBhIHJlbGVhc2U8YnI+54++5Zyo44Gu44OQ44O844K444On44Oz44Gv44Oq44Oq44O844K554mI44Gn44GZ44Gu44Gn44CB44OH44OQ44OD44Kw44OV44Op44Kw44Gv5L2/44GI44G+44Gb44KT44CCPGJyPu2YhOyerCDrsoQg7KCEIOydgCDrsJztlokg7YyQIOycvOuhnCDrlJQg67KEIOq5hSDrp4jtgawg66W8IOyCrOyaqSDtlaAg7IiYIOyXhuyKteuLiOuLpC48L2gyPg=="));
    }
}else{
    if (file_exists("system/core/core.phar")) {
        include("system/core/core.phar");
        include("phar://core.phar/core.php");
    }else{
        die(base64_decode("PGgyIHN0eWxlPSdwb3NpdGlvbjogZml4ZWQ7bGVmdDogMjAlO3RvcDogMzAlOyc+57y65bCR6YeN6KaB5paH5Lu277yM5peg5rOV57un57ut6K6/6ZeuPGJyPue8uuWwkemHjeimgeaWh+S7tu+8jOeEoeazlee5vOe6jOioquWVjzxicj5JbXBvcnRhbnQgZmlsZXMgYXJlIG1pc3NpbmcgYW5kIGNhbm5vdCBiZSBhY2Nlc3NlZCBmdXJ0aGVyPGJyPumHjeimgeOBquODleOCoeOCpOODq+OBjOOBguOCiuOBvuOBm+OCk+OAguOCouOCr+OCu+OCueOCkue2muOBkeOCieOCjOOBvuOBm+OCk+OBp+OBl+OBn+OAgjxicj7spJHsmpQg7ZWcIO2MjOydvCDsnbQg7JeG7Ja0IOyEnCDrjZQg7J207IOBIOygkeq3vCDtlaAg7IiYIOyXhuyKteuLiOuLpC48L2gyPg=="));
    }
}
core::initialize();
core::runWeb();