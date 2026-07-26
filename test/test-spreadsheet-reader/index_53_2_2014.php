<?php

$base_folder = dirname(dirname(dirname(__FILE__))) . '/';
require_once $base_folder . 'external/php53_2/spreadsheet-reader_2014/SpreadsheetReader.php';
require_once $base_folder . 'external/php53_2/spreadsheet-reader_2014/php-excel-reader/excel_reader2.php';

require_once 'sheet_import.php';

function sheet_import_e($file_path) {
    ?>
    <b><?php echo $file_path ?></b><br>
    <pre>
<?php echo var_export(sheet_import($file_path)); ?>
    </pre>
    <hr>
    <?php
}
// Con PHP 5.5 o anterior tiene problemas de conversión de caracteres
// Desde PHP 5.6 funciona bien hasta 7.3

echo '<h3> PHP ' . PHP_VERSION . '</h3><hr>';

sheet_import_e('test_files/sheet.xlsx'); 
sheet_import_e('test_files/sheet.xls');
sheet_import_e('test_files/sheet.ods');
sheet_import_e('test_files/sheet.csv');
sheet_import_e('test_files/sheet.tsv');