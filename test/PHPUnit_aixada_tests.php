<?php
// ./vendor/bin/phpunit test/PHPUnit_aixada_tests.php --testdox
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Comprobar algunas partes del código de Aixada con PHPUnit')]
class PHPUnit_aixada_tests extends TestCase
{
 
    #[TestDox('Comprobar importación de hojas de cálculo con ./external/php74/spreadsheet-reader')]
    public function test_spreadsheet_reader(): void
    {
        $base_folder = dirname(dirname(__FILE__)) . '/';
        require_once $base_folder . 'external/php74/spreadsheet-reader/SpreadsheetReader.php';
        require_once $base_folder . 'external/php74/spreadsheet-reader/php-excel-reader/excel_reader2.php';
        require_once 'test-spreadsheet-reader/sheet_import.php';
        function i_sheet_import($file_path) {
            return sheet_import('test/test-spreadsheet-reader/test_files/' . $file_path);
        }

        $from_excel = [ 'Hoja1' => [ 
                1 => [ 0 => '1 to 9', 1 => 123456789.0, ],
                2 => [ 0 => 'Currency (Euro)', 1 => '5.43 €', ], 
                3 => [ 0 => 'Currency (UK pound) +0,111', 1 => '£5.54', ],
                4 => [ 0 => 'Currency (US dollar) +0,111', 1 => '$5.65', ],
                5 => [ 0 => 'Normal (2 decimal) +0,111', 1 => '5.77', ],
                6 => [ 2 => 'C6', ],
                7 => [ 0 => 'àèìòù', 1 => 'ÀÈÌÒÙ', ],
                8 => [ 0 => 'áéíóú', 1 => 'ÁÉÍÓÚ', ],
                9 => [ 0 => 'aeiou', 1 => 'AEIOU', ],
                10 => [ 0 => 'çñäïü', 1 => 'ÇÑÄÏÜ', ],
                12 => [ 0 => 'Christmas 2026', 1 => '12/25/2026', ],
                13 => [ 0 => 'Last day of the past year', 1 => '12/31/2025', ],
                14 => [ 0 => 'Saint Stephen\'s Day', 1 => '12/26/2026', ],
                16 => [ 4 => 'E16', ] ],
             'Hoja2' => [ 2 => [ 1 => 'B2', ] ] ];
        $from_ods = [ 'Hoja1' => [
                0 => [ 0 => '1 to 9', 1 => '123456789', ], 
                1 => [ 0 => 'Currency (Euro)', 1 => '5,43 €', ], 
                2 => [ 0 => 'Currency (UK pound) +0,111', 1 => '£5,54', ], 
                3 => [ 0 => 'Currency (US dollar) +0,111', 1 => '$5,65', ], 
                4 => [ 0 => 'Normal (2 decimal) +0,111', 1 => '5,77', ], 
                5 => [ 2 => 'C6', ], 
                6 => [ 0 => 'àèìòù', 1 => 'ÀÈÌÒÙ', ], 
                7 => [ 0 => 'áéíóú', 1 => 'ÁÉÍÓÚ', ], 
                8 => [ 0 => 'aeiou', 1 => 'AEIOU', ], 
                9 => [ 0 => 'çñäïü', 1 => 'ÇÑÄÏÜ', ], 
                11 => [ 0 => 'Christmas 2026', 1 => '25/12/2026', ], 
                12 => [ 0 => 'Last day of the past year', 1 => '31/12/2025', ], 
                13 => [ 0 => 'Saint Stephen\'s Day', 1 => '26/12/2026', ], 
                15 => [ 4 => 'E16', ] ],
             'Hoja2' => [ 1 => [ 1 => 'B2' ] ] ];
        $from_csv_sheet = [ 
            0 => [ 0 => '1 to 9', 1 => '123456789', ], 
            1 => [ 0 => 'Currency (Euro)', 1 => '5,43 €', ], 
            2 => [ 0 => 'Currency (UK pound) +0,111', 1 => '£5,54', ], 
            3 => [ 0 => 'Currency (US dollar) +0,111', 1 => '$5,65', ], 
            4 => [ 0 => 'Normal (2 decimal) +0,111', 1 => '5,77', ], 
            5 => [ 2 => 'C6', ], 
            6 => [ 0 => 'àèìòù', 1 => 'ÀÈÌÒÙ', ], 
            7 => [ 0 => 'áéíóú', 1 => 'ÁÉÍÓÚ', ], 
            8 => [ 0 => 'aeiou', 1 => 'AEIOU', ], 
            9 => [ 0 => 'çñäïü', 1 => 'ÇÑÄÏÜ', ], 
            11 => [ 0 => 'Christmas 2026', 1 => '12/25/2026', ], 
            12 => [ 0 => 'Last day of the past year', 1 => '12/31/2025', ], 
            13 => [ 0 => 'Saint Stephen\'s Day', 1 => '12/26/2026', ], 
            15 => [ 4 => 'E16', ], ];

        // Provamos los distintos archivos
        $this->assertEquals(['sheet.csv' => $from_csv_sheet ], i_sheet_import('sheet.csv'));
        
        $this->assertEquals(['sheet.tsv' => $from_csv_sheet ], i_sheet_import('sheet.tsv'));

        $this->assertEquals($from_ods, i_sheet_import('sheet.ods'));
        
        $this->assertEquals($from_excel, i_sheet_import('sheet.xls'));
    
        $this->assertEquals($from_excel, i_sheet_import('sheet.xlsx'));

    }
}
