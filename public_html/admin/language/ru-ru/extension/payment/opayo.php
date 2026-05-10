<?php
// Heading
$_['heading_title'] = 'Хорошо';

// Text
$_['text_opayo'] = '<img src="view/image/payo/opayo.png" alt="Opayo" title="Opayo" />';
$_['text_extensions'] = 'Расширения';
$_['text_edit'] = 'Править Опайо';
$_['text_tab_general'] = 'Общий';
$_['text_tab_cron'] = 'Крон';
$_['text_test'] = 'Тест';
$_['text_live'] = 'Жить';
$_['text_defered'] = 'Отложено';
$_['text_authenticate'] = 'Аутентификация';
$_['text_payment'] = 'Оплата';
$_['text_payment_info'] = 'Информация об оплате';
$_['text_release_status'] = 'Платеж выпущен';
$_['text_void_status'] = 'Платеж аннулирован';
$_['text_rebate_status'] = 'Платеж возвращен';
$_['text_order_ref'] = 'Ссылка на заказ';
$_['text_order_total'] = 'Всего разрешено';
$_['text_total_released'] = 'Всего выпущено';
$_['text_transactions'] = 'Транзакции';
$_['text_column_amount'] = 'Количество';
$_['text_column_type'] = 'Тип';
$_['text_column_date_added'] = 'Созданный';
$_['text_confirm_void'] = 'Вы уверены, что хотите аннулировать платеж?';
$_['text_confirm_release'] = 'Вы уверены, что хотите отменить платеж?';
$_['text_confirm_rebate'] = 'Вы уверены, что хотите вернуть платеж?';

// Entry
$_['entry_vendor'] = 'Продавец';
$_['entry_environment'] = 'Среда';
$_['entry_transaction_method'] = 'Метод транзакции';
$_['entry_total'] = 'Общий';
$_['entry_order_status'] = 'Статус заказа';
$_['entry_geo_zone'] = 'Геозона';
$_['entry_status'] = 'Статус';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_debug'] = 'Ведение журнала отладки';
$_['entry_card_save'] = 'Карты магазинов';
$_['entry_cron_token'] = 'Секретный токен';
$_['entry_cron_url'] = 'URL-адрес';
$_['entry_cron_last_run'] = 'Время последнего запуска:';

// Help
$_['help_total'] = 'Сумма заказа должна быть достигнута, прежде чем этот способ оплаты станет активным.';
$_['help_debug'] = 'Включение отладки приведет к записи конфиденциальных данных в файл журнала. Вы всегда должны отключать, если не указано иное.';
$_['help_transaction_method'] = 'Для метода транзакции ДОЛЖНО быть установлено значение «Оплата», чтобы разрешить оплату подписки.';
$_['help_card_save'] = 'Чтобы покупатель мог сохранить данные карты для последующих платежей, необходимо оформить подписку на MID TOKEN. Вам нужно будет связаться со службой поддержки Opayo, чтобы обсудить включение системы токенов для вашей учетной записи.';
$_['help_cron_token'] = 'Сделайте это длинным и трудным для угадывания.';
$_['help_cron_url'] = 'Установите cron для вызова этого URL.';

// Button
$_['button_release'] = 'Выпускать';
$_['button_rebate'] = 'Скидка/возврат';
$_['button_void'] = 'Пустота';
$_['button_enable_recurring'] = 'Включить повторяющееся';
$_['button_disable_recurring'] = 'Отключить повторяющееся';

// Success
$_['success_save'] = 'Успех: вы изменили Opayo!';
$_['success_release_ok'] = 'Успех: Релиз прошел успешно!';
$_['success_release_ok_order'] = 'Успех: выпуск прошел успешно, статус заказа изменен на успешный – решено!';
$_['success_rebate_ok'] = 'Успех: скидка прошла успешно!';
$_['success_rebate_ok_order'] = 'Успех: скидка прошла успешно, статус заказа изменен на скидку!';
$_['success_void_ok'] = 'Успех: аннулирование прошло успешно, статус заказа изменен на аннулирован!';
$_['success_enable_recurring'] = 'Успех: регулярный платеж включен!';
$_['success_disable_recurring'] = 'Успех: регулярный платеж отключен!';

// Error
$_['error_warning'] = 'Внимание: пожалуйста, внимательно проверьте форму на наличие ошибок!';
$_['error_permission'] = 'Внимание: у вас нет разрешения на изменение оплаты Opayo!';
$_['error_vendor'] = 'Требуется идентификатор поставщика!';
