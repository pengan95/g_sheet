<?php
error_reporting(E_ERROR);

include_once "../config/const.php";

include_once "../vendor/autoload.php";

use MeiKaiGsuit\GSheet\GClient;
use MeiKaiGsuit\GSheet\GSheet;

$sp_dsn = "mysql:host=" . WALLET_DB_HOST . ";dbname=" . SHOPMALL_DB_NAME;
$ebrp_dsn = "mysql:host=" . EB_DB_HOST . ";dbname=" . EB_REPORT_DB_NAME;


$sp_db = new PDO($sp_dsn,WALLET_DB_USER, WALLET_DB_PWD, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
$ebrp_db = new PDO($ebrp_dsn,EB_DB_USER, EB_DB_PWD, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);

$month = $argv[1];

$client = (new GClient(GClient::SHEET_WRITE_SCOPE_GROUP))->getClient();
// 1.获取所有数据分析
// 2.组合所有数据记录
// 3.更新数据都google sheet
$currencies = $argv[2];

$currencies = explode(',', $currencies);

$gfile = new GSheet($client, GSHEET_FILE_ID);

$sheets = getSheets($gfile);

$spHsbcData = getSpHsbcMonthData($month);
$spCheckData = getSpCheckMonthData($month);
$spPayPalData = getSpPayPalMonthData($month);
$spAchData = getSpAchMonthData($month);

foreach ($currencies as $currency) {
    if (!isset($sheets[strtoupper($currency)])) {
        $sheet_id = $gfile->addSheet(strtoupper($currency));
        $row_index = 1; //没有默认添加标题行所有row_index就从1开始了
        echo "$currency sheet not exist" . PHP_EOL;
    } else {
        $sheet_id = $sheets[strtoupper($currency)]['id'];
        $row_index = $sheets[strtoupper($currency)]['append_row_index'];
    }

    $rowData = new \MeiKaiGsuit\FinanceRow();
    $rowData->setMonth($month);

    if ($row_index == 1) {
        $rowData->setSystemInit(0)->setWalletInit(0)->setSpInit(0);
    }

    $walletData = getV3TableDataByMonthAndCurrency($currency, $month);

    if ($walletData) {
        $rowData->setWalletUpCashback(round($walletData['cashback_add'],2))
            ->setWalletUpMarketing(round($walletData['opt_add'],2))
            ->setWalletUpWithdrawReturn(0)
            ->setWalletUpCheckReturn(round($walletData['check_add'],2))
            ->setWalletUpPaypalReturn(round($walletData['paypal_add'], 2))
            ->setWalletUpOtherReturn(round($walletData['other_add'], 2))
            ->setWalletDownCashback(round($walletData['cashback_reduce'], 2))
            ->setWalletDownMarketing(round($walletData['opt_reduce'], 2))
            ->setWalletDownWithdrawApply(round($walletData['shopmall_reduce'], 2));
    }

    $shopMallData = getShopMallDataByMonthAndCurrency($currency, $month);

    if ($shopMallData) {
        $rowData->setSpUpApply($shopMallData['add_all'])
            ->setSpDownCheckPaid(round($shopMallData['reduce_check_paid'], 2))
            ->setSpDownPaypalPaid(round($shopMallData['reduce_paypal_paid'],2))
            ->setSpDownOtherPaid(round($shopMallData['reduce_other_paid'],2))
            ->setSpDownCheckFailed(round($shopMallData['reduce_check_failed'], 2))
            ->setSpDownPaypalFailed(round($shopMallData['reduce_paypal_failed'], 2))
            ->setSpDownOtherFailed(round($shopMallData['reduce_other_failed'],2))
            ->setSpDownCanceled(round($shopMallData['reduce_cancel'],2))
            ->setSpDownFraud(round($shopMallData['reduce_fake'],2));
    }

    if ($currency == 'USD' && $spCheckData) {
        $rowData->setBankCheckUp(round($spCheckData['paid_amount'] ?? 0, 2));
        $rowData->setBankCheckDown(round($spCheckData['return_amount'] ?? 0, 2));
    }

    if (isset($spPayPalData[$currency])) {
        $rowData->setBankPaypalUp(round($spPayPalData[$currency]['paid_amount'] ?? 0, 2));
        $rowData->setBankPaypalDown(round($spPayPalData[$currency]['return_amount'] ?? 0, 2));
    }

    $data = $rowData->toArray();

    if (in_array($currency, ['USD','HKD','TWD'])) {
        $data[] = 0;
        $data[] = round($spAchData[$currency]['paid_amount'] ?? 0, 2);
        $data[] = round($spAchData[$currency]['return_amount'] ?? 0, 2);
    }

    if ($currency == 'HKD') {
        $data[] = round($spHsbcData, 2);
    } else {
        $data[] = 0;  //其他
    }

    if ($currency != 'CAD') {
        $data[] = ''; //空列
        $data[] = 0; //diff
    }

//    var_dump($data);
    $gfile->addCurrencyNumberRow($sheet_id, strtoupper($currency), $row_index, $data);
}

function checkTableExist($table_name)
{
    global $ebrp_db;

    $sql = "show tables like '{$table_name}'";
    $res = $ebrp_db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    if (!is_array($res)) {
        return false;
    }
    return true;
}

function getV3TableDataByMonthAndCurrency($currency, $month)
{
    global $ebrp_db;

    $table_prefix = 'monthly_member_payable_v3';
    $table_name = getTableName($currency, $table_prefix);

    if(!checkTableExist($table_name)) {
        return false;
    }

    $sql = "SELECT sum( current_wallet_add_cashback )/ 10000 AS cashback_add,
	    sum( current_wallet_add_opt )/ 10000 AS opt_add,
	    sum( current_wallet_add_payment_check_cancel )/ 10000 AS check_add,
	    sum( current_wallet_add_payment_paypal_cancel )/ 10000 AS paypal_add,
	    sum( current_wallet_add_payment_other_cancel )/ 10000 AS other_add,
	    sum( current_wallet_reduce_cashback )/ 10000 AS cashback_reduce,
	    sum( current_wallet_reduce_opt )/ 10000 AS opt_reduce,
	    sum( current_wallet_reduce_payment_request ) / 10000 AS shopmall_reduce 
    FROM {$table_name} WHERE `month` = '{$month}' GROUP BY `month`";

    $state = $ebrp_db->query($sql);
    $data = $state->fetchAll(PDO::FETCH_ASSOC);

    return $data[0];
}

function getShopMallDataByMonthAndCurrency($currency, $month)
{
    global $ebrp_db;
    $table_prefix = 'monthly_member_payable_v3';
    $table_name = getTableName($currency, $table_prefix);

    $sql = "SELECT sum( current_shop_pending_add_payment_request )/ 100 AS add_all,
	    sum( current_shop_pending_reduce_payment_paid_check )/ 100 AS reduce_check_paid,
	    sum( current_shop_pending_reduce_payment_paid_paypal )/ 100 AS reduce_paypal_paid,
	    sum( current_shop_pending_reduce_payment_paid_other )/ 100 AS reduce_other_paid,
	    sum( current_shop_pending_reduce_payment_return_check )/ 100 AS reduce_check_failed,
	    sum( current_shop_pending_reduce_payment_return_paypal )/ 100 AS reduce_paypal_failed,
	    sum( current_shop_pending_reduce_payment_return_other )/ 100 AS reduce_other_failed,
	    sum( current_shop_pending_reduce_payment_request_cancel )/ 100 AS reduce_cancel,
	    sum( current_shop_pending_reduce_payment_diff ) / 100 AS reduce_fake,
	    sum( current_bank_add_check ) / 100 AS bank_check_add,
	    sum( current_bank_reduce_check ) / 100 AS bank_check_reduce,
	    sum( current_bank_add_paypal ) / 100 AS bank_paypal_add,
	    sum( current_bank_reduce_payal ) / 100 AS bank_paypal_reduce 
    FROM {$table_name} WHERE `month` = '{$month}' GROUP BY `month`";

    $st = $ebrp_db->query($sql);
    $data = $st->fetchAll(PDO::FETCH_ASSOC);

    return $data[0];

}

function getSpPayPalMonthData($month)
{
    global $sp_db;
    $month_time = strtotime($month);

    $m1 = date('Y-m', $month_time);

    $m2 = date('Y-m', strtotime('+1 months', $month_time));

    $sql = "SELECT currency, sum( amt ) as amount FROM 
    (   
        SELECT substring_index( amount, ' ', - 1 ) AS currency, substring_index( amount, ' ', 1 )+ 0 AS amt 
	    FROM paypal_transactions 
	    WHERE processing LIKE '{$m1}%' AND STATUS <> 'FAILED' 
    ) AS t 
    GROUP BY currency";
    $st = $sp_db->query($sql);
    $paid_data = $st->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT currency, sum( amt ) as amount FROM
	(
	    SELECT substring_index( amount, ' ', - 1 ) AS currency, substring_index( amount, ' ', 1 )+ 0 AS amt 
	    FROM paypal_transactions 
	    WHERE created >= '{$m1}-01' AND created < '{$m2}-01' AND STATUS = 'RETURNED' 
	) AS t 
    GROUP BY currency"; //AF
    $st = $sp_db->query($sql);
    $return_data = $st->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($paid_data as $item) {
        $data[$item['currency']]['paid_amount'] =  $item['amount'];
    }

    foreach ($return_data as $item) {
        $data[$item['currency']]['return_amount'] = $item['amount'];
    }

    return $data;
}

function getSpCheckMonthData($month)
{
    global $sp_db;
    $month_time = strtotime($month);

    $m1 = date('m/%/y', $month_time);

    $m2 = date('Y-m', $month_time);

    $m3 = date('Y-m', strtotime('+1 months', $month_time));
    $sql = "SELECT sum( pay ) as `pay` FROM
	(
	    SELECT DISTINCT payment_reference,
	    IF ( amount + 0.00 < 0, amount + 0.00, 0 ) AS pay 
	    FROM check_transactions 
	    WHERE due_date LIKE '{$m1}' AND payment_reference <> '' 
	) AS t";

    $st = $sp_db->query($sql);
    $paid_data = $st->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT sum( pay ) as `pay` FROM
	(
	    SELECT DISTINCT payment_reference,
	    IF ( amount + 0.00 < 0, amount + 0.00, 0 ) AS pay 
	    FROM check_transactions 
	    WHERE modified >= '{$m2}-01' AND modified < '{$m3}-01' AND STATUS = 'Mark_Refund' AND payment_reference <> '' 
	) AS t";

    $st = $sp_db->query($sql);
    $return_data = $st->fetchAll(PDO::FETCH_ASSOC);

    $paid_amount = 0;
    if ($paid_data) {
        $paid_amount = 0 - $paid_data[0]['pay'];
    }

    $return_amount = 0;
    if ($return_data) {
        $return_amount = 0 - $return_data[0]['pay'];
    }

    return compact('paid_amount', 'return_amount');
}

function getSpAchMonthData($month)
{
    global $sp_db;
    $month_time = strtotime($month);

    $m1 = date('M-Y', $month_time);

    $m2 = date('Y-m', $month_time);

    $m3 = date('Y-m', strtotime('+1 months', $month_time));

    $sql = "SELECT currency, sum( REPLACE ( amount, ',', '' )+ 0.00 ) as amount
    FROM wiretransfer_transactions 
    WHERE `date` LIKE '%{$m1}' AND type = 'ach' 
    GROUP BY currency"; //USD!AH

    $st = $sp_db->query($sql);
    $paid_data = $st->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT currency, sum( REPLACE ( amount, ',', '' )+ 0.00 ) as amount
    FROM wiretransfer_transactions 
    WHERE modified >= '{$m2}-01' AND modified < '{$m3}-01' AND type = 'ach' AND STATUS = 'Mark_Refund'"; //USD!AI

    $st = $sp_db->query($sql);
    $return_data = $st->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($paid_data as $item) {
        $data[$item['currency']]['paid_amount'] =  $item['amount'];
    }

    foreach ($return_data as $item) {
        $data[$item['currency']]['return_amount'] = $item['amount'];
    }

    return $data;
}

function getSpHsbcMonthData($month)
{
    global $sp_db;
    $before_month = date('Y-m-01', strtotime('-1 months', strtotime($month)));
    $month = date("m/Y", strtotime($month));

    $sql = "SELECT sum( amount ) as amount
    FROM hsbc_echeck_transactions 
    WHERE send_date like '%{$month}' and `created` > '{$before_month}'"; //HKD!AJ

    $data = $sp_db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if ($data) {
        return $data[0]['amount'];
    }
    return 0;
}

//TODO 慈善怎么获取
function getSpCharityData($month)
{
    global $sp_db;

}

function getTableName($currency, $table_prefix)
{
    if (strtoupper($currency) == 'USD') {
        $table_name = $table_prefix;
    } else {
        $table_name = $table_prefix . '_' . strtolower($currency);
    }
    return $table_name;
}

function getSheets(GSheet $gFile): array
{
    $data = [];
    $sheets = $gFile->getSheets();
    foreach ($sheets as $sheet) {
        if (!preg_match('/^[A-Z]{3}$/', $sheet->getProperties()->getTitle())) {
            continue;
        }
        $data[$sheet->getProperties()->getTitle()] = [
            'id' => $sheet->getProperties()->getSheetId(),
            'append_row_index' => $gFile->getSheetLastRow($sheet)
        ];
    }
    return $data;
}

