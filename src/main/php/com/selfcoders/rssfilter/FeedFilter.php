<?php
namespace com\selfcoders\rssfilter;

use DOMDocument;
use DOMElement;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class FeedFilter
{
    private array $filterStrings = [];
    private string $title;
    private ResponseInterface $response;

    public function addFilter(string $filterString): void
    {
        $this->filterStrings[] = $filterString;
    }

    public function filterUrl(string $url): string
    {
        $client = new Client([
            RequestOptions::TIMEOUT => 5,
            RequestOptions::ALLOW_REDIRECTS => true,
        ]);

        $this->response = $client->get($url);

        return $this->filterContent($this->response->getBody()->getContents());
    }

    public function filterContent(string $content): string
    {
        $document = new DOMDocument;
        $document->loadXML($content);

        /**
         * @var $rootElement DOMElement
         */
        $rootElement = $document->documentElement;

        $elementsToRemove = [];

        $this->title = $rootElement->getElementsByTagName("title")?->item(0)?->textContent ?? "";

        /**
         * @var $entryElement DOMElement
         */
        foreach ($rootElement->getElementsByTagName("entry") as $entryElement) {
            if ($entryElement->parentNode !== $rootElement) {
                continue;
            }

            $title = trim($entryElement->getElementsByTagName("title")?->item(0)?->textContent ?? "");

            if ($title === "") {
                continue;
            }

            if (!$this->isFiltered($title)) {
                continue;
            }

            $elementsToRemove[] = $entryElement;
        }

        foreach ($elementsToRemove as $element) {
            $element->parentNode->removeChild($element);
        }

        return $document->saveXML($document);
    }

    private function isFiltered(string $string): bool
    {
        foreach ($this->filterStrings as $filterString) {
            if (str_starts_with($filterString, "/") and str_ends_with($filterString, "/")) {
                if (preg_match($filterString, $string)) {
                    return true;
                }
            } elseif (stripos($string, $filterString) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function hasHeader(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }
}