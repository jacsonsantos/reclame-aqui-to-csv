<?php

namespace ReclameAqui\Console;

use ReclameAqui\Console\ReclameAquiMatch as RMatch;

class ReclameAquiCrawler
{
    const LIMIT = 10;

    const STATUS = 'PENDING';

    const EVALUATED = 'bool:false';

    const HEADER = ['companyName', 'solved', 'userState', 'userCity', 'id', 'created', 'modified', 'read', 'problemType', 'otherProblemType', 'productType', 'otherProductType', 'status', 'companyShortname', 'title', 'description', 'hasReply', 'category', 'slug', 'url', 'full_description'];

    private $offset = 0;

    private $last_page = 1;

    private $total_complains_found = 0;

    private $total_complains_saved = 0;

    private $limit_page = 0;
    
    private $companyId;

    private $path_dataset;

    private $file_path_real = '';

    private $categories = [];

    private $products = [];
    
    private $problems = [];

    public function __construct($companyId, $path_dataset = '/app/dataset', $limit_page = 10)
    {
        $this->companyId = $companyId;
        $this->path_dataset = $path_dataset;
        $this->limit_page = $limit_page;
    }

    public function run()
    {
        $response = $this->request();

        $company = $this->normalizeResponseToCompany($response);
        $complains = $this->normalizeResponseToComplains($response);

        $this->categories = $complains['categories'] ?? [];
        $this->products   = $complains['products'] ?? [];
        $this->problems   = $complains['problems'] ?? [];

        if ($this->last_page == 1) {
            $this->generateFileCsv($company['shortname']);
        }
        $this->calculeNextPage($complains['count']);
        $this->saveComplains($complains['data']);

        if ($this->isCompletedExtraction() === false) {
            sleep(2);
            $this->run();
        }
    }

    private function request()
    {
        $query_string = http_build_query([
            'company' => $this->companyId,
            'status' => self::STATUS,
            'evaluated' => self::EVALUATED
        ]);

        $url = "https://iosearch.reclameaqui.com.br/raichu-io-site-search-v1/query/companyComplains/10/{$this->offset}?$query_string";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: _abck=6C69FEB2C73A7D07F385FAD692708ADC~-1~YAAQyxIuFzXtMqiBAQAAtQp0tQgldxUouDS8cU1leyvcnA80mszWvOuWxxJIFiQgosuglwOYWOspdNUtRlF2AWIsK3ei5k8vwsD9kbEYFbPoZcUzBh/1EcmJrMumEX7goPJWhpMnxXFrtsZA3KfASgLCeP4gY4ScFed5rFJs00m24/ydIMfvldPN45TS4mpRrrdZWTJOFaQSF/b21qWx4/SG57pQkhoz4qSDt+DToLnNf0P53oe1wlLQNMAkPuk7w/2HJjaMycIlbH87dXbocm++tHOhlDf4nsE1lYTZVA77PAkbXbN6AHComPUSrSnpqeenLU1MBvjMzlWyfhhYx4D+HOlQRW5rdLbBd4UpKnZuu3tWmuIGpdRa82W0tMaNXw==~-1~-1~-1; ak_bmsc=33B5B32B82F61A80EEE44A0E2F40AC20~000000000000000000000000000000~YAAQyxIuFzbtMqiBAQAAtQp0tRDjzyU8cYBEvdG5GZ1WhroWZuGdF+CQ88JzK3VXDKB3FM3tvTLVN3VEyv2T5kLtm4MKOaFN7zzalbUnVIwdPE4kfXBLj8ZrVc2d/5Ysc6pP6TDQ3LRT5b49fUPfs/uaeXIluVICAHpXXAPVsXYpMPTep01ugJjtBOMneXBYeKl+5NfmUfKKnm4DT2DqcWuZNLU/14IPAq2y6uBY8qVN5nmupPBNNDX2QN2i7rnz201y4j7o1UTkoY2dnIuITpRY+LVjX/2D4j8w7LioLkF8F4CIk+RuEAsve7riBTpQbxh725ITrroZlqpp6ti8nGhcqBwx7HhEN/6ae51WUDhD2/Di6riVqOm5twdBSGFsWmBRSg==; bm_sz=725892D4819E501E1B3AC2A01D86964C~YAAQyxIuFzftMqiBAQAAtQp0tRCsvr4Q4y03Eu1pcRP7rgF2NMjylELhyWcr46Y0rsD2fFtUo+vooZZJdVK1oVNiFUlU0OT+QshhzI23u8h0FTFAkVsEP/jjmYQZPRZwa2aGomueyNcwtm8fslPM1mXKEOkKxDb62Y71A02qkpz9u5c58q/MxSOYzdM02aeCrQjqpxgcsr0kJ1xhwo7DMbgZaEpwmZRm64j3jfAgrtWQQPtNrRZuJnBEA+WURu/iYbpXMkijg97tiQQbT+gCxn74FGMwL969OCD6tW8EnWVu2x0swHk5WO9h5g==~4474163~4274487; bm_sv=4542BC84F5D934A9486FB0CF9F746B78~YAAQyxIuF5iEM6iBAQAA84OttRDYRpJ+dAXBLdwkkUOf8pUuqIp8YYMOJDQiok6rswAYunOIt9IAs4aR/lLOZaZQ3pHm6GR8KSgNBdVg8dXYghs5PF/J6yDaUzSjY4/xripgZCv3ZSYl51OLZ71rVhIQsRhNN7pOFoFmuRYytMELgAP/tkuNfsV//Vqn/rY0OofQbDKPZb7qB6OChTW9oOjX96lvbznGNbkXBhntCdCS14ZPW5baUYfgNUdmhJKwMWkjNsXltZI=~1'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    private function normalizeResponseToCompany($response)
    {
        if (is_null($response)) {
            return [];
        }

        $suggestion = $response['suggestion'];
        return [
            'id' => $suggestion['id'],
            'companyName' => $suggestion['companyName'],
            'shortname' => $suggestion['shortname'],
            'created' => $suggestion['created'],
        ];
    }

    private function normalizeResponseToComplains($response)
    {
        if (is_null($response)) {
            return [];
        }

        $complainResult = $response['complainResult'];
        return $complainResult['complains'];
    }

    private function calculeNextPage(int $total_found)
    {
        if ($total_found < 1) {
            return;
        }

        $this->total_complains_found = $total_found;
        $this->last_page = ceil($total_found / self::LIMIT);
    }

    private function generateFileCsv($filename)
    {
        $this->file_path_real = "{$this->path_dataset}/{$filename}.csv";
        file_put_contents($this->file_path_real, implode(',', self::HEADER) . \PHP_EOL);
    }

    private function saveComplains(array $complains)
    {
        $this->total_saved += count($complains);
        $this->offset += self::LIMIT;
        $page = $this->offset / self::LIMIT;
        
        foreach ($complains as $complain) {
            $this->appendLineCsv($complain);
        }
    }

    private function isCompletedExtraction()
    {
        return (
            ($this->offset/self::LIMIT) >= $this->limit_page ||
            ($this->offset/self::LIMIT) >= $this->last_page ||
            $this->total_complains_found == $this->total_complains_saved
        );
    }

    private function appendLineCsv($complain)
    {
        $line = [];
        foreach (self::HEADER as $column) {
            $value = $complain[$column] ?? null;

            if (in_array($column, ['title', 'description'])) {
                $value = '"'. $value .'"';
            }

            if ($value && in_array($column, ['problemType', 'productType'])) {
                $value = $this->$column($value);
            }

            $line[] = $value;
        }

        $slug = $this->getSlugComplain($complain);
        $line[] = $slug;
        $url = $this->getLinkComplain($complain['companyShortname'], $slug);
        $line[] = $url;

        try {
            $match = new RMatch($url);
            $description = $match->extractComplainDescription()->getComplainDescription();
            $line[] = '"'. $description .'"';
        } catch (\Exception $e) {
            $line[] = null;
        }

        file_put_contents($this->file_path_real, implode(',', $line) . \PHP_EOL, \FILE_APPEND);
    }

    public function problemType($id)
    {
        return $this->getItemFilteredById($this->problems, $id);
    }

    public function productType($id)
    {
        return $this->getItemFilteredById($this->products, $id);
    }

    private function getItemFilteredById($list, $id, $fieldReturn = 'name')
    {
        $filtered = array_filter($list, function($item) use ($id) {
            return $item['id'] == $id;	
        });

        $product = current($filtered);
        return $product[$fieldReturn];
    }

    private function getSlugComplain($complain)
    {
        return $this->slugify($complain['title']) . '_' . $complain['id'];
    }

    private function slugify($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    private function getLinkComplain($companyShortname, $slug)
    {
        return "https://www.reclameaqui.com.br/$companyShortname/$slug/";
    }
}