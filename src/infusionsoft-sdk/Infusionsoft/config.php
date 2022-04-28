<?php

$infusionsoft_host = 'sk687.infusionsoft.com';
$infusionsoft_api_key = '0f0598ec5f4b0b1adc2b0b7f06159377';

//To Add Custom Fields, use the addCustomField method like below.
//Infusionsoft_Contact::addCustomField('_LeadScore');

//Below is just some magic...  Unless you are going to be communicating with more than one APP at the SAME TIME.  You can ignore it.
Infusionsoft_AppPool::addApp(new Infusionsoft_App($infusionsoft_host, $infusionsoft_api_key, 443));