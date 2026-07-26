<?php
function sheet_import($file_path) {
    try {
        
        $Spreadsheet = new SpreadsheetReader($file_path);

        $Sheets = $Spreadsheet -> Sheets();
        $v_shets = [];
        foreach ($Sheets as $Index => $Name) {
            $v_shet = [];
            $Spreadsheet -> ChangeSheet($Index);
            foreach ($Spreadsheet as $Key => $Row) {
                $v_row = [];
                foreach($Row as $k => $v) {
                    if ($v !== '') {
                        $v_row[$k]=$v;
                    }
                }
                if (count($v_row) > 0) {
                    $v_shet[$Key]=$v_row;
                }
            }
            if (count($v_shet) > 0) {
                $v_shets[$Name]=$v_shet;
            }
        }
        return $v_shets;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return [ $e->getMessage() ];
    }
}
