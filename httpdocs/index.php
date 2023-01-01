<?php
use com\selfcoders\rssfilter\Controller;
use com\selfcoders\rssfilter\exception\NotFoundException;
use com\selfcoders\rssfilter\TwigRenderer;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

require_once __DIR__ . "/../bootstrap.php";

$router = new AltoRouter;

$router->map("GET", "/", "getPage");
$router->map("GET", "/add-feed", "editFeed");
$router->map("POST", "/add-feed", "saveFeed");
$router->map("GET", "/feeds.json", "getFeedsAsJson");
$router->map("GET", "/feeds/[:name]/edit", "editFeed");
$router->map("POST", "/feeds/[:name]/edit", "saveFeed");
$router->map("GET", "/feeds/[:name]", "getFeed");
$router->map("DELETE", "/feeds/[:name]", "removeFeed");

$match = $router->match();

$assetsPackage = new Package(new JsonManifestVersionStrategy(APP_ROOT . "/webpack.assets.json"));

TwigRenderer::init($assetsPackage);

try {
    if ($match === false) {
        throw new NotFoundException;
    } else {
        $target = $match["target"];

        $controller = new Controller;
        $result = $controller->{$target}($match["params"]);

        if (is_array($result) or is_object($result)) {
            header("Content-Type: application/json");
            echo json_encode($result);
        } else {
            echo $result;
        }
    }
} catch (NotFoundException) {
    http_response_code(404);
    echo TwigRenderer::render("error-404", [
        "url" => $_SERVER["REQUEST_URI"]
    ]);
}