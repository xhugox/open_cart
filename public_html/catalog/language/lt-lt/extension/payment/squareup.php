<?php
// Text
$_['text_new_card'] = '+ Pridėti naują kortelę';
$_['text_loading'] = 'Įkeliama... Palaukite...';
$_['text_card_details'] = 'Kortelės detalės';
$_['text_saved_card'] = 'Naudokite išsaugotą kortelę:';
$_['text_card_ends_in'] = 'Mokėkite esama %s kortele, kuri baigiasi XXXX XXXX XXXX %s';
$_['text_card_number'] = 'Kortelės numeris:';
$_['text_card_expiry'] = 'Kortelės galiojimo laikas (MM/MM):';
$_['text_card_cvc'] = 'Kortelės saugos kodas (CVC):';
$_['text_card_zip'] = 'Kortelės pašto kodas:';
$_['text_card_save'] = 'Išsaugoti kortelę ateityje?';
$_['text_trial'] = '%s kas %s %s %s mokėjimų tada';
$_['text_recurring'] = '%s kas %s %s';
$_['text_length'] = '%s mokėjimams';
$_['text_cron_subject'] = 'Square CRON darbo santrauka';
$_['text_cron_message'] = 'Čia yra visų CRON užduočių, kurias atliko jūsų Square plėtinys, sąrašas:';
$_['text_squareup_profile_suspended'] = 'Jūsų pasikartojantys mokėjimai buvo sustabdyti. Norėdami gauti daugiau informacijos, susisiekite su mumis.';
$_['text_squareup_trial_expired'] = 'Jūsų bandomasis laikotarpis baigėsi.';
$_['text_squareup_recurring_expired'] = 'Jūsų pasikartojančių mokėjimų galiojimo laikas baigėsi. Tai buvo paskutinis jūsų mokėjimas.';
$_['text_cron_summary_token_heading'] = 'Prieigos prieigos rakto atnaujinimas:';
$_['text_cron_summary_token_updated'] = 'Prieigos prieigos raktas sėkmingai atnaujintas!';
$_['text_cron_summary_error_heading'] = 'Operacijų klaidos:';
$_['text_cron_summary_fail_heading'] = 'Nepavykusios operacijos (profiliai sustabdyti):';
$_['text_cron_summary_success_heading'] = 'Sėkmingos operacijos:';
$_['text_cron_fail_charge'] = 'Profilio <strong>#%s</strong> nepavyko apmokestinti <strong>%s</strong>';
$_['text_cron_success_charge'] = 'Profilis <strong>#%s</strong> buvo apmokestintas <strong>%s</strong>';
$_['text_card_placeholder'] = 'XXXX XXXX XXXX XXXX';
$_['text_cvv'] = 'CVV';
$_['text_expiry'] = 'MM/MM';
$_['text_default_squareup_name'] = 'Kredito / debeto kortelė';
$_['text_token_issue_customer_error'] = 'Mes patiriame techninį mokėjimo sistemos gedimą. Bandykite dar kartą vėliau.';
$_['text_token_revoked_subject'] = 'Jūsų aikštės prieigos raktas buvo atšauktas!';
$_['text_token_revoked_message']        = "The Square payment extension's access to your Square account has been revoked through the Square Dashboard. You need to verify your application credentials in the extension settings and connect again.";
$_['text_token_expired_subject'] = 'Baigėsi jūsų aikštės prieigos rakto galiojimo laikas!';
$_['text_token_expired_message']        = "The Square payment extension's access token connecting it to your Square account has expired. You need to verify your application credentials and CRON job in the extension settings and connect again.";

// Error
$_['error_browser_not_supported'] = 'Klaida: mokėjimo sistema nebepalaiko jūsų žiniatinklio naršyklės. Atnaujinkite arba naudokite kitą.';
$_['error_card_invalid'] = 'Klaida: kortelė netinkama!';
$_['error_squareup_cron_token'] = 'Klaida: prieigos prieigos rakto nepavyko atnaujinti. Prijunkite „Square Payment“ plėtinį naudodami „OpenCart“ administratoriaus skydelį.';

// Warning
$_['warning_test_mode'] = 'Įspėjimas: smėlio dėžės režimas įjungtas! Atrodo, kad operacijos bus įvykdytos, tačiau mokesčiai nebus atlikti.';

// Statuses
$_['squareup_status_comment_authorized'] = 'Kortelės operacija buvo autorizuota, bet dar neužfiksuota.';
$_['squareup_status_comment_captured'] = 'Kortelės operacija buvo autorizuota ir vėliau užfiksuota (t. y. užbaigta).';
$_['squareup_status_comment_voided'] = 'Kortelės operacija buvo autorizuota ir vėliau anuliuota (t. y. atšaukta).';
$_['squareup_status_comment_failed'] = 'Operacija kortele nepavyko.';

// Override errors
$_['squareup_override_error_billing_address.country'] = 'Mokėjimo adreso šalis negalioja. Pakeiskite jį ir bandykite dar kartą.';
$_['squareup_override_error_shipping_address.country'] = 'Pristatymo adreso šalis negalioja. Pakeiskite jį ir bandykite dar kartą.';
$_['squareup_override_error_email_address'] = 'Jūsų kliento el. pašto adresas negalioja. Pakeiskite jį ir bandykite dar kartą.';
$_['squareup_override_error_phone_number'] = 'Jūsų kliento telefono numeris negalioja. Pakeiskite jį ir bandykite dar kartą.';
$_['squareup_error_field'] = 'Laukas: %s';