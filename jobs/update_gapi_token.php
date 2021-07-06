<?php
error_reporting(E_ERROR);

include_once "../config/const.php";

include_once "../vendor/autoload.php";

use MeiKaiGsuit\GSheet\GClient;

$client = (new GClient(GClient::SHEET_WRITE_SCOPE_GROUP))->getClient();