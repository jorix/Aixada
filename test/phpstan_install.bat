REM Instala composer.json
    composer init --no-interaction 
    
REM Instala verificador phpstan
    composer require --dev phpstan/phpstan

REM Executar verificador
    vendor/bin/phpstan analyse *.php ./php --autoload-file=./local_config/config.php --no-progress --level 0 --memory-limit 500M > phpstan_diagnostic-php.log 2>&1
    
    vendor/bin/phpstan analyse --no-progress --memory-limit 500M > phpstan_diagnostic.log 2>&1
