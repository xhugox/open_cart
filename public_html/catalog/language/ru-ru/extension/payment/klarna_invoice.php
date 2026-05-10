<?php
// Text
$_['text_title'] = 'Счет-фактура Klarna - оплата в течение 14 дней';
$_['text_terms_fee']			= '<span id="klarna_invoice_toc"></span> (+%s)<script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\', charge: %s});</script>';
$_['text_terms_no_fee']			= '<span id="klarna_invoice_toc"></span><script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\'});</script>';
$_['text_additional'] = 'Klarna Invoice требует некоторую дополнительную информацию, прежде чем они смогут обработать ваш заказ.';
$_['text_male'] = 'Мужской';
$_['text_female'] = 'Женский';
$_['text_year'] = 'Год';
$_['text_month'] = 'Месяц';
$_['text_day'] = 'День';
$_['text_comment']				= 'Klarna\'s Invoice ID: %s. ' . "\n" . '%s/%s: %.4f';
$_['text_trems_description'] = 'С передачей информации, необходимой для обработки покупки на счете, а также проверки личности и кредитоспособности.
Я согласен отправить данные в Klarna. Я могу отозвать свое <a href="https://online.klarna.com/consent_de.yaws" target="_blank">согласие</a> в любое время с эффектом на будущее.';

// Entry
$_['entry_gender'] = 'Пол';
$_['entry_pno'] = 'Персональный номер';
$_['entry_dob'] = 'Дата рождения';
$_['entry_phone_no'] = 'Номер телефона';
$_['entry_street'] = 'Улица';
$_['entry_house_no'] = 'Дом №.';
$_['entry_house_ext'] = 'Дом доб.';
$_['entry_company'] = 'Регистрационный номер компании';

// Help
$_['help_pno'] = 'Пожалуйста, введите здесь свой номер социального страхования.';
$_['help_phone_no'] = 'Пожалуйста, введите свой номер телефона.';
$_['help_street'] = 'Обратите внимание, что доставка может быть осуществлена ​​только на зарегистрированный адрес при оплате через Klarna.';
$_['help_house_no'] = 'Пожалуйста, введите номер вашего дома.';
$_['help_house_ext'] = 'Пожалуйста, отправьте расширение вашего дома здесь. Например. A, B, C, красный, синий и т. д.';
$_['help_company']				= 'Please enter your Company\'s registration number';

// Error
$_['error_deu_terms']			= 'You must agree to Klarna\'s privacy policy (Datenschutz)';
$_['error_address_match'] = 'Если вы хотите использовать Klarna Invoice, адреса выставления счета и доставки должны совпадать.';
$_['error_network'] = 'Произошла ошибка при подключении к Кларне. Пожалуйста, повторите попытку позже.';