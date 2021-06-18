# g_sheet
google sheet api for meikai

### Example
```php
include_once "./vendor/autoload.php";

use MeiKaiGsuit\GSheet\GClient;

$client = (new GClient(GClient::SHEET_WRITE_SCOPE_GROUP))->getClient();

$data = [
    '2021-06',
    '=AB`BR1`',
    [
        'value' => 0.1,
        'number_format' => [
            'type' => \MeiKaiGsuit\GSheet\GSheetCell::FIELDS_NUMBER_FORMAT_TYPE_PERCENT,
//            'pattern' => '',
        ]
    ],
    '123.11',
    '=SUM(`BC3``CR`:`BC1``CR`)',
];

$gfile = new \MeiKaiGsuit\GSheet\GSheet($client, '1jYiRxggd3T2iBDpkin97p5p9q5SX7172ReH_aS9VsKg');

$sheets = $gfile->getSheets();

foreach ($sheets as $sheet) {
    if (!preg_match('/^[A-Z]{3}$/', $sheet->getProperties()->getTitle())) {
        continue;
    }
    $row_index = $gfile->getSheetLastRow($sheet);
    $gfile->addCurrencyNumberRow($sheet->getProperties()->getSheetId(), $sheet->getProperties()->getTitle(), $row_index, $data);
}
```
