<?php
// Heading
$_['heading_title'] = 'First Data EMEA Connect (с поддержкой 3DSecure)';

// Text
$_['text_extension'] = 'Расширения';
$_['text_success'] = 'Успех: вы изменили данные учетной записи First Data!';
$_['text_edit'] = 'Редактирование первых данных EMEA Connect (3DSecure включено)';
$_['text_notification_url'] = 'URL-адрес уведомления';
$_['text_live'] = 'Жить';
$_['text_demo'] = 'Демо';
$_['text_enabled'] = 'Включено';
$_['text_merchant_id'] = 'Идентификатор магазина';
$_['text_secret'] = 'Общий секрет';
$_['text_capture_ok'] = 'Захват прошел успешно';
$_['text_capture_ok_order'] = 'Захват прошел успешно, статус заказа изменен на успешный – урегулирован';
$_['text_void_ok'] = 'Аннулирование прошло успешно, статус заказа изменен на аннулирован';
$_['text_settle_auto'] = 'Распродажа';
$_['text_settle_delayed'] = 'Предварительная аутентификация';
$_['text_success_void'] = 'Транзакция аннулирована';
$_['text_success_capture'] = 'Транзакция зафиксирована';
$_['text_firstdata'] = '<img src="view/image/paying/firstdata.png" alt="Первые данные" title="Первые данные" style="border: 1px сплошной #EEEEEE;" />';
$_['text_payment_info'] = 'Информация об оплате';
$_['text_capture_status'] = 'Платеж получен';
$_['text_void_status'] = 'Платеж аннулирован';
$_['text_order_ref'] = 'Ссылка на заказ';
$_['text_order_total'] = 'Всего разрешено';
$_['text_total_captured'] = 'Всего захвачено';
$_['text_transactions'] = 'Транзакции';
$_['text_column_amount'] = 'Количество';
$_['text_column_type'] = 'Тип';
$_['text_column_date_added'] = 'Созданный';
$_['text_confirm_void'] = 'Вы уверены, что хотите аннулировать платеж?';
$_['text_confirm_capture'] = 'Вы уверены, что хотите получить платеж?';

// Entry
$_['entry_merchant_id'] = 'Идентификатор магазина';
$_['entry_secret'] = 'Общий секрет';
$_['entry_total'] = 'Общий';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_geo_zone'] = 'Геозона';
$_['entry_status'] = 'Статус';
$_['entry_debug'] = 'Ведение журнала отладки';
$_['entry_live_demo'] = 'Живой/Демо';
$_['entry_auto_settle'] = 'Тип расчета';
$_['entry_card_select'] = 'Выбрать карту';
$_['entry_tss_check'] = 'ТСС проверки';
$_['entry_live_url'] = 'URL-адрес живого подключения';
$_['entry_demo_url'] = 'URL демо-соединения';
$_['entry_status_success_settled'] = 'Успех - решено';
$_['entry_status_success_unsettled'] = 'Успех – не решено';
$_['entry_status_decline'] = 'Отклонить';
$_['entry_status_void'] = 'аннулирован';
$_['entry_enable_card_store'] = 'Включить токены хранения карты';

// Help
$_['help_total'] = 'Сумма заказа, которую должен достичь заказ, прежде чем этот способ оплаты станет активным.';
$_['help_notification'] = 'Вам необходимо предоставить этот URL-адрес компании First Data, чтобы получать уведомления о платежах.';
$_['help_debug'] = 'Включение отладки приведет к записи конфиденциальных данных в файл журнала. Вы всегда должны отключать, если не указано иное.';
$_['help_settle'] = 'Если вы используете предварительную аутентификацию, вы должны выполнить действие после аутентификации в течение 3–5 дней, иначе ваша транзакция будет отменена.'; 

// Tab
$_['tab_account'] = 'Информационный API';
$_['tab_order_status'] = 'Статус заказа';
$_['tab_payment'] = 'Настройки оплаты';
$_['tab_advanced'] = 'Передовой';

// Button
$_['button_capture'] = 'Захватывать';
$_['button_void'] = 'Пустота';

// Error
$_['error_merchant_id'] = 'Требуется идентификатор магазина.';
$_['error_secret'] = 'Требуется общий секрет.';
$_['error_live_url'] = 'Требуется активный URL.';
$_['error_demo_url'] = 'Требуется URL-адрес демо-версии.';
$_['error_data_missing'] = 'Данные отсутствуют';
$_['error_void_error'] = 'Невозможно аннулировать транзакцию';
$_['error_capture_error'] = 'Не удалось зафиксировать транзакцию';