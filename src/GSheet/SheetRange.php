<?php


namespace MeiKaiGsuit\GSheet;


use \Google_Service_Sheets_GridRange as GridRange;

class SheetRange
{
    private $sheet;
    private $range;

    public function __construct(\Google_Service_Sheets_Sheet $sheet, string $range)
    {
        $this->sheet = $sheet;
        $this->range = $range;
    }

    public function setRange(string $range): self
    {
        $this->range = $range;
        return $this;
    }

    public function getRange(): string
    {
        return sprintf("%s!%s", $this->sheet->getProperties()->getTitle(), $this->range);
    }

    public function getGridRange() : GridRange
    {
        return self::strRange2GridRange($this->range, $this->sheet->getProperties()->getSheetId());
    }

    public function getRangeFromGridRange(GridRange $range): string
    {
        if ($range->getSheetId() != $this->sheet->getProperties()->getSheetId()) {
            throw new \InvalidArgumentException("range.sheet_id[{$range->getSheetId()}] not belong to "
                . "sheet {$this->sheet->getProperties()->getTitle()}[{$this->sheet->getProperties()->getSheetId()}]");
        }
        return self::gridRange2StrRange($range, $this->sheet->getProperties()->getTitle());
    }

    /**
     * 字符定义的Range转成GridRange实例
     * GridRange的值范围符合左开右闭规则 (0,1]
     * 例子：
     *  1. A1->(col:0,1 row:0,1),
     *  2. A1:A3->(col:0,1 row:0,3)
     *  3. B2:C4->(col:1,3 row:1,4)
     * @param string $range
     * @param int $sheet_id
     * @return GridRange
     */
    public static function strRange2GridRange(string $range, int $sheet_id): GridRange
    {
        $range = strtoupper($range); //预先转大写
        $matches = [];
        $range_regx = '/^(?<sc>[A-Z]+)(?<sr>[0-9]?)(:(?<ec>[A-Z]+)(?<er>[0-9]?))?$/';

        preg_match($range_regx, $range, $matches);

        $gRange = new GridRange();
        $gRange->setSheetId($sheet_id);

        if (empty($matches)) {
            return $gRange;
        }

        $gRange->setStartColumnIndex(colLetter2Num($matches['sc'])-1);
        if (isset($matches['sr'])) {
            $gRange->setStartRowIndex($matches['sr']-1);
        }

        if (isset($matches['ec'])) {
            $gRange->setEndColumnIndex(colLetter2Num($matches['ec'])-1);
        }
        if (isset($matches['er'])) {
            $gRange->setEndRowIndex($matches['er']);
        }

        $start_col_index = colLetter2Num($matches['sc'])-1;

        $start_row_index = $matches['sr']-1;

        if (isset($matches['end'])) {
            $end_col_index = colLetter2Num($matches['ec']);
            $end_row_index = $matches['er'];
        } else {
            $end_col_index = colLetter2Num($matches['sc']);
            $end_row_index = $matches['er'];
        }

        $gRange = new GridRange();
        $gRange->setSheetId($sheet_id);
        $gRange->setStartColumnIndex($start_col_index);
        $gRange->setEndColumnIndex($end_col_index);
        $gRange->setStartRowIndex($start_row_index);
        $gRange->setEndRowIndex($end_row_index);

        return $gRange;
    }

    /**
     * GridRange实例转换为字符串
     */
    public static function gridRange2StrRange(GridRange $range, string $sheet_title): string
    {
        $startRange = sprintf("%s%s", colNum2Letter($range->getStartColumnIndex()+1),
            $range->getStartRowIndex());

        $endRange = "";
        if ($range->getEndColumnIndex()-1 != $range->getStartColumnIndex()
            || $range->getEndRowIndex()-1 != $range->getStartRowIndex()) {

            $endRange = sprintf(":%s%s", colNum2Letter($range->getEndColumnIndex()+1),
                $range->getEndRowIndex()
            );
        }

        return sprintf("%s!%s%s", $sheet_title, $startRange, $endRange);
    }

    public function __toString(): string
    {
        return $this->getRange();
    }
}