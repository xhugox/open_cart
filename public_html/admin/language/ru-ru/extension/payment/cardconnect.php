<?php
// Heading
$_['heading_title'] = 'КардКоннект';

// Tab
$_['tab_settings'] = 'Настройки';
$_['tab_order_status'] = 'Статус заказа';

// Text
$_['text_extension'] = 'Расширения';
$_['text_success'] = 'Успех: Вы модифицировали платежный модуль CardConnect!';
$_['text_edit'] = 'Редактировать CardConnect';
$_['text_cardconnect'] = '<a href="http://www.cardconnect.com" target="_blank"><img src="view/image/paid/cardconnect.png" alt="CardConnect" title="CardConnect"></a>';
$_['text_payment'] = 'Оплата';
$_['text_refund'] = 'Возвращать деньги';
$_['text_authorize'] = 'Авторизовать';
$_['text_live'] = 'Жить';
$_['text_test'] = 'Тест';
$_['text_no_cron_time'] = 'Крон еще не выполнен';
$_['text_payment_info'] = 'Информация об оплате';
$_['text_payment_method'] = 'Способ оплаты';
$_['text_card'] = 'Карта';
$_['text_echeck'] = 'электронная проверка';
$_['text_reference'] = 'Ссылка';
$_['text_update'] = 'Обновлять';
$_['text_order_total'] = 'Сумма заказа';
$_['text_total_captured'] = 'Всего захвачено';
$_['text_capture_payment'] = 'Захват платежа';
$_['text_refund_payment'] = 'Возврат платежа';
$_['text_void'] = 'Пустота';
$_['text_transactions'] = 'Транзакции';
$_['text_column_type'] = 'Тип';
$_['text_column_reference'] = 'Ссылка';
$_['text_column_amount'] = 'Количество';
$_['text_column_status'] = 'Статус';
$_['text_column_date_modified'] = 'Дата изменения';
$_['text_column_date_added'] = 'Дата добавления';
$_['text_column_update'] = 'Обновлять';
$_['text_column_void'] = 'Пустота';
$_['text_confirm_capture'] = 'Вы уверены, что хотите получить платеж?';
$_['text_confirm_refund'] = 'Вы уверены, что хотите вернуть платеж?';
$_['text_inquire_success'] = 'Запрос прошел успешно';
$_['text_capture_success'] = 'Запрос на захват выполнен успешно';
$_['text_refund_success'] = 'Запрос на возврат выполнен успешно';
$_['text_void_success'] = 'Запрос на аннулирование прошел успешно';

// Entry
$_['entry_merchant_id'] = 'Идентификатор продавца';
$_['entry_api_username'] = 'Имя пользователя API';
$_['entry_api_password'] = 'Пароль API';
$_['entry_token'] = 'Секретный токен';
$_['entry_transaction'] = 'Сделка';
$_['entry_environment'] = 'Среда';
$_['entry_site'] = 'Сайт';
$_['entry_store_cards'] = 'Карты магазинов';
$_['entry_echeck'] = 'электронная проверка';
$_['entry_total'] = 'Общий';
$_['entry_geo_zone'] = 'Геозона';
$_['entry_status'] = 'Статус';
$_['entry_logging'] = 'Ведение журнала отладки';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_cron_url'] = 'URL-адрес задания Cron';
$_['entry_cron_time'] = 'Последний запуск задания Cron';
$_['entry_order_status_pending'] = 'В ожидании';
$_['entry_order_status_processing'] = 'Обработка';

// Help
$_['help_merchant_id'] = 'Ваш личный идентификатор продавца в учетной записи CardConnect.';
$_['help_api_username'] = 'Ваше имя пользователя для доступа к API CardConnect.';
$_['help_api_password'] = 'Ваш пароль для доступа к API CardConnect.';
$_['help_token'] = 'Введите случайный токен для безопасности или используйте сгенерированный.';
$_['help_transaction']              = 'Choose \'Payment\' to capture the payment immediately or \'Authorize\' to have to approve it.';
$_['help_site'] = 'Это определяет первую часть URL-адреса API. Меняйте только по указанию.';
$_['help_store_cards'] = 'Хотите ли вы хранить карты с использованием токенизации.';
$_['help_echeck'] = 'Хотите ли вы предложить возможность оплаты с помощью eCheck.';
$_['help_total'] = 'Сумма заказа должна быть достигнута, прежде чем этот способ оплаты станет активным. Должно быть значение без знака валюты.';
$_['help_logging'] = 'Включение отладки приведет к записи конфиденциальных данных в файл журнала. Вы всегда должны отключать, если не указано иное.';
$_['help_cron_url'] = 'Настройте задание cron для вызова этого URL-адреса, чтобы заказы автоматически обновлялись. Он предназначен для запуска через несколько часов после последнего захвата рабочего дня.';
$_['help_cron_time'] = 'Это последний раз, когда URL-адрес задания cron выполнялся.';
$_['help_order_status_pending'] = 'Статус заказа, когда заказ должен быть авторизован продавцом.';
$_['help_order_status_processing'] = 'Статус заказа, когда заказ фиксируется автоматически.';

// Button
$_['button_inquire_all'] = 'Запросить все';
$_['button_capture'] = 'Захватывать';
$_['button_refund'] = 'Возвращать деньги';
$_['button_void_all'] = 'Аннулировать все';
$_['button_inquire'] = 'Запросить';
$_['button_void'] = 'Пустота';

// Error
$_['error_permission'] = 'Внимание: у вас нет разрешения на изменение платежной карты CardConnect!';
$_['error_merchant_id'] = 'Требуется идентификатор продавца!';
$_['error_api_username'] = 'Требуется имя пользователя API!';
$_['error_api_password'] = 'Требуется пароль API!';
$_['error_token'] = 'Требуется секретный токен!';
$_['error_site'] = 'Сайт обязателен!';
$_['error_amount_zero'] = 'Сумма должна быть больше нуля!';
$_['error_no_order'] = 'Нет соответствующей информации о заказе!';
$_['error_invalid_response'] = 'Получен неверный ответ!';
$_['error_data_missing'] = 'Отсутствуют данные!';
$_['error_not_enabled'] = 'Модуль не активирован!';