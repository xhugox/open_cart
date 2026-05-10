<?php
//Headings
$_['heading_title'] = 'Amazon Pay и вход в систему с помощью Amazon';

//Text
$_['text_success'] = 'Обновлен модуль Amazon Pay и Login with Amazon.';
$_['text_ipn_url']					= 'Cron Job\'s URL';
$_['text_ipn_token'] = 'Секретный токен';
$_['text_us'] = 'Американский английский';
$_['text_de'] = 'немецкий';
$_['text_uk'] = 'Английский';
$_['text_fr'] = 'Французский';
$_['text_it'] = 'итальянский';
$_['text_es'] = 'испанский';
$_['text_us_region'] = 'Соединенные Штаты';
$_['text_eu_region'] = 'Еврорегион';
$_['text_uk_region'] = 'Великобритания';
$_['text_live'] = 'Жить';
$_['text_sandbox'] = 'Песочница';
$_['text_auth'] = 'Авторизация';
$_['text_payment'] = 'Оплата';
$_['text_account'] = 'Счет';
$_['text_guest'] = 'Гость';
$_['text_no_capture'] = '--- Нет автоматического захвата ---';
$_['text_all_geo_zones'] = 'Все географические зоны';
$_['text_button_settings'] = 'Настройки кнопки оформления заказа';
$_['text_colour'] = 'Цвет';
$_['text_orange'] = 'Апельсин';
$_['text_tan'] = 'Тан';
$_['text_white'] = 'Белый';
$_['text_light'] = 'Свет';
$_['text_dark'] = 'Темный';
$_['text_size'] = 'Размер';
$_['text_medium'] = 'Середина';
$_['text_large'] = 'Большой';
$_['text_x_large'] = 'Очень большой';
$_['text_background'] = 'Фон';
$_['text_edit'] = 'Отредактируйте Amazon Pay и войдите в систему с помощью Amazon';
$_['text_amazon_login_pay'] = '<a href="https://pay.amazon.com/help/201828820" target="_blank" title="Зарегистрироваться в Amazon Pay"><img src="view/image/paid/amazon_lpa.png" alt="Amazon Pay и войти с помощью Amazon" title="Amazon Pay и войти с Amazon" style="border: 1px сплошной #EEEEEE;" /></а>';
$_['text_amazon_join'] = '<a href="https://pay.amazon.com/help/201828820" target="_blank" title="Регистрация в Amazon Pay"><u>Зарегистрируйтесь в Amazon Pay</u></a>';
$_['text_payment_info'] = 'Информация об оплате';
$_['text_capture_ok'] = 'Захват прошел успешно';
$_['text_capture_ok_order'] = 'Захват прошел успешно, авторизация полностью захвачена';
$_['text_refund_ok'] = 'Возврат был успешно запрошен';
$_['text_refund_ok_order'] = 'Возврат успешно запрошен, сумма полностью возвращена';
$_['text_cancel_ok'] = 'Отмена прошла успешно, статус заказа изменен на «Отменен».';
$_['text_capture_status'] = 'Платеж получен';
$_['text_cancel_status'] = 'Платеж отменен';
$_['text_refund_status'] = 'Платеж возвращен';
$_['text_order_ref'] = 'Ссылка на заказ';
$_['text_order_total'] = 'Всего разрешено';
$_['text_total_captured'] = 'Всего захвачено';
$_['text_transactions'] = 'Транзакции';
$_['text_column_authorization_id'] = 'Идентификатор авторизации Amazon';
$_['text_column_capture_id'] = 'Идентификатор захвата Amazon';
$_['text_column_refund_id'] = 'Идентификатор возврата Amazon';
$_['text_column_amount'] = 'Количество';
$_['text_column_type'] = 'Тип';
$_['text_column_status'] = 'Статус';
$_['text_column_date_added'] = 'Дата добавления';
$_['text_confirm_cancel'] = 'Вы уверены, что хотите отменить платеж?';
$_['text_confirm_capture'] = 'Вы уверены, что хотите получить платеж?';
$_['text_confirm_refund'] = 'Вы уверены, что хотите вернуть платеж?';
$_['text_minimum_total'] = 'Минимальная сумма заказа';
$_['text_geo_zone'] = 'Геозона';
$_['text_buyer_multi_currency'] = 'Мультивалютная функция';
$_['text_status'] = 'Статус';
$_['text_declined_codes'] = 'Коды отклонения теста';
$_['text_sort_order'] = 'Порядок сортировки';
$_['text_amazon_invalid'] = 'Неверный метод оплаты';
$_['text_amazon_rejected'] = 'AmazonОтклонено';
$_['text_amazon_timeout'] = 'ТранзакцияTimedOut';
$_['text_amazon_no_declined'] = '--- Нет автоматического отклонения авторизации ---';
$_['text_amazon_signup'] = 'Зарегистрируйтесь в Amazon Pay';
$_['text_credentials'] = 'Пожалуйста, вставьте сюда свои ключи (в формате JSON)';
$_['text_validate_credentials'] = 'Проверка и использование учетных данных';
$_['text_extension'] = 'Расширения';
$_['text_info_ssl'] = '<strong>Важно!</strong> SSL (https://) является обязательным требованием и должен быть включен на вашем веб-сайте, чтобы кнопки Amazon Pay и Login with Amazon работали.';
$_['text_info_buyer_multi_currencies'] = 'Это расширение поддерживает функцию мультивалютности. Если вы хотите использовать его, убедитесь, что вы включили хотя бы одну из <a href="https://pay.amazon.co.uk/help/5BDCWHCUC27485L"><b>валют, поддерживаемых Amazon Pay</b></a> в настройках интернет-магазина <b><a href="index.php?route=localisation/currency&user_token=%s">(%s > %s > %s )</b></a>, а затем включите <b>функцию мультивалютности</b>';

// Columns
$_['column_status'] = 'Статус';

//entry
$_['entry_merchant_id'] = 'Идентификатор продавца';
$_['entry_access_key'] = 'Ключ доступа';
$_['entry_access_secret'] = 'Секретный ключ';
$_['entry_client_id'] = 'Идентификатор клиента';
$_['entry_client_secret'] = 'Секрет клиента';
$_['entry_language'] = 'Язык по умолчанию';
$_['entry_login_pay_test'] = 'Тестовый режим';
$_['entry_login_pay_mode'] = 'Режим оплаты';
$_['entry_checkout'] = 'Режим оформления заказа';
$_['entry_payment_region'] = 'Регион оплаты';
$_['entry_capture_status'] = 'Статус автоматического захвата';
$_['entry_pending_status'] = 'Статус отложенного ордера';
$_['entry_capture_oc_status'] = 'Зафиксировать статус заказа';
$_['entry_ipn_url']					= 'IPN\'s URL';
$_['entry_ipn_token'] = 'Секретный токен';
$_['entry_debug'] = 'Ведение журнала отладки';

// Help
$_['help_pay_mode'] = 'Выберите «Платеж», если вы хотите, чтобы платеж фиксировался автоматически, или «Авторизация», чтобы фиксировать его вручную.';
$_['help_checkout'] = 'Должна ли кнопка оплаты также войти в систему клиента';
$_['help_capture_status'] = 'Выберите статус заказа, при котором будет автоматически зафиксирован авторизованный платеж.';
$_['help_capture_oc_status'] = 'Выберите статус заказа, который он получит после его регистрации в Amazon Seller Central или с помощью функции захвата в администраторе OpenCart > %s > %s > %s.';
$_['help_ipn_url'] = 'Установите этот URL-адрес в качестве URL-адреса продавца в Amazon Seller Central.';
$_['help_ipn_token'] = 'Сделайте это длинным и трудным для угадывания. Результирующий URL-адрес IPN не должен содержать более 150 символов.';
$_['help_minimum_total'] = 'Это должно быть выше нуля';
$_['help_debug'] = 'Включение отладки приведет к записи конфиденциальных данных в файл журнала. Вы всегда должны отключать, если не указано иное.';
$_['help_declined_codes'] = 'Это только для целей тестирования';
$_['help_buyer_multi_currency'] = 'Включите эту опцию, если вы хотите, чтобы покупатель совершал покупки в любой из поддерживаемых Amazon Pay валют, доступных в вашем интернет-магазине: %s';
$_['help_buyer_multi_currency_no_available_currency'] = 'В вашем интернет-магазине нет доступных <a href="https://pay.amazon.co.uk/help/5BDCWHCUC27485L"><b>валют, поддерживаемых Amazon Pay</b></a>. Добавьте/включите такие валюты, чтобы использовать эту функцию.';

// Order Info
$_['tab_order_adjustment'] = 'Корректировка заказа';

// Errors
$_['error_permission'] = 'У вас нет прав на изменение этого модуля!';
$_['error_merchant_id'] = 'Требуется идентификатор продавца.';
$_['error_access_key'] = 'Требуется ключ доступа';
$_['error_access_secret'] = 'Требуется секретный ключ';
$_['error_client_id'] = 'Требуется идентификатор клиента.';
$_['error_client_secret'] = 'Требуется клиентский ключ';
$_['error_pay_mode'] = 'Оплата доступна только для торговой площадки США.';
$_['error_minimum_total'] = 'Сумма минимального заказа должна быть больше нуля.';
$_['error_curreny'] = 'В вашем магазине должна быть установлена ​​и включена валюта %s.';
$_['error_upload'] = 'Загрузка не удалась';
$_['error_data_missing'] = 'Необходимые данные отсутствуют';
$_['error_credentials'] = 'Пожалуйста, введите ключи в допустимом формате JSON.';
$_['error_no_supported_currencies'] = 'В вашем магазине нет поддерживаемых валют. Добавьте/включите поддержку нескольких валют покупателя, чтобы использовать эту функцию.';

// Buttons
$_['button_capture'] = 'Захватывать';
$_['button_refund'] = 'Возвращать деньги';
$_['button_cancel'] = 'Отмена';
