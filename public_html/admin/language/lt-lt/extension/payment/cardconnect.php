<?php
// Heading
$_['heading_title'] = 'CardConnect';

// Tab
$_['tab_settings'] = 'Nustatymai';
$_['tab_order_status'] = 'Užsakymo būsena';

// Text
$_['text_extension'] = 'Plėtiniai';
$_['text_success'] = 'Sėkmė: Jūs pakeitėte CardConnect mokėjimo modulį!';
$_['text_edit'] = 'Redaguoti CardConnect';
$_['text_cardconnect'] = '<a href="http://www.cardconnect.com" target="_blank"><img src="view/image/payment/cardconnect.png" alt="CardConnect" title="CardConnect"></a>';
$_['text_payment'] = 'Mokėjimas';
$_['text_refund'] = 'Grąžinti pinigus';
$_['text_authorize'] = 'Įgalioti';
$_['text_live'] = 'Tiesiogiai';
$_['text_test'] = 'Testas';
$_['text_no_cron_time'] = 'Kronas dar nebuvo įvykdytas';
$_['text_payment_info'] = 'Mokėjimo informacija';
$_['text_payment_method'] = 'Mokėjimo būdas';
$_['text_card'] = 'Kort';
$_['text_echeck'] = 'eCheck';
$_['text_reference'] = 'Nuoroda';
$_['text_update'] = 'Atnaujinti';
$_['text_order_total'] = 'Užsakymo suma';
$_['text_total_captured'] = 'Iš viso užfiksuota';
$_['text_capture_payment'] = 'Užfiksuoti mokėjimą';
$_['text_refund_payment'] = 'Grąžinimo mokėjimas';
$_['text_void'] = 'Tuščia';
$_['text_transactions'] = 'Sandoriai';
$_['text_column_type'] = 'Tipas';
$_['text_column_reference'] = 'Nuoroda';
$_['text_column_amount'] = 'Suma';
$_['text_column_status'] = 'Būsena';
$_['text_column_date_modified'] = 'Pakeitimo data';
$_['text_column_date_added'] = 'Įtraukimo data';
$_['text_column_update'] = 'Atnaujinti';
$_['text_column_void'] = 'Tuščia';
$_['text_confirm_capture'] = 'Ar tikrai norite užfiksuoti mokėjimą?';
$_['text_confirm_refund'] = 'Ar tikrai norite grąžinti mokėjimą?';
$_['text_inquire_success'] = 'Apklausa buvo sėkminga';
$_['text_capture_success'] = 'Užklausa užfiksuoti buvo sėkminga';
$_['text_refund_success'] = 'Lėšų grąžinimo užklausa buvo sėkminga';
$_['text_void_success'] = 'Anuliuota užklausa buvo sėkminga';

// Entry
$_['entry_merchant_id'] = 'Prekybininko ID';
$_['entry_api_username'] = 'API vartotojo vardas';
$_['entry_api_password'] = 'API slaptažodis';
$_['entry_token'] = 'Slaptas ženklas';
$_['entry_transaction'] = 'Sandoris';
$_['entry_environment'] = 'Aplinka';
$_['entry_site'] = 'Svetainė';
$_['entry_store_cards'] = 'Parduotuvės kortelės';
$_['entry_echeck'] = 'eCheck';
$_['entry_total'] = 'Iš viso';
$_['entry_geo_zone'] = 'Geo zona';
$_['entry_status'] = 'Būsena';
$_['entry_logging'] = 'Derinimo registravimas';
$_['entry_sort_order'] = 'Rūšiavimo tvarka';
$_['entry_cron_url'] = 'Cron darbo URL';
$_['entry_cron_time'] = 'Cron Job paskutinis bėgimas';
$_['entry_order_status_pending'] = 'Laukiama';
$_['entry_order_status_processing'] = 'Apdorojimas';

// Help
$_['help_merchant_id'] = 'Jūsų asmeninė CardConnect paskyros prekybininko ID.';
$_['help_api_username'] = 'Jūsų vartotojo vardas norint pasiekti CardConnect API.';
$_['help_api_password'] = 'Jūsų slaptažodis norint pasiekti CardConnect API.';
$_['help_token'] = 'Įveskite atsitiktinį prieigos raktą saugumo sumetimais arba naudokite sugeneruotą.';
$_['help_transaction']              = 'Choose \'Payment\' to capture the payment immediately or \'Authorize\' to have to approve it.';
$_['help_site'] = 'Tai nustato pirmąją API URL dalį. Keiskite tik gavus nurodymą.';
$_['help_store_cards'] = 'Nesvarbu, ar norite saugoti korteles naudodami tokenizaciją.';
$_['help_echeck'] = 'Nesvarbu, ar norite pasiūlyti galimybę mokėti naudojant „eCheck“.';
$_['help_total'] = 'Bendra užsakymo apmokėjimo suma turi būti pasiekta, kad šis mokėjimo būdas suaktyvėtų. Turi būti vertė be valiutos ženklo.';
$_['help_logging'] = 'Įgalinus derinimą, slapti duomenys bus įrašyti į žurnalo failą. Visada turėtumėte išjungti, nebent nurodyta kitaip.';
$_['help_cron_url'] = 'Nustatykite cron užduotį, kad šis URL būtų iškviestas, kad užsakymai būtų atnaujinami automatiškai. Jis skirtas paleisti praėjus kelioms valandoms po paskutinio darbo dienos fiksavimo.';
$_['help_cron_time'] = 'Tai paskutinis kartas, kai buvo vykdomas cron užduoties URL.';
$_['help_order_status_pending'] = 'Užsakymo būsena, kai prekybininkas turi patvirtinti užsakymą.';
$_['help_order_status_processing'] = 'Užsakymo būsena, kai užsakymas užfiksuojamas automatiškai.';

// Button
$_['button_inquire_all'] = 'Teiraukitės visų';
$_['button_capture'] = 'Užfiksuoti';
$_['button_refund'] = 'Grąžinti pinigus';
$_['button_void_all'] = 'Tuščia viskas';
$_['button_inquire'] = 'Pasiteirauti';
$_['button_void'] = 'Tuščia';

// Error
$_['error_permission'] = 'Įspėjimas: Jūs neturite leidimo keisti mokėjimo CardConnect!';
$_['error_merchant_id'] = 'Reikalingas prekybininko ID!';
$_['error_api_username'] = 'Reikalingas API vartotojo vardas!';
$_['error_api_password'] = 'Reikalingas API slaptažodis!';
$_['error_token'] = 'Reikalingas slaptas žetonas!';
$_['error_site'] = 'Reikalinga svetainė!';
$_['error_amount_zero'] = 'Suma turi būti didesnė už nulį!';
$_['error_no_order'] = 'Nėra atitinkamos užsakymo informacijos!';
$_['error_invalid_response'] = 'Gautas neteisingas atsakymas!';
$_['error_data_missing'] = 'Trūksta duomenų!';
$_['error_not_enabled'] = 'Modulis neįjungtas!';