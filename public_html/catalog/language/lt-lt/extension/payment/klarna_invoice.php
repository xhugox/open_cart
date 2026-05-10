<?php
// Text
$_['text_title'] = 'Klarna sąskaita – apmokėkite per 14 dienų';
$_['text_terms_fee']			= '<span id="klarna_invoice_toc"></span> (+%s)<script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\', charge: %s});</script>';
$_['text_terms_no_fee']			= '<span id="klarna_invoice_toc"></span><script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', eid: \'%s\', country: \'%s\'});</script>';
$_['text_additional'] = 'Kad galėtų apdoroti jūsų užsakymą, „Klarna“ sąskaitai faktūrai reikia papildomos informacijos.';
$_['text_male'] = 'Vyriška';
$_['text_female'] = 'Moteris';
$_['text_year'] = 'Metai';
$_['text_month'] = 'Mėnuo';
$_['text_day'] = 'Diena';
$_['text_comment']				= 'Klarna\'s Invoice ID: %s. ' . "\n" . '%s/%s: %.4f';
$_['text_trems_description'] = 'Perduodant informaciją, reikalingą pirkimo iš sąskaitos tvarkymui ir tapatybės bei kreditingumo patikrinimui
Sutinku siųsti duomenis Klarna. Galiu bet kada atšaukti savo <a href="https://online.klarna.com/consent_de.yaws" target="_blank">sutikimą</a> ir tai galioja ateityje.';

// Entry
$_['entry_gender'] = 'Lytis';
$_['entry_pno'] = 'Asmeninis numeris';
$_['entry_dob'] = 'Gimimo data';
$_['entry_phone_no'] = 'Telefono numeris';
$_['entry_street'] = 'Gatvė';
$_['entry_house_no'] = 'Namas Nr.';
$_['entry_house_ext'] = 'Namo išl.';
$_['entry_company'] = 'Įmonės registracijos numeris';

// Help
$_['help_pno'] = 'Čia įveskite savo socialinio draudimo numerį.';
$_['help_phone_no'] = 'Įveskite savo telefono numerį.';
$_['help_street'] = 'Atkreipkite dėmesį, kad atsiskaitant Klarna, pristatymas gali vykti tik registruotu adresu.';
$_['help_house_no'] = 'Įveskite savo namo numerį.';
$_['help_house_ext'] = 'Pateikite savo namo priestatą čia. Pvz. A, B, C, raudona, mėlyna ir kt.';
$_['help_company']				= 'Please enter your Company\'s registration number';

// Error
$_['error_deu_terms']			= 'You must agree to Klarna\'s privacy policy (Datenschutz)';
$_['error_address_match'] = 'Jei norite naudoti „Klarna“ sąskaitą faktūrą, atsiskaitymo ir pristatymo adresai turi sutapti';
$_['error_network'] = 'Prisijungiant prie „Klarna“ įvyko klaida. Bandykite dar kartą vėliau.';