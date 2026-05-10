<?php
// Heading
$_['heading_title'] = 'API веб-службы First Data EMEA';

// Text
$_['text_firstdata_remote'] = '<img src="view/image/paying/firstdata.png" alt="Первые данные" title="Первые данные" style="border: 1px сплошной #EEEEEE;" />';
$_['text_extension'] = 'Расширения';
$_['text_success'] = 'Успех: вы изменили данные учетной записи First Data!';
$_['text_edit'] = 'Изменить API веб-службы First Data EMEA';
$_['text_card_type'] = 'Тип карты';
$_['text_enabled'] = 'Включено';
$_['text_merchant_id'] = 'Идентификатор магазина';
$_['text_subaccount'] = 'субсчет';
$_['text_user_id'] = 'ID пользователя';
$_['text_capture_ok'] = 'Захват прошел успешно';
$_['text_capture_ok_order'] = 'Захват прошел успешно, статус заказа изменен на успешный – урегулирован';
$_['text_refund_ok'] = 'Возврат прошел успешно';
$_['text_refund_ok_order'] = 'Возврат прошел успешно, статус заказа изменен на «возвращен».';
$_['text_void_ok'] = 'Аннулирование прошло успешно, статус заказа изменен на аннулирован';
$_['text_settle_auto'] = 'Распродажа';
$_['text_settle_delayed'] = 'Предварительная аутентификация';
$_['text_mastercard'] = 'Мастеркард';
$_['text_visa'] = 'Виза';
$_['text_diners'] = 'Посетители';
$_['text_amex'] = 'Американ Экспресс';
$_['text_maestro'] = 'Маэстро';
$_['text_payment_info'] = 'Информация об оплате';
$_['text_capture_status'] = 'Платеж получен';
$_['text_void_status'] = 'Платеж аннулирован';
$_['text_refund_status'] = 'Платеж возвращен';
$_['text_order_ref'] = 'Ссылка на заказ';
$_['text_order_total'] = 'Всего разрешено';
$_['text_total_captured'] = 'Всего захвачено';
$_['text_transactions'] = 'Транзакции';
$_['text_column_amount'] = 'Количество';
$_['text_column_type'] = 'Тип';
$_['text_column_date_added'] = 'Созданный';
$_['text_confirm_void'] = 'Вы уверены, что хотите аннулировать платеж?';
$_['text_confirm_capture'] = 'Вы уверены, что хотите получить платеж?';
$_['text_confirm_refund'] = 'Вы уверены, что хотите вернуть платеж?';
$_['text_void'] = 'Пустота';
$_['text_payment'] = 'Оплата';
$_['text_refund']                    = "Refund";

// Entry
$_['entry_certificate_path'] = 'Путь сертификата';
$_['entry_certificate_key_path'] = 'Путь к секретному ключу';
$_['entry_certificate_key_pw'] = 'Пароль закрытого ключа';
$_['entry_certificate_ca_path'] = 'Путь ЦС';
$_['entry_merchant_id'] = 'Идентификатор магазина';
$_['entry_user_id'] = 'ID пользователя';
$_['entry_password'] = 'Пароль';
$_['entry_total'] = 'Общий';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_geo_zone'] = 'Геозона';
$_['entry_status'] = 'Статус';
$_['entry_debug'] = 'Ведение журнала отладки';
$_['entry_auto_settle'] = 'Тип расчета';
$_['entry_status_success_settled'] = 'Успех - решено';
$_['entry_status_success_unsettled'] = 'Успех – не решено';
$_['entry_status_decline'] = 'Отклонить';
$_['entry_status_void'] = 'аннулирован';
$_['entry_status_refund'] = 'Возвращено';
$_['entry_enable_card_store'] = 'Включить токены хранения карты';
$_['entry_cards_accepted'] = 'Принимаемые типы карт';

// Help
$_['help_total'] = 'Сумма заказа, которую должен достичь заказ, прежде чем этот способ оплаты станет активным.';
$_['help_certificate'] = 'Сертификаты и закрытые ключи должны храниться за пределами общедоступных веб-папок.';
$_['help_card_select'] = 'Попросите пользователя выбрать тип карты, прежде чем он будет перенаправлен.';
$_['help_notification'] = 'Вам необходимо предоставить этот URL-адрес компании First Data, чтобы получать уведомления о платежах.';
$_['help_debug'] = 'Включение отладки приведет к записи конфиденциальных данных в файл журнала. Вы всегда должны отключать эту функцию, если не указано иное.';
$_['help_settle'] = 'Если вы используете предварительную аутентификацию, вы должны выполнить действие после аутентификации в течение 3–5 дней, иначе ваша транзакция будет отменена.';

// Tab
$_['tab_account'] = 'Информационный API';
$_['tab_order_status'] = 'Статус заказа';
$_['tab_payment'] = 'Настройки оплаты';

// Button
$_['button_capture'] = 'Захватывать';
$_['button_refund'] = 'Возвращать деньги';
$_['button_void'] = 'Пустота';

// Error
$_['error_merchant_id'] = 'Требуется идентификатор магазина.';
$_['error_user_id'] = 'Требуется идентификатор пользователя.';
$_['error_password'] = 'Требуется пароль';
$_['error_certificate'] = 'Укажите путь к сертификату.';
$_['error_key'] = 'Требуется ключ сертификата.';
$_['error_key_pw'] = 'Требуется пароль ключа сертификата.';
$_['error_ca'] = 'Требуется центр сертификации (CA).';