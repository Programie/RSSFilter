<?php
namespace com\selfcoders\rssfilter\models;

use com\selfcoders\rssfilter\FeedFilter;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="com\selfcoders\rssfilter\orm\FeedRepository")
 * @ORM\Table(name="feeds")
 */
class Feed implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private int $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="string")
     */
    private string $url;

    /**
     * @ORM\Column(type="string")
     */
    private string $filters;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $filterIsWhitelist;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFilters(): array
    {
        return self::filterArray(explode("\n", $this->filters));
    }

    public function setFilters(array $filters): self
    {
        $this->filters = implode("\n", self::filterArray($filters));

        return $this;
    }

    public function isFilterWhitelist(): bool
    {
        return $this->filterIsWhitelist;
    }

    public function setFilterIsWhitelist(bool $filterIsWhitelist): self
    {
        $this->filterIsWhitelist = $filterIsWhitelist;

        return $this;
    }

    public function requestAndFilter(bool $addHeaders = false): string
    {
        $feedFilter = new FeedFilter;

        $feedFilter->setFilterIsWhitelist($this->isFilterWhitelist());

        foreach ($this->getFilters() as $filter) {
            $feedFilter->addFilter($filter);
        }

        $feedContent = $feedFilter->filterUrl($this->getUrl());

        $this->setTitle($feedFilter->getTitle());

        if ($addHeaders) {
            $headersToKeep = ["Content-Type", "Last-Modified", "Cache-Control", "Age"];

            foreach ($headersToKeep as $name) {
                if (!$feedFilter->hasHeader($name)) {
                    continue;
                }

                header(sprintf("%s: %s", $name, $feedFilter->getHeaderLine($name)));
            }
        }

        return $feedContent;
    }

    private static function filterArray(array $array): array
    {
        return array_unique(array_filter(array_map("trim", $array)));
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "title" => $this->title,
            "url" => $this->url,
            "filters" => $this->getFilters(),
            "filterIsWhitelist" => $this->filterIsWhitelist
        ];
    }
}