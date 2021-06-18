<?php


namespace MeiKaiGsuit\GSheet;


use Google_Service_Sheets_Sheet;

class GSheet
{
    protected $serv;

    protected $file_id;

    public function __construct(\Google_Client $client, $file_id)
    {
        $this->serv = new \Google_Service_Sheets($client);
        $this->file_id = $file_id;
    }

    /**
     * 获取一个表格文件的所有 Sheets
     * @return array
     **/
    public function getSheets(): array
    {
        return $this->serv->spreadsheets->get($this->file_id)->getSheets();
    }

    /**
     * 获取表格的第一个空行所在行
     * @param Google_Service_Sheets_Sheet $sheet
     * @param string $identify_col 识别的列，这一列必须所有行都有值
     * @return int
    */
    public function getSheetLastRow(Google_Service_Sheets_Sheet $sheet, string $identify_col = 'A'): int
    {
        $range = $sheet->getProperties()->getTitle() . "!{$identify_col}1:{$identify_col}";

        $postBody = new \Google_Service_Sheets_ValueRange();
        $postBody->setMajorDimension('ROWS');
        $postBody->setValues([]);

        $optParams = [
            'insertDataOption' => 'INSERT_ROWS',
            'valueInputOption' => 'USER_ENTERED'
        ];
        $response = $this->serv->spreadsheets_values->append($this->file_id, $range, $postBody, $optParams);

//      $response->getTableRange(); //main table, can get last column

        //return USD!A2333
        $updateCell = $response->getUpdates()->getUpdatedRange();

        return (int)str_replace($sheet->getProperties()->getTitle() . "!A", '', $updateCell);
    }

    /**
     * 添加一行金额的数据
     *
    */
    public function addCurrencyNumberRow($sheet_id, $currency, $row_index, $data, $withStyle = [], $col_offset = 0)
    {
        $cellHandle = new GSheetCell();
        $cells = [];
        foreach ($data as $index => $item) {
            $cellPos = [
                GSheetCell::DIMENSION_COLUMNS => $index + 1 + $col_offset,
                GSheetCell::DIMENSION_ROWS => $row_index
            ];

            if (is_array($item)) {
                $nf = null;
                if (isset($item['number_format'])) {
                    $nf = new \Google_Service_Sheets_NumberFormat($item['number_format']);
                }
                $cell = $cellHandle->addCell($item['value'], $cellPos, $nf);
            } elseif (!is_numeric($item) && strpos($item, '=') !== 0) {
                $cell = $cellHandle->addCell($item, $cellPos);
            } else {
                $cell = $cellHandle->addCurrencyNumberCell($item, $currency, $cellPos);
            }
            //第一列设置居中
            if ($index == 0) {
                $cell->getUserEnteredFormat()->setHorizontalAlignment('CENTER');
            }
            //都加上边框
            $cell->getUserEnteredFormat()->setBorders($cellHandle::getSolidBorders());
            //加上背景色
            if (in_array(colNum2Letter($index+1), ['B','AB'])) {
                $cell->getUserEnteredFormat()->setBackgroundColor($cellHandle::getOrangeColor());
            }

            $cells[] = $cell;
        }

        //第一列设置居中
        $cells[0]->getUserEnteredFormat()->setHorizontalAlignment('CENTER');

        $rawData = new \Google_Service_Sheets_RowData();
        $rawData->setValues($cells);

        $cellsReq = new \Google_Service_Sheets_AppendCellsRequest();
        $cellsReq->setFields(implode(',', [GSheetCell::FIELDS_UPDATE_CELL_VALUE , GSheetCell::FIELDS_UPDATE_CELL_FORMAT]));
        $cellsReq->setRows([$rawData]);
        $cellsReq->setSheetId($sheet_id);

        $request = new \Google_Service_Sheets_Request();
        $request->setAppendCells($cellsReq);

        $postBody = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
        $postBody->setRequests([$request]);

        $response = $this->serv->spreadsheets->batchUpdate($this->file_id, $postBody);

        var_dump($response->getSpreadsheetId());
    }

    /**
     * 更新表格中的多个元素
     * 注：如果只是修改元素的格式或者样式，请指定对应Fields，否则不传入元素值会把值置空
     * @todo 暂时不需要此功能，作为备忘先写下注意事项
     * */
    public function updateCells()
    {

    }
}