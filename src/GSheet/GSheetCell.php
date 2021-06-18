<?php
declare(strict_types=1);

namespace MeiKaiGsuit\GSheet;

use Google_Service_Sheets_Borders;
use Google_Service_Sheets_Color;
use \Google_Service_Sheets_NumberFormat as NumberFormat;
use \Google_Service_Sheets_CellData as CellData;

class GSheetCell
{
    const CURRENCY_USD = 'USD';
    const CURRENCY_AUD = 'AUD';
    const CURRENCY_GBP = 'GBP';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_JPY = 'JPY';
    const CURRENCY_CNY = 'CNY';
    const CURRENCY_CAD = 'CAD';
    const CURRENCY_KRW = 'KRW';
    const CURRENCY_RUB = 'RUB';
    const CURRENCY_HKD = 'HKD';
    const CURRENCY_TWD = 'TWD';

    const CURRENCY_NUMBER_FORMAT = '#,##0.00';

    const CURRENCY_USD_SYMBOL = '"$"[number]';
    const CURRENCY_AUD_SYMBOL = '"AU$"[number]';
    const CURRENCY_GBP_SYMBOL = '"£"[number]';
    const CURRENCY_EUR_SYMBOL = '"€"[number]';
    const CURRENCY_JPY_SYMBOL = '"¥"[number]';
    const CURRENCY_CNY_SYMBOL = '"¥"[number]';
    const CURRENCY_CAD_SYMBOL = '"CAD $"[number]';
    const CURRENCY_KRW_SYMBOL = '"₩"[number]';
    const CURRENCY_RUB_SYMBOL = '[number]" ₽"';
    const CURRENCY_HKD_SYMBOL = '"$"[number]';
    const CURRENCY_TWD_SYMBOL = '"$"[number]';

    const FIELDS_NUMBER_FORMAT_TYPE_CURRENCY   = 'CURRENCY';
    const FIELDS_NUMBER_FORMAT_TYPE_TEXT       = 'TEXT';
    const FIELDS_NUMBER_FORMAT_TYPE_NUMBER     = 'NUMBER';
    const FIELDS_NUMBER_FORMAT_TYPE_PERCENT    = 'PERCENT';
    const FIELDS_NUMBER_FORMAT_TYPE_DATE       = 'DATE';
    const FIELDS_NUMBER_FORMAT_TYPE_TIME       = 'TIME';
    const FIELDS_NUMBER_FORMAT_TYPE_DATE_TIME  = 'DATE_TIME';
    const FIELDS_NUMBER_FORMAT_TYPE_SCIENTIFIC = 'SCIENTIFIC';

    const FIELDS_UPDATE_CELL_FORMAT        = 'userEnteredFormat';
    const FIELDS_UPDATE_CELL_NUMBER_FORMAT = 'userEnteredFormat.numberFormat';
    const FIELDS_UPDATE_CELL_BORDER        = 'userEnteredFormat.borders';
    const FIELDS_UPDATE_CELL_VALUE         = 'userEnteredValue';
    const FIELDS_UPDATE_CELL_NUMBER_VALUE  = 'userEnteredValue.numberValue';
    const FIELDS_UPDATE_CELL_STRING_VALUE  = 'userEnteredValue.stringValue';
    const FIELDS_UPDATE_CELL_FORMULA_VALUE = 'userEnteredValue.formulaValue'; //公式值 =MAX(A1:A12)

    // 表格公式关键词宏定义 'BRN', 'ARN', 'BCN', 'ACN', 'CR', 'CC',
    const FORMULA_DEFINED_BEFORE_ROW  = 'BRn';     //往前走n行
    const FORMULA_DEFINED_AFTER_ROW   = 'ARn';       //往后走n行
    const FORMULA_DEFINED_BEFORE_COL  = 'BCn';     //往前走n列
    const FORMULA_DEFINED_AFTER_COL   = 'ACn';      //往后走n列
    const FORMULA_DEFINED_CURRENT_ROW = 'CR';     //当前行
    const FORMULA_DEFINED_CURRENT_COL = 'CC';     //当前列

    const DIMENSION_COLUMNS = 'COLUMNS';
    const DIMENSION_ROWS    = 'ROWS';

    public static $FormulaDefinedOp = [
        self::FORMULA_DEFINED_BEFORE_ROW  => [self::DIMENSION_ROWS,    'minus'],
        self::FORMULA_DEFINED_AFTER_ROW   => [self::DIMENSION_ROWS,    'add'  ],
        self::FORMULA_DEFINED_CURRENT_ROW => [self::DIMENSION_ROWS,    'add'  ],
        self::FORMULA_DEFINED_BEFORE_COL  => [self::DIMENSION_COLUMNS, 'minus'],
        self::FORMULA_DEFINED_AFTER_COL   => [self::DIMENSION_COLUMNS, 'add'  ],
        self::FORMULA_DEFINED_CURRENT_COL => [self::DIMENSION_COLUMNS, 'add'  ]
    ];

    public static $CurrencyMappingToSymbol = [
        self::CURRENCY_AUD => self::CURRENCY_AUD_SYMBOL,
        self::CURRENCY_CAD => self::CURRENCY_CAD_SYMBOL,
        self::CURRENCY_CNY => self::CURRENCY_CNY_SYMBOL,
        self::CURRENCY_EUR => self::CURRENCY_EUR_SYMBOL,
        self::CURRENCY_GBP => self::CURRENCY_GBP_SYMBOL,
        self::CURRENCY_HKD => self::CURRENCY_HKD_SYMBOL,
        self::CURRENCY_JPY => self::CURRENCY_JPY_SYMBOL,
        self::CURRENCY_KRW => self::CURRENCY_KRW_SYMBOL,
        self::CURRENCY_RUB => self::CURRENCY_RUB_SYMBOL,
        self::CURRENCY_TWD => self::CURRENCY_TWD_SYMBOL,
        self::CURRENCY_USD => self::CURRENCY_USD_SYMBOL
    ];

    /**
     * 添加一个金额类表格元素 支持传入公式
     * @param string|int|float $value 元素的值
     * @param string $currency 金额货币定义
     * @param string|array $cellPos 元素在表格的位置，用于解析公式的宏定义
     * @return CellData
    */
    public function addCurrencyNumberCell($value, string $currency, $cellPos = ''): CellData
    {
        if (is_numeric($value)) {
            $value = (float)$value;
        } elseif (empty($cellPos)) {
            throw new \InvalidArgumentException("if value is not number, cellPos must be define");
        } elseif (is_string($value) && strpos($value, '=') !== 0) {
            throw new \InvalidArgumentException("if value is not number, but identify is not, check it [$value]");
        }
        return $this->addCell($value, $cellPos, self::currencyNumberFormat($currency));
    }

    /**
     * 添加一个表格元素，自动识别传入值的类型，支持自定义数字类型格式化 不支持表格样式编辑
     * @param bool|string|float|int $value 元素的值
     * @param array|string $cellPos 元素在表格的位置，用于解析公式的宏定义
     * @param NumberFormat|null $number_format 元素的值格式化
     * @return CellData
    */
    public function addCell($value, $cellPos, NumberFormat $number_format = null): CellData
    {
        $ev = new \Google_Service_Sheets_ExtendedValue();

        if (is_string($value)) {
            if (strpos($value,'=') === 0) {
                $ev->setFormulaValue(self::formulaAnalyze($value, $cellPos));
            } else {
                $ev->setStringValue($value);
            }
        } elseif (is_numeric($value)) {
            $ev->setNumberValue($value);
        } elseif (is_bool($value)) {
            $ev->setBoolValue($value);
        }

        if (is_null($number_format)) {
            $number_format = new NumberFormat(['type' => self::FIELDS_NUMBER_FORMAT_TYPE_TEXT]);
        }

        //TODO test whether $number_format is null be successful
        $format = new \Google_Service_Sheets_CellFormat();
        $format->setNumberFormat($number_format);

        $cellData = new CellData();
        $cellData->setUserEnteredValue($ev);
        $cellData->setUserEnteredFormat($format);
        return $cellData;
    }

    /**
     * 识别公式里包含的宏定义,并解析
     * @param string $formula_val 公式值
     * @param string|array $cellPos 元素所在位置
     * @return string 返回解析后的公式
    */
    public static function formulaAnalyze(string $formula_val, $cellPos): string
    {
        $matches = [];

        if (is_string($cellPos)) {
            $cellPos = self::cellExplode($cellPos);
        }

        preg_match_all('/(?<def>`[A-Z]+[0-9]*`)/', $formula_val, $matches);
        //去重
        $matches = array_unique($matches['def']);

        array_map(function ($v) use (&$formula_val, $cellPos){

            $definedArr = [];
            preg_match('/^`(?<op>[A-Z]+)(?<num>[0-9]*)`$/', $v, $definedArr);

            $opKey = $definedArr['op'] . ($definedArr['num'] ? 'n' : '');

            if (!isset(self::$FormulaDefinedOp[$opKey])) {
                throw new \LogicException("{$opKey} in {$v} is not formula defined key");
            }

            $cellPosStr = self::calCellPos($cellPos, (int)$definedArr['num'], ...self::$FormulaDefinedOp[$opKey]);
            $formula_val = str_replace($v, $cellPosStr, $formula_val);
        }, $matches);

        return $formula_val;
    }

    /**
     * 解析表格元素位置为行列的index值
     * @param string $cell
     * @return array
    */
    public static function cellExplode(string $cell): array
    {
        $cellPos = [];
        preg_match('/^(?<col>[A-Z]+)(?<row>[0-9]+)$/', $cell, $cellPos);

        if (empty($cellPos)) {
            throw new \InvalidArgumentException("{$cell} is invalid cell, example A1");
        }

        $col_index = colLetter2Num($cellPos['col']);
        $row_index = (int)$cellPos['row'];

        return  [self::DIMENSION_ROWS => $row_index, self::DIMENSION_COLUMNS => $col_index];
    }

    /**
     * 根据当前元素某个维度的位置，偏移量计算新的元素位置
     * @param array $cellPos 当前元素位置
     * @param int $offset 偏移量
     * @param string $dimension 偏移轴 行/列
     * @param string $op 偏移方向 +/-
     * @return string 新的元素位置, row 是A,B,C, col是 1,2,3
    */
    public static function calCellPos(array $cellPos, int $offset, string $dimension, string $op): string
    {
        $offset = ($op == 'minus') ? (-$offset) : $offset;
        $cellPos[$dimension] += $offset;

        return ($dimension == self::DIMENSION_COLUMNS)
            ? colNum2Letter($cellPos[self::DIMENSION_COLUMNS])
            : (string)$cellPos[self::DIMENSION_ROWS];
    }

    /**
     * 根据货币设置对应的NumberFormat实例
     * @param string $currency
     * @return NumberFormat
    */
    public static function currencyNumberFormat(string $currency) : NumberFormat
    {
        $symbol = self::$CurrencyMappingToSymbol[$currency] ?? self::CURRENCY_USD_SYMBOL;

        $pattern = str_replace('[number]', self::CURRENCY_NUMBER_FORMAT, $symbol);

        $nf = new NumberFormat();

        $nf->setType(self::FIELDS_NUMBER_FORMAT_TYPE_CURRENCY);
        $nf->setPattern($pattern);

        return $nf;
    }

    /**
     * 获取一个全边框实例
     * @return Google_Service_Sheets_Borders
    */
    public static function getSolidBorders(): Google_Service_Sheets_Borders
    {
        $borders = new Google_Service_Sheets_Borders();
        $solidBorder = new \Google_Service_Sheets_Border();
        $solidBorder->setStyle('SOLID');
        $borders->setBottom($solidBorder);
        $borders->setTop($solidBorder);
        $borders->setLeft($solidBorder);
        $borders->setRight($solidBorder);
        return $borders;
    }

    /**
     * 获取一个橘色背景色的color实例
     * @return Google_Service_Sheets_Color
    */
    public static function getOrangeColor(): Google_Service_Sheets_Color
    {
        $color = new Google_Service_Sheets_Color();
        $color->setRed(242/255);
        $color->setGreen(176/255);
        $color->setBlue(134/255);
        $color->setAlpha(1);
        return $color;
    }
}