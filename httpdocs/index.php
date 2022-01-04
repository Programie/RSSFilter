<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

require_once __DIR__ . "/../vendor/autoload.php";

$url = $_GET["url"] ?? null;
$filter = $_GET["filter"] ?? "";

if ($url === null or $url === "") {
    http_response_code(400);
    echo "URL missing!";
    exit;
}

if (!is_array($filter)) {
    $filter = [$filter];
}

$client = new Client([
    RequestOptions::TIMEOUT => 5,
    RequestOptions::ALLOW_REDIRECTS => true,
]);

try {
    $response = $client->get($url);
} catch (GuzzleException $exception) {
    http_response_code(500);
    echo $exception->getMessage();
    exit;
}

$responseCode = $response->getStatusCode();
$body = $response->getBody()->getContents();

http_response_code($responseCode);

if ($responseCode === 200) {
    $document = new DOMDocument;
    $document->loadXML($body);

    $rootElement = $document->documentElement;

    /**
     * @var $entryElement DOMElement
     */
    foreach ($rootElement->getElementsByTagName("entry") as $entryElement) {
        if ($entryElement->parentNode !== $rootElement) {
            continue;
        }

        $title = $entryElement->getElementsByTagName("title")?->item(0)?->textContent ?? null;

        if ($title === null or $title === "") {
            continue;
        }

        $filtered = false;

        foreach ($filter as $filterString) {
            if (stripos($title, $filterString) !== false) {
                $filtered = true;
                break;
            }
        }

        if (!$filtered) {
            continue;
        }

        $rootElement->removeChild($entryElement);
    }

    echo $document->saveXML($document);
} else {
    echo $body;
}