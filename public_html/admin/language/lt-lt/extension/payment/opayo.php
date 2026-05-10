<?php
// Heading
$_['heading_title'] = 'Gerai';

// Text
$_['text_opayo'] = '<img src="view/image/payment/opayo.png" alt="Opayo" title="Opayo" />';
$_['text_extensions'] = 'Plėtiniai';
$_['text_edit'] = 'Redaguoti Opayo';
$_['text_tab_general'] = 'Generolas';
$_['text_tab_cron'] = 'Cron';
$_['text_test'] = 'Testas';
$_['text_live'] = 'Tiesiogiai';
$_['text_defered'] = 'Atidėtas';
$_['text_authenticate'] = 'Autentifikuoti';
$_['text_payment'] = 'Mokėjimas';
$_['text_payment_info'] = 'Mokėjimo informacija';
$_['text_release_status'] = 'Mokėjimas išleistas';
$_['text_void_status'] = 'Mokėjimas anuliuotas';
$_['text_rebate_status'] = 'Mokėjimas grąžintas';
$_['text_order_ref'] = 'Užsakymo Nr';
$_['text_order_total'] = 'Iš viso įgaliota';
$_['text_total_released'] = 'Iš viso išleista';
$_['text_transactions'] = 'Sandoriai';
$_['text_column_amount'] = 'Suma';
$_['text_column_type'] = 'Tipas';
$_['text_column_date_added'] = 'Sukurta';
$_['text_confirm_void'] = 'Ar tikrai norite anuliuoti mokėjimą?';
$_['text_confirm_release'] = 'Ar tikrai norite atšaukti mokėjimą?';
$_['text_confirm_rebate'] = 'Ar tikrai norite grąžinti mokėjimą?';

// Entry
$_['entry_vendor'] = 'Pardavėjas';
$_['entry_environment'] = 'Aplinka';
$_['entry_transaction_method'] = 'Sandorio metodas';
$_['entry_total'] = 'Iš viso';
$_['entry_order_status'] = 'Užsakymo būsena';
$_['entry_geo_zone'] = 'Geo zona';
$_['entry_status'] = 'Būsena';
$_['entry_sort_order'] = 'Rūšiavimo tvarka';
$_['entry_debug'] = 'Derinimo registravimas';
$_['entry_card_save'] = 'Parduotuvės kortelės';
$_['entry_cron_token'] = 'Slaptas ženklas';
$_['entry_cron_url'] = 'URL';
$_['entry_cron_last_run'] = 'Paskutinis paleidimo laikas:';

// Help
$_['help_total'] = 'Bendra užsakymo apmokėjimo suma turi būti pasiekta, kad šis mokėjimo būdas suaktyvėtų.';
$_['help_debug'] = 'Įgalinus derinimą, slapti duomenys bus įrašyti į žurnalo failą. Visada turėtumėte išjungti, nebent nurodyta kitaip.';
$_['help_transaction_method'] = 'Operacijos metodas PRIVALO būti nustatytas į Mokėjimas, kad būtų galima atlikti prenumeratos mokėjimus.';
$_['help_card_save'] = 'Kad pirkėjas galėtų išsaugoti kortelės duomenis tolesniems mokėjimams, reikia užsiprenumeruoti MID TOKEN. Turėsite susisiekti su „Opayo“ klientų aptarnavimo tarnyba, kad aptartumėte, kaip įjungti žetonų sistemą jūsų paskyrai.';
$_['help_cron_token'] = 'Padarykite tai ilgą ir sunkiai atspėjamą.';
$_['help_cron_url'] = 'Nustatykite cron, kad iškviestumėte šį URL.';

// Button
$_['button_release'] = 'Paleisti';
$_['button_rebate'] = 'Nuolaida / grąžinimas';
$_['button_void'] = 'Tuščia';
$_['button_enable_recurring'] = 'Įgalinti pasikartojantį';
$_['button_disable_recurring'] = 'Išjungti pasikartojančius';

// Success
$_['success_save'] = 'Sėkmė: pakeitėte Opayo!';
$_['success_release_ok'] = 'Sėkmė: išleidimas buvo sėkmingas!';
$_['success_release_ok_order'] = 'Sėkmė: išleidimas buvo sėkmingas, užsakymo būsena atnaujinta į sėkmingą – išspręsta!';
$_['success_rebate_ok'] = 'Sėkmė: nuolaida buvo sėkminga!';
$_['success_rebate_ok_order'] = 'Sėkmė: nuolaida buvo sėkminga, užsakymo būsena atnaujinta į nuolaidą!';
$_['success_void_ok'] = 'Sėkmė: anuliavimas buvo sėkmingas, užsakymo būsena atnaujinta į anuliuota!';
$_['success_enable_recurring'] = 'Sėkmė: pasikartojantis mokėjimas buvo įgalintas!';
$_['success_disable_recurring'] = 'Sėkmė: pasikartojantis mokėjimas buvo išjungtas!';

// Error
$_['error_warning'] = 'Įspėjimas: atidžiai patikrinkite, ar formoje nėra klaidų!';
$_['error_permission'] = 'Įspėjimas: Jūs neturite leidimo keisti mokėjimo Opayo!';
$_['error_vendor'] = 'Reikalingas pardavėjo ID!';
