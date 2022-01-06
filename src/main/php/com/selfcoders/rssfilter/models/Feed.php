<?php
namespace com\selfcoders\rssfilter\models;

use com\selfcoders\rssfilter\FeedFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="com\selfcoders\rssfilter\orm\FeedRepository")
 * @ORM\Table(name="feeds")
 */
class Feed
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
    private string $url;

    /**
     * @ORM\Column(type="string")
     */
    private string $filters;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function requestAndFilter(bool $addHeaders = false): string
    {
        $feedFilter = new FeedFilter;

        foreach ($this->getFilters() as $filter) {
            $feedFilter->addFilter($filter);
        }

        $feedContent = $feedFilter->filterUrl($this->getUrl());

        if ($addHeaders) {
            foreach ($feedFilter->getHeaders() as $name => $value) {
                header(sprintf("%s: %s", $name, implode(", ", $value)));
            }
        }

        return $feedContent;
    }

    private static function filterArray(array $array): array
    {
        return array_unique(array_filter(array_map("trim", $array)));
    }
}