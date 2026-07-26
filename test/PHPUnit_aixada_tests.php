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

// =============================================

    #[TestDox('Comprobar función DBWrap->get_error() de ./php/inc/database.php')]
    public function test_db_get_error(): void
    {
        // Instanciamos la db de Aixada
        $db_aixada = $this->get_db();

        // Ejecutar en vacio
        $errorMsg = $db_aixada->get_error();
        $this->assertEmpty($errorMsg);

        // Ejecitar una SELECT, no responde error.
        $rs = $db_aixada->Execute('SELECT id FROM aixada_user where id=1;');
        $errorMsg = $db_aixada->get_error();
        $this->assertEmpty($errorMsg);

        try {
            $rs = $db_aixada->Execute('SELECT id FROM aixada_userr where id=1;');
        } catch (Exception $e) {
            $errorMsg = $db_aixada->get_error();
            // error_log('$errorMsg ="' . $errorMsg . '"');
            $this->assertNotEmpty($errorMsg);
            $this->assertEquals($e->getMessage(), $errorMsg);
        }
    }

    #[TestDox('Comprobar valor DBWrap->current_query_SQL de ./php/inc/database.php vía ./php/lib/table_with_ref.php')]
    public function test_foreign_key_manager(): void
    {
        // Instanciamos la db de Aixada
        $db_aixada = $this->get_db();

        require_once __ROOT__ . 'php/lib/table_with_ref.php';

        $fkm = new foreign_key_manager("aixada_version");
        $this->assertEquals('SELECT * FROM aixada_version LIMIT 1', $db_aixada->current_query_SQL);
        try {
            $fkm = new foreign_key_manager("aixada_userr");
        } catch (Exception $e) {
            // error_log('$db_aixada->current_query_SQL ="' . $db_aixada->current_query_SQL . '"');
            $this->assertEquals('SHOW CREATE TABLE aixada_userr', $db_aixada->current_query_SQL);
        }
    }
    
    /* -----------------------
     * Para abrir la base de datos y mantener una buena gestión output buffers.
     * -----------------------
     */
    private function get_db()
    {
        $already_required = defined('__ROOT__');

        if(! $already_required) {
            $f_root = dirname(dirname(__FILE__)). '/';

            // Los modulos de Aixada usan las constantes DS i __ROOT__
            define('DS', DIRECTORY_SEPARATOR);
            define('__ROOT__', $f_root);

            // database.php ya se encarga de hacer los require_once que necesiste
            require_once $f_root . "php/inc/database.php";
        }

        $db = DBWrap::get_instance();

        // php/inc/database.php abre con ob_start();
        // Lo cerramos para que PHPUnit concluya bien
        if(! $already_required) {
            ob_end_flush();
        }

        return $db;
    }

}