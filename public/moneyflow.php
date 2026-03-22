php
<?php
$html = file_get_contents("https://life.moneyflow.be/stock.php?stock_id=6801684");
$doc = new DOMDocument();
@$doc->loadHTML($html);

$xpath = new DOMXPath($doc);
// We zoeken specifiek naar alle 'tr' elementen
$rows = $xpath->query("//tr");

$results = [];

foreach ($rows as $row) {
    // In plaats van getElementsByTagName op de rij, 
    // zoeken we naar de 'td' elementen die directe kinderen zijn van deze 'tr'
    $cols = $xpath->query("td", $row); 

    if ($cols->length >= 2) {
        $datum = trim($cols->item(0)->nodeValue);
        $koers = trim($cols->item(1)->nodeValue);

        // Check of de eerste kolom een datum is (dd-mm-jjjj)
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $datum)) {
            $results[] = [
                'datum' => $datum,
                'koers' => str_replace(',', '.', $koers) // Komma naar punt voor berekeningen
            ];
        }
    }
}

// Nu kun je de array wél veilig dumpen
print_r($results); 
?>