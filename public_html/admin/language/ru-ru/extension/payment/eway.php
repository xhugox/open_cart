<?php
// Heading
$_['heading_title'] = 'eWAY Оплата';

// Text
$_['text_extension'] = 'Расширения';
$_['text_success'] = 'Успех: вы изменили свои данные eWAY!';
$_['text_edit'] = 'Редактировать';
$_['text_eway'] = '<a target="_BLANK" href="http://www.eway.com.au/"><img src="view/image/paying/eway.png" alt="eWAY" title="eWAY" style="border: 1px сплошной #EEEEEE;" /></а>';
$_['text_authorisation'] = 'Авторизация';
$_['text_sale'] = 'Распродажа';
$_['text_transparent'] = 'Прозрачный редирект (форма оплаты на сайте)';
$_['text_iframe'] = 'IFrame (форма оплаты в окне)';
$_['text_connect_eway'] = 'eWAY помогает предприятиям безопасно обрабатывать все основные кредитные карты благодаря встроенной системе предотвращения мошенничества, круглосуточной технической поддержке и многому другому. <a target="_blank" href="https://myeway.force.com/success/accelerator-signup?pid=4382&pa=0012000000ivcWf">Нажмите здесь</a>';
$_['text_eway_image'] = '<a target="_blank" href="https://myeway.force.com/success/accelerator-signup?pid=4382&pa=0012000000ivcWf"><img src="view/image/pay/eway_connect.png" alt="eWAY" title="eWAY" class="img-fluid" /></a>';

// Entry
$_['entry_paymode'] = 'Режим оплаты';
$_['entry_test'] = 'Тестовый режим';
$_['entry_order_status'] = 'Статус заказа';
$_['entry_order_status_refund'] = 'Статус возвращенного заказа';
$_['entry_order_status_auth'] = 'Статус авторизованного заказа';
$_['entry_order_status_fraud'] = 'Статус заказа «Подозрение на мошенничество»';
$_['entry_status'] = 'Статус';
$_['entry_username'] = 'API-ключ eWAY';
$_['entry_password'] = 'пароль eWAY';
$_['entry_payment_type'] = 'Тип оплаты';
$_['entry_geo_zone'] = 'Геозона';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_transaction_method'] = 'Метод транзакции';

// Error
$_['error_permission'] = 'Внимание: у вас нет разрешения на изменение платежного модуля eWAY.';
$_['error_username'] = 'Требуется API-ключ eWAY!';
$_['error_password'] = 'Требуется пароль eWAY!';
$_['error_payment_type'] = 'Требуется хотя бы один тип оплаты!';

// Help hints
$_['help_testmode'] = 'Установите значение «Да», чтобы использовать песочницу eWAY.';
$_['help_username'] = 'Ваш API-ключ eWAY из вашей учетной записи MYeWAY.';
$_['help_password'] = 'Ваш пароль eWAY API от вашей учетной записи MYeWAY.';
$_['help_transaction_method'] = 'Авторизация доступна только для австралийских банков.';

// Order page - payment tab
$_['text_payment_info'] = 'Информация об оплате';
$_['text_order_total'] = 'Всего разрешено';
$_['text_transactions'] = 'Транзакции';
$_['text_column_transactionid'] = 'Идентификатор транзакции eWAY';
$_['text_column_amount'] = 'Количество';
$_['text_column_type'] = 'Тип';
$_['text_column_created'] = 'Созданный';
$_['text_total_captured'] = 'Всего захвачено';
$_['text_capture_status'] = 'Платеж получен';
$_['text_void_status'] = 'Платеж аннулирован';
$_['text_refund_status'] = 'Платеж возвращен';
$_['text_total_refunded'] = 'Всего возвращено';
$_['text_refund_success'] = 'Возврат удался!';
$_['text_capture_success'] = 'Захват удался!';
$_['text_refund_failed'] = 'Возврат не удался:';
$_['text_capture_failed'] = 'Захватить не удалось:';
$_['text_unknown_failure'] = 'Неверный заказ или сумма.';
$_['text_refund'] = 'Возвращать деньги';

$_['text_confirm_capture'] = 'Вы уверены, что хотите получить платеж?';
$_['text_confirm_release'] = 'Вы уверены, что хотите отменить платеж?';
$_['text_confirm_refund'] = 'Вы уверены, что хотите вернуть платеж?';

$_['text_empty_refund'] = 'Пожалуйста, введите сумму для возврата';
$_['text_empty_capture'] = 'Пожалуйста, введите сумму для получения';

$_['btn_refund'] = 'Возвращать деньги';
$_['btn_capture'] = 'Захватывать';

// Validation Error codes
$_['text_card_message_V6000'] = 'Неопределенная ошибка проверки';
$_['text_card_message_V6001'] = 'Неверный IP-адрес клиента';
$_['text_card_message_V6002'] = 'Неверный идентификатор устройства';
$_['text_card_message_V6011'] = 'Неверная сумма';
$_['text_card_message_V6012'] = 'Неверное описание счета';
$_['text_card_message_V6013'] = 'Неверный номер счета';
$_['text_card_message_V6014'] = 'Неверная ссылка на счет-фактуру';
$_['text_card_message_V6015'] = 'Неверный код валюты';
$_['text_card_message_V6016'] = 'Требуется оплата';
$_['text_card_message_V6017'] = 'Требуется код валюты платежа';
$_['text_card_message_V6018'] = 'Неизвестный код валюты платежа';
$_['text_card_message_V6021'] = 'Требуется имя владельца карты';
$_['text_card_message_V6022'] = 'Требуется номер карты';
$_['text_card_message_V6023'] = 'Требуется CVN';
$_['text_card_message_V6031'] = 'Неверный номер карты';
$_['text_card_message_V6032'] = 'Неверный CVN';
$_['text_card_message_V6033'] = 'Неверная дата истечения срока действия';
$_['text_card_message_V6034'] = 'Неверный номер выпуска';
$_['text_card_message_V6035'] = 'Неверная дата начала';
$_['text_card_message_V6036'] = 'Неверный месяц';
$_['text_card_message_V6037'] = 'Неверный год';
$_['text_card_message_V6040'] = 'Неверный идентификатор клиента токена';
$_['text_card_message_V6041'] = 'Требуется клиент';
$_['text_card_message_V6042'] = 'Требуется имя клиента';
$_['text_card_message_V6043'] = 'Требуется фамилия клиента';
$_['text_card_message_V6044'] = 'Требуется код страны клиента';
$_['text_card_message_V6045'] = 'Требуется название клиента';
$_['text_card_message_V6046'] = 'Требуется идентификатор клиента токена';
$_['text_card_message_V6047'] = 'Требуется URL-адрес перенаправления';
$_['text_card_message_V6051'] = 'Неверное имя';
$_['text_card_message_V6052'] = 'Неверная фамилия';
$_['text_card_message_V6053'] = 'Неверный код страны';
$_['text_card_message_V6054'] = 'Неверный адрес электронной почты';
$_['text_card_message_V6055'] = 'Неверный телефон';
$_['text_card_message_V6056'] = 'Неверный мобильный телефон';
$_['text_card_message_V6057'] = 'Неверный факс';
$_['text_card_message_V6058'] = 'Неверный заголовок';
$_['text_card_message_V6059'] = 'URL-адрес перенаправления недействителен';
$_['text_card_message_V6060'] = 'URL-адрес перенаправления недействителен';
$_['text_card_message_V6061'] = 'Неверная ссылка';
$_['text_card_message_V6062'] = 'Неверное название компании';
$_['text_card_message_V6063'] = 'Неверное описание вакансии';
$_['text_card_message_V6064'] = 'Неверная улица 1';
$_['text_card_message_V6065'] = 'Неверная улица 2';
$_['text_card_message_V6066'] = 'Неверный город';
$_['text_card_message_V6067'] = 'Недопустимое состояние';
$_['text_card_message_V6068'] = 'Неверный почтовый индекс';
$_['text_card_message_V6069'] = 'Неверный адрес электронной почты';
$_['text_card_message_V6070'] = 'Неверный телефон';
$_['text_card_message_V6071'] = 'Неверный мобильный телефон';
$_['text_card_message_V6072'] = 'Неверные комментарии';
$_['text_card_message_V6073'] = 'Неверный факс';
$_['text_card_message_V6074'] = 'Неверный URL';
$_['text_card_message_V6075'] = 'Неверный адрес доставки, имя.';
$_['text_card_message_V6076'] = 'Неверный адрес доставки, фамилия.';
$_['text_card_message_V6077'] = 'Неверный адрес доставки Street1';
$_['text_card_message_V6078'] = 'Неверный адрес доставки Street2';
$_['text_card_message_V6079'] = 'Неверный адрес доставки Город';
$_['text_card_message_V6080'] = 'Неверный адрес доставки';
$_['text_card_message_V6081'] = 'Неверный почтовый индекс адреса доставки';
$_['text_card_message_V6082'] = 'Неверный адрес электронной почты для доставки';
$_['text_card_message_V6083'] = 'Неверный адрес доставки Телефон';
$_['text_card_message_V6084'] = 'Неверный адрес доставки. Страна.';
$_['text_card_message_V6091'] = 'Неизвестный код страны';
$_['text_card_message_V6100'] = 'Неверное имя карты';
$_['text_card_message_V6101'] = 'Неверный месяц истечения срока действия карты';
$_['text_card_message_V6102'] = 'Неверный год истечения срока действия карты';
$_['text_card_message_V6103'] = 'Неверный месяц начала карты';
$_['text_card_message_V6104'] = 'Неверный год начала карты';
$_['text_card_message_V6105'] = 'Неверный номер выпуска карты';
$_['text_card_message_V6106'] = 'Неверный CVN карты';
$_['text_card_message_V6107'] = 'Неверный код доступа';
$_['text_card_message_V6108'] = 'Неверный адрес хоста клиента';
$_['text_card_message_V6109'] = 'Неверный пользовательский агент';
$_['text_card_message_V6110'] = 'Неверный номер карты';
$_['text_card_message_V6111'] = 'Несанкционированный доступ к API, учетная запись не сертифицирована PCI';
$_['text_card_message_V6112'] = 'Избыточные данные карты, кроме года и месяца истечения срока действия.';
$_['text_card_message_V6113'] = 'Недействительная транзакция для возврата';
$_['text_card_message_V6114'] = 'Ошибка проверки шлюза';
$_['text_card_message_V6115'] = 'Неверный DirectRefundRequest, идентификатор транзакции';
$_['text_card_message_V6116'] = 'Неверные данные карты в исходном идентификаторе транзакции.';
$_['text_card_message_V6124'] = 'Недопустимые позиции. Позиции предоставлены, однако итоговые суммы не соответствуют полю TotalAmount.';
$_['text_card_message_V6125'] = 'Выбранный тип платежа не активирован';
$_['text_card_message_V6126'] = 'Неверный номер зашифрованной карты, расшифровка не удалась.';
$_['text_card_message_V6127'] = 'Неверный зашифрованный cvn, расшифровка не удалась.';
$_['text_card_message_V6128'] = 'Неверный метод для типа платежа';
$_['text_card_message_V6129'] = 'Транзакция не была авторизована для захвата/отмены';
$_['text_card_message_V6130'] = 'Общая ошибка информации о клиенте';
$_['text_card_message_V6131'] = 'Общая ошибка информации о доставке';
$_['text_card_message_V6132'] = 'Транзакция уже завершена или аннулирована, операция не разрешена';
$_['text_card_message_V6133'] = 'Оформление заказа недоступно для данного типа оплаты.';
$_['text_card_message_V6134'] = 'Неверный идентификатор транзакции аутентификации для захвата/аннулирования';
$_['text_card_message_V6135'] = 'Возврат средств при обработке ошибки PayPal';
$_['text_card_message_V6140'] = 'Аккаунт продавца заблокирован';
$_['text_card_message_V6141'] = 'Неверные данные учетной записи PayPal или подпись API.';
$_['text_card_message_V6142'] = 'Авторизация недоступна для банка/филиала';
$_['text_card_message_V6150'] = 'Неверная сумма возврата';
$_['text_card_message_V6151'] = 'Сумма возврата превышает исходную транзакцию';
$_['text_card_message_D4406'] = 'Неизвестная ошибка';
$_['text_card_message_S5010'] = 'Неизвестная ошибка';