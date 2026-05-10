<?php
// Heading
$_['heading_title'] = 'eWAY mokėjimas';

// Text
$_['text_extension'] = 'Plėtiniai';
$_['text_success'] = 'Sėkmė: pakeitėte savo eWAY informaciją!';
$_['text_edit'] = 'Redaguoti eWAY';
$_['text_eway'] = '<a target="_BLANK" href="http://www.eway.com.au/"><img src="view/image/payment/eway.png" alt="eWAY" title="eWAY" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_authorisation'] = 'Autorizacija';
$_['text_sale'] = 'Išpardavimas';
$_['text_transparent'] = 'Skaidrus peradresavimas (mokėjimo forma svetainėje)';
$_['text_iframe'] = 'IFrame (mokėjimo forma lange)';
$_['text_connect_eway'] = 'eWAY padeda įmonėms saugiai apdoroti visas pagrindines kredito korteles su integruota sukčiavimo prevencija, visą parą veikiančia technine pagalba ir dar daugiau. <a target="_blank" href="https://myeway.force.com/success/accelerator-signup?pid=4382&pa=0012000000ivcWf">Spustelėkite čia</a>';
$_['text_eway_image'] = '<a target="_blank" href="https://myeway.force.com/success/accelerator-signup?pid=4382&pa=0012000000ivcWf"><img src="view/image/payment/eway_connect.png" alt="eWAY" title="eWAY" class="img/afluid" />';

// Entry
$_['entry_paymode'] = 'Mokėjimo režimas';
$_['entry_test'] = 'Bandymo režimas';
$_['entry_order_status'] = 'Užsakymo būsena';
$_['entry_order_status_refund'] = 'Užsakymo būsena grąžinta';
$_['entry_order_status_auth'] = 'Įgalioto užsakymo būsena';
$_['entry_order_status_fraud'] = 'Įtariamo sukčiavimo užsakymo būsena';
$_['entry_status'] = 'Būsena';
$_['entry_username'] = 'eWAY API raktas';
$_['entry_password'] = 'eWAY slaptažodis';
$_['entry_payment_type'] = 'Mokėjimo tipas';
$_['entry_geo_zone'] = 'Geo zona';
$_['entry_sort_order'] = 'Rūšiavimo tvarka';
$_['entry_transaction_method'] = 'Sandorio metodas';

// Error
$_['error_permission'] = 'Įspėjimas: Jūs neturite leidimo keisti eWAY mokėjimo modulio';
$_['error_username'] = 'Reikalingas eWAY API raktas!';
$_['error_password'] = 'Reikalingas eWAY slaptažodis!';
$_['error_payment_type'] = 'Būtinas bent vienas mokėjimo tipas!';

// Help hints
$_['help_testmode'] = 'Jei norite naudoti eWAY smėlio dėžę, nustatykite Taip.';
$_['help_username'] = 'Jūsų eWAY API raktas iš jūsų MYeWAY paskyros.';
$_['help_password'] = 'Jūsų eWAY API slaptažodis iš jūsų MYeWAY paskyros.';
$_['help_transaction_method'] = 'Autorizacija galima tik Australijos bankams';

// Order page - payment tab
$_['text_payment_info'] = 'Mokėjimo informacija';
$_['text_order_total'] = 'Iš viso įgaliota';
$_['text_transactions'] = 'Sandoriai';
$_['text_column_transactionid'] = 'eWAY operacijos ID';
$_['text_column_amount'] = 'Suma';
$_['text_column_type'] = 'Tipas';
$_['text_column_created'] = 'Sukurta';
$_['text_total_captured'] = 'Iš viso užfiksuota';
$_['text_capture_status'] = 'Mokėjimas užfiksuotas';
$_['text_void_status'] = 'Mokėjimas anuliuotas';
$_['text_refund_status'] = 'Mokėjimas grąžintas';
$_['text_total_refunded'] = 'Iš viso grąžinta';
$_['text_refund_success'] = 'Pinigų grąžinimas pavyko!';
$_['text_capture_success'] = 'Užfiksuoti pavyko!';
$_['text_refund_failed'] = 'Nepavyko grąžinti lėšų:';
$_['text_capture_failed'] = 'Užfiksuoti nepavyko:';
$_['text_unknown_failure'] = 'Neteisingas užsakymas arba suma';
$_['text_refund'] = 'Grąžinti pinigus';

$_['text_confirm_capture'] = 'Ar tikrai norite užfiksuoti mokėjimą?';
$_['text_confirm_release'] = 'Ar tikrai norite atšaukti mokėjimą?';
$_['text_confirm_refund'] = 'Ar tikrai norite grąžinti mokėjimą?';

$_['text_empty_refund'] = 'Įveskite grąžintiną sumą';
$_['text_empty_capture'] = 'Įveskite sumą, kurią norite užfiksuoti';

$_['btn_refund'] = 'Grąžinti pinigus';
$_['btn_capture'] = 'Užfiksuoti';

// Validation Error codes
$_['text_card_message_V6000'] = 'Neapibrėžta patvirtinimo klaida';
$_['text_card_message_V6001'] = 'Neteisingas kliento IP';
$_['text_card_message_V6002'] = 'Neteisingas įrenginio ID';
$_['text_card_message_V6011'] = 'Neteisinga suma';
$_['text_card_message_V6012'] = 'Neteisingas sąskaitos faktūros aprašymas';
$_['text_card_message_V6013'] = 'Neteisingas sąskaitos faktūros numeris';
$_['text_card_message_V6014'] = 'Netinkama sąskaitos faktūros nuoroda';
$_['text_card_message_V6015'] = 'Neteisingas valiutos kodas';
$_['text_card_message_V6016'] = 'Reikalingas mokėjimas';
$_['text_card_message_V6017'] = 'Reikalingas mokėjimo valiutos kodas';
$_['text_card_message_V6018'] = 'Nežinomas mokėjimo valiutos kodas';
$_['text_card_message_V6021'] = 'Būtinas kortelės turėtojo vardas';
$_['text_card_message_V6022'] = 'Būtinas kortelės numeris';
$_['text_card_message_V6023'] = 'Reikalingas CVN';
$_['text_card_message_V6031'] = 'Neteisingas kortelės numeris';
$_['text_card_message_V6032'] = 'Neteisingas CVN';
$_['text_card_message_V6033'] = 'Neteisinga galiojimo data';
$_['text_card_message_V6034'] = 'Neteisingas leidimo numeris';
$_['text_card_message_V6035'] = 'Neteisinga pradžios data';
$_['text_card_message_V6036'] = 'Netinkamas mėnuo';
$_['text_card_message_V6037'] = 'Netinkami metai';
$_['text_card_message_V6040'] = 'Neteisingas prieigos rakto kliento ID';
$_['text_card_message_V6041'] = 'Reikalingas klientas';
$_['text_card_message_V6042'] = 'Būtinas kliento vardas';
$_['text_card_message_V6043'] = 'Būtina nurodyti kliento pavardę';
$_['text_card_message_V6044'] = 'Būtinas kliento šalies kodas';
$_['text_card_message_V6045'] = 'Reikalingas kliento pavadinimas';
$_['text_card_message_V6046'] = 'Reikalingas kliento ID prieigos raktas';
$_['text_card_message_V6047'] = 'RedirectURL reikalingas';
$_['text_card_message_V6051'] = 'Neteisingas vardas';
$_['text_card_message_V6052'] = 'Neteisinga pavardė';
$_['text_card_message_V6053'] = 'Neteisingas šalies kodas';
$_['text_card_message_V6054'] = 'Neteisingas el';
$_['text_card_message_V6055'] = 'Neteisingas telefonas';
$_['text_card_message_V6056'] = 'Error 500 (Server Error)!!1500.That’s an error.There was an error. Please try again later.That’s all we know.';
$_['text_card_message_V6057'] = 'Error 500 (Server Error)!!1500.That’s an error.There was an error. Please try again later.That’s all we know.';
$_['text_card_message_V6058'] = 'Neteisingas pavadinimas';
$_['text_card_message_V6059'] = 'Peradresavimo URL neteisingas';
$_['text_card_message_V6060'] = 'Peradresavimo URL neteisingas';
$_['text_card_message_V6061'] = 'Neteisinga nuoroda';
$_['text_card_message_V6062'] = 'Neteisingas įmonės pavadinimas';
$_['text_card_message_V6063'] = 'Neteisingas darbo aprašymas';
$_['text_card_message_V6064'] = 'Netinkama gatvė1';
$_['text_card_message_V6065'] = 'Neteisinga gatvė2';
$_['text_card_message_V6066'] = 'Neteisingas miestas';
$_['text_card_message_V6067'] = 'Netinkama valstybė';
$_['text_card_message_V6068'] = 'Neteisingas pašto kodas';
$_['text_card_message_V6069'] = 'Neteisingas el';
$_['text_card_message_V6070'] = 'Neteisingas telefonas';
$_['text_card_message_V6071'] = 'Netinkamas mobilusis';
$_['text_card_message_V6072'] = 'Netinkami komentarai';
$_['text_card_message_V6073'] = 'Neteisingas faksas';
$_['text_card_message_V6074'] = 'Netinkamas URL';
$_['text_card_message_V6075'] = 'Neteisingas pristatymo adresas Vardas';
$_['text_card_message_V6076'] = 'Neteisingas pristatymo adresas Pavardė';
$_['text_card_message_V6077'] = 'Neteisingas pristatymo adresas Gatvė1';
$_['text_card_message_V6078'] = 'Neteisingas pristatymo adresas Gatvė2';
$_['text_card_message_V6079'] = 'Neteisingas pristatymo adresas Miestas';
$_['text_card_message_V6080'] = 'Neteisinga pristatymo adreso būsena';
$_['text_card_message_V6081'] = 'Neteisingas pristatymo adreso pašto kodas';
$_['text_card_message_V6082'] = 'Neteisingas pristatymo adreso el. paštas';
$_['text_card_message_V6083'] = 'Neteisingas pristatymo adresas Telefonas';
$_['text_card_message_V6084'] = 'Neteisingas pristatymo adreso šalis';
$_['text_card_message_V6091'] = 'Nežinomas šalies kodas';
$_['text_card_message_V6100'] = 'Neteisingas kortelės pavadinimas';
$_['text_card_message_V6101'] = 'Neteisinga kortelės galiojimo pabaigos mėnuo';
$_['text_card_message_V6102'] = 'Neteisinga kortelės galiojimo pabaigos metai';
$_['text_card_message_V6103'] = 'Neteisinga kortelės pradžios mėnuo';
$_['text_card_message_V6104'] = 'Neteisinga kortelės pradžios metai';
$_['text_card_message_V6105'] = 'Neteisingas kortelės išdavimo numeris';
$_['text_card_message_V6106'] = 'Neteisingas kortelės CVN';
$_['text_card_message_V6107'] = 'Neteisingas prieigos kodas';
$_['text_card_message_V6108'] = 'Neteisingas CustomerHostAddress';
$_['text_card_message_V6109'] = 'Neteisingas UserAgent';
$_['text_card_message_V6110'] = 'Neteisingas kortelės numeris';
$_['text_card_message_V6111'] = 'Neteisėta API prieiga, paskyra nesertifikuota PCI';
$_['text_card_message_V6112'] = 'Perteklinė kortelės informacija, išskyrus galiojimo metus ir mėnesį';
$_['text_card_message_V6113'] = 'Netinkama pinigų grąžinimo operacija';
$_['text_card_message_V6114'] = 'Šliuzo patvirtinimo klaida';
$_['text_card_message_V6115'] = 'Neteisingas „DirectRefundRequest“, operacijos ID';
$_['text_card_message_V6116'] = 'Netinkami kortelės duomenys pradiniame operacijos ID';
$_['text_card_message_V6124'] = 'Netinkami eilutės elementai. Eilučių elementai buvo pateikti, tačiau sumos neatitinka lauko TotalAmount';
$_['text_card_message_V6125'] = 'Pasirinktas mokėjimo tipas neįjungtas';
$_['text_card_message_V6126'] = 'Netinkamas šifruotos kortelės numeris, nepavyko iššifruoti';
$_['text_card_message_V6127'] = 'Neteisingas užšifruotas cvn, iššifruoti nepavyko';
$_['text_card_message_V6128'] = 'Netinkamas mokėjimo būdas';
$_['text_card_message_V6129'] = 'Operacija nebuvo įgaliota užfiksuoti / atšaukti';
$_['text_card_message_V6130'] = 'Bendra kliento informacijos klaida';
$_['text_card_message_V6131'] = 'Bendra pristatymo informacijos klaida';
$_['text_card_message_V6132'] = 'Sandoris jau baigtas arba anuliuotas, operacija neleidžiama';
$_['text_card_message_V6133'] = 'Atsiskaityti negalima pagal mokėjimo tipą';
$_['text_card_message_V6134'] = 'Netinkamas fiksavimo / negaliojimo autentifikavimo operacijos ID';
$_['text_card_message_V6135'] = '„PayPal“ klaida apdorojant lėšų grąžinimą';
$_['text_card_message_V6140'] = 'Prekybininko paskyra laikinai sustabdyta';
$_['text_card_message_V6141'] = 'Neteisinga „PayPal“ paskyros informacija arba API parašas';
$_['text_card_message_V6142'] = 'Įgaliojimas nepasiekiamas bankui / filialui';
$_['text_card_message_V6150'] = 'Neteisinga grąžinama suma';
$_['text_card_message_V6151'] = 'Grąžinama suma didesnė nei pradinė operacija';
$_['text_card_message_D4406'] = 'Nežinoma klaida';
$_['text_card_message_S5010'] = 'Nežinoma klaida';