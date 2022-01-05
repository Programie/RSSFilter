<?php
namespace com\selfcoders\rssfilter;

use Symfony\Component\Asset\Package;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Html\HtmlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwigRenderer
{
    private static ?Environment $twig = null;

    public static function init(Package $assetsPackage): void
    {
        if (self::$twig !== null) {
            return;
        }

        $loader = new FilesystemLoader(VIEWS_ROOT);

        self::$twig = new Environment($loader);

        self::$twig->addExtension(new HtmlExtension);

        self::$twig->addFunction(new TwigFunction("asset", function (string $path) use ($assetsPackage) {
            return $assetsPackage->getUrl($path);
        }));
    }

    /**
     * @param string $name
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function render(string $name, array $context = []): string
    {
        return self::$twig->render($name . ".twig", $context);
    }
}