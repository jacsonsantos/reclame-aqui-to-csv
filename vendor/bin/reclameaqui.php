<?php

require_once dirname(__DIR__) . "/autoload.php";

use ReclameAqui\Console\ReclameAquiCrawler as Crawler;
use ReclameAqui\Console\ReclameAquiMatch as RMatch;

echo "=============== Runnig crawler Reclame Aqui ===============" . \PHP_EOL;

echo "LINK Reclame Aqui: ". $argv[1] . \PHP_EOL;

$match = new RMatch($argv[1]);

$companyId = $match->extractCompanyId()->getCompanyId();
echo "Company ID: ". $companyId . \PHP_EOL;

$crawler = new Crawler($companyId);
$crawler->run();

echo "=============== Completed crawler Reclame Aqui ===============" . \PHP_EOL;
