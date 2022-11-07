```
php -a
require "vendor/autoload.php";
require "module/Application/src/Elastic/retroactiveIndexingConfig.php";
require "module/Application/src/Elastic/ElasticRetroactiveIndexing.php";
Application\Elastic\ElasticRetroactiveIndexing::createRetroactiveIndexing($retroactiveIndexingConfig)->doIndexing();
```