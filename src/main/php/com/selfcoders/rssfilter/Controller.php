<?php
namespace com\selfcoders\rssfilter;

use com\selfcoders\rssfilter\exception\NotFoundException;
use com\selfcoders\rssfilter\models\Feed;

class Controller
{
    public function getPage()
    {
        $entityManager = Database::getEntityManager();

        $feeds = $entityManager->getRepository(Feed::class)->findBy([], ["name" => "asc"]);

        return TwigRenderer::render("index", [
            "feeds" => $feeds
        ]);
    }

    public function editFeed(array $params)
    {
        if (isset($params["name"])) {
            $entityManager = Database::getEntityManager();

            $feed = $entityManager->getRepository(Feed::class)->findOneBy(["name" => $params["name"]]);
        } else {
            $feed = null;
        }

        return TwigRenderer::render("edit-feed", [
            "editFeed" => $feed !== null,
            "feed" => $feed
        ]);
    }

    public function saveFeed(array $params)
    {
        $entityManager = Database::getEntityManager();

        if (isset($params["name"])) {
            $feed = $entityManager->getRepository(Feed::class)->findOneBy(["name" => $params["name"]]);
        } else {
            $feed = null;
        }

        $name = trim($_POST["name"] ?? "");
        $url = trim($_POST["url"] ?? "");
        $filters = explode("\n", $_POST["filters"] ?? "");

        if ($name === "") {
            $errors[] = "name";
            $errors[] = "name-empty";
        } else {
            if (!preg_match("/^([a-zA-Z_-]+)$/", $name)) {
                $errors[] = "name";
                $errors[] = "invalid-name";
            } else {
                if ($feed === null or $feed->getName() !== $name) {
                    if ($entityManager->getRepository(Feed::class)->count(["name" => $name])) {
                        $errors[] = "name";
                        $errors[] = "duplicate-name";
                    }
                }
            }
        }

        if ($url === "") {
            $errors[] = "url";
            $errors[] = "url-empty";
        }

        if (!empty($errors)) {
            return TwigRenderer::render("edit-feed", [
                "editFeed" => isset($params["name"]),
                "feed" => [
                    "name" => $name,
                    "url" => $url,
                    "filters" => $filters
                ],
                "errors" => $errors
            ]);
        }

        if ($feed === null) {
            $feed = new Feed;
        }

        $feed->setName($name);
        $feed->setUrl($url);
        $feed->setFilters($filters);

        $entityManager->persist($feed);
        $entityManager->flush();

        header("Location: /", response_code: 302);
        return "";
    }

    public function removeFeed(array $params)
    {
        $entityManager = Database::getEntityManager();

        $feed = $entityManager->getRepository(Feed::class)->findOneBy(["name" => $params["name"]]);

        if ($feed !== null) {
            $entityManager->remove($feed);
            $entityManager->flush();
        }
    }

    public function getFeed(array $params)
    {
        $entityManager = Database::getEntityManager();

        /**
         * @var $feed Feed
         */
        $feed = $entityManager->getRepository(Feed::class)->findOneBy(["name" => $params["name"]]);

        if ($feed === null) {
            throw new NotFoundException;
        }

        return $feed->requestAndFilter();
    }
}