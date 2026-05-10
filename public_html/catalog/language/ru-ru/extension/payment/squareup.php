<?php
// Text
$_['text_new_card'] = '+ Добавить новую карту';
$_['text_loading'] = 'Загрузка... Пожалуйста, подождите...';
$_['text_card_details'] = 'Детали карты';
$_['text_saved_card'] = 'Использовать сохраненную карту:';
$_['text_card_ends_in'] = 'Оплатите существующей картой %s, срок действия которой заканчивается XXXX XXXX XXXX %s.';
$_['text_card_number'] = 'Номер карты:';
$_['text_card_expiry'] = 'Срок действия карты (ММ/ГГ):';
$_['text_card_cvc'] = 'Код безопасности карты (CVC):';
$_['text_card_zip'] = 'Почтовый индекс карты:';
$_['text_card_save'] = 'Сохранить карту для будущего использования?';
$_['text_trial'] = '%s каждые %s %s для платежей %s, затем';
$_['text_recurring'] = '%s каждые %s %s';
$_['text_length'] = 'для %s платежей';
$_['text_cron_subject'] = 'Краткое описание вакансии Square CRON';
$_['text_cron_message'] = 'Вот список всех задач CRON, выполняемых вашим расширением Square:';
$_['text_squareup_profile_suspended'] = 'Ваши регулярные платежи были приостановлены. Пожалуйста, свяжитесь с нами для получения более подробной информации.';
$_['text_squareup_trial_expired'] = 'Ваш пробный период истек.';
$_['text_squareup_recurring_expired'] = 'Срок действия ваших регулярных платежей истек. Это был ваш последний платеж.';
$_['text_cron_summary_token_heading'] = 'Обновление токена доступа:';
$_['text_cron_summary_token_updated'] = 'Токен доступа успешно обновлен!';
$_['text_cron_summary_error_heading'] = 'Ошибки транзакции:';
$_['text_cron_summary_fail_heading'] = 'Неудачные транзакции (профили заблокированы):';
$_['text_cron_summary_success_heading'] = 'Успешные сделки:';
$_['text_cron_fail_charge'] = 'Профилю <strong>#%s</strong> не удалось списать средства на сумму <strong>%s</strong>.';
$_['text_cron_success_charge'] = 'Профилю <strong>#%s</strong> было предъявлено обвинение в <strong>%s</strong>.';
$_['text_card_placeholder'] = 'ХХХХ ХХХХ ХХХХ ХХХ';
$_['text_cvv'] = 'CVV';
$_['text_expiry'] = 'ММ/ГГ';
$_['text_default_squareup_name'] = 'Кредитная/дебетовая карта';
$_['text_token_issue_customer_error'] = 'В нашей платежной системе произошел технический сбой. Пожалуйста, повторите попытку позже.';
$_['text_token_revoked_subject'] = 'Ваш токен доступа Square был отозван!';
$_['text_token_revoked_message']        = "The Square payment extension's access to your Square account has been revoked through the Square Dashboard. You need to verify your application credentials in the extension settings and connect again.";
$_['text_token_expired_subject'] = 'Срок действия вашего токена доступа Square истек!';
$_['text_token_expired_message']        = "The Square payment extension's access token connecting it to your Square account has expired. You need to verify your application credentials and CRON job in the extension settings and connect again.";

// Error
$_['error_browser_not_supported'] = 'Ошибка: Платежная система больше не поддерживает ваш веб-браузер. Пожалуйста, обновите или используйте другой.';
$_['error_card_invalid'] = 'Ошибка: Карта недействительна!';
$_['error_squareup_cron_token'] = 'Ошибка: токен доступа не удалось обновить. Пожалуйста, подключите расширение Square Payment через панель администратора OpenCart.';

// Warning
$_['warning_test_mode'] = 'Внимание: включен режим песочницы! Транзакции будут считаться выполненными, но никакие платежи производиться не будут.';

// Statuses
$_['squareup_status_comment_authorized'] = 'Транзакция по карте была авторизована, но еще не зафиксирована.';
$_['squareup_status_comment_captured'] = 'Транзакция по карте была авторизована и впоследствии зафиксирована (т. е. завершена).';
$_['squareup_status_comment_voided'] = 'Транзакция по карте была авторизована и впоследствии аннулирована (т. е. отменена).';
$_['squareup_status_comment_failed'] = 'Транзакция по карте не удалась.';

// Override errors
$_['squareup_override_error_billing_address.country'] = 'Страна платежного адреса недействительна. Пожалуйста, измените его и повторите попытку.';
$_['squareup_override_error_shipping_address.country'] = 'Страна адреса доставки недействительна. Пожалуйста, измените его и повторите попытку.';
$_['squareup_override_error_email_address'] = 'Ваш адрес электронной почты клиента недействителен. Пожалуйста, измените его и повторите попытку.';
$_['squareup_override_error_phone_number'] = 'Ваш номер телефона клиента недействителен. Пожалуйста, измените его и повторите попытку.';
$_['squareup_error_field'] = 'Поле: %s';