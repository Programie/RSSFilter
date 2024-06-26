<?php
namespace com\selfcoders\rssfilter;

use DOMDocument;
use DOMElement;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class FeedFilter
{
    private bool $filterIsWhitelist = false;
    private array $filterStrings = [];
    private string $title;
    private ResponseInterface $response;

    public function setFilterIsWhitelist(bool $filterIsWhitelist): void
    {
        $this->filterIsWhitelist = $filterIsWhitelist;
    }

    public function addFilter(string $filterString): void
    {
        $this->filterStrings[] = $filterString;
    }

    public function filterUrl(string $url, string $selfUrl = null): string
    {
        $client = new Client([
            RequestOptions::TIMEOUT => 5,
            RequestOptions::ALLOW_REDIRECTS => true,
        ]);

        $this->response = $client->get($url);

        return $this->filterContent($this->response->getBody()->getContents(), $selfUrl);
    }

    public function filterContent(string $content, string $selfUrl = null): string
    {
        $document = new DOMDocument;
        $document->loadXML($content);

        /**
         * @var $rootElement DOMElement
         */
        $rootElement = $document->documentElement;

        if ($selfUrl !== null) {
            $selfLinkElement = null;

            /**
             * @var DOMElement $linkElement
             */
            foreach ($rootElement->getElementsByTagName("link") as $linkElement) {
                if ($linkElement->getAttribute("rel") === "self") {
                    $selfLinkElement = $linkElement;
                    break;
                }
            }

            if ($selfLinkElement === null) {
                $selfLinkElement = new DOMElement("link");
                $selfLinkElement->setAttribute("rel", "self");
                $selfLinkElement->setAttribute("type", "application/atom+xml");
                $rootElement->appendChild($selfLinkElement);
            }

            $selfLinkElement->setAttribute("href", $selfUrl);
        }

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

            if ($title === "" or !$this->includeInFeed($title)) {
                $elementsToRemove[] = $entryElement;
            }
        }

        foreach ($elementsToRemove as $element) {
            $element->parentNode->removeChild($element);
        }

        return $document->saveXML($document);
    }

    private function includeInFeed(string $string): bool
    {
        $match = $this->checkFilterMatch($string);

        // Filter is in whitelist mode and string matches a filter
        if ($this->filterIsWhitelist and $match) {
            return true;
        }

        // Filter is in blacklist mode and string does not match a filter
        if (!$this->filterIsWhitelist and !$match) {
            return true;
        }

        // Do not include in feed
        return false;
    }

    private function checkFilterMatch(string $string): bool
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