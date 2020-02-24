<?php

namespace App\ViewFunctions;

use DI\Container;
use Tightenco\Collect\Support\Collection;

class Breadcrumbs extends ViewFunction
{
    /** @var string The function name */
    protected $name = 'breadcrumbs';

    /** @var Container The application container */
    protected $container;

    /**
     * Create a new Breadcrumbs object.
     *
     * @param \DI\Container        $container
     * @param \PHLAK\Config\Config $config
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Build an array of breadcrumbs for a given path.
     *
     * @param string $path
     *
     * @return array
     */
    public function __invoke(string $path)
    {
        $breadcrumbs = Collection::make(explode('/', $path))->diff(
            explode('/', $this->container->get('base_path'))
        )->filter();

        return $breadcrumbs->filter(function (string $crumb) {
            return $crumb !== '.';
        })->reduce(function (Collection $carry, string $crumb) {
            return $carry->put($crumb, ltrim("{$carry->last()}/{$crumb}", '/'));
        }, new Collection)->map(function (string $path): string {
            return sprintf('?dir=%s', $path);
        });
    }
}
