<?php

namespace ReclameAqui\Console;

class ReclameAquiMatch
{
    private $html;

    private $companyId;

    private $complainDescription;

    public function __construct(string $link)
    {
        $this->html = $this->request($link);
    }

    private function request($link)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
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

        return $response;
    }

    public function extractCompanyId()
    {
        preg_match('/<a class="([^"]*)" href="\/reclamar\/(.*)\/\"(.*)id="cta-header-complain">/', $this->html, $matches);
        $this->companyId = $matches[2];

        return $this;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function extractComplainDescription()
    {
        preg_match('/<p data-testid="complaint-description" class="(.*?)">(.*?)<\/p><div id="widget-fixed"/', $this->html, $matches);
        $this->complainDescription = $matches[2];

        return $this;
    }

    public function getComplainDescription()
    {
        return $this->complainDescription;
    }
}
