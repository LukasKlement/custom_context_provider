<?php

namespace Drupal\custom_context_provider\ContextProvider;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Sets the current taxonomy term as a context on taxonomy term routes.
 */
class TaxonomyTermContext implements ContextProviderInterface
{

    use StringTranslationTrait;

    /**
     * The route match object.
     *
     * @var \Drupal\Core\Routing\RouteMatchInterface
     */
    protected $routeMatch;

    /**
     * Constructs a new object.
     *
     * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
     *   The route match object.
     */
    public function __construct(RouteMatchInterface $route_match)
    {
        $this->routeMatch = $route_match;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeContexts(array $unqualified_context_ids)
    {
        $result = [];
        $context_definition = new ContextDefinition('entity:taxonomy_term', $this->t('Taxonomy term from URL'));
        $value = null;
        if (($route_object = $this->routeMatch->getRouteObject())
            && ($route_contexts = $route_object->getOption('parameters'))
            && isset($route_contexts['taxonomy_term'])
        ) {
            if ($term = $this->routeMatch->getParameter('taxonomy_term')) {
                $value = $term;
            }
        }

        $cacheability = new CacheableMetadata();
        $cacheability->setCacheContexts(['route']);

        $context = new Context($context_definition, $value);
        $context->addCacheableDependency($cacheability);
        $result['taxonomy_term'] = $context;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableContexts()
    {
        $context = new Context(EntityContextDefinition::create('entity:taxonomy_term', $this->t('Taxonomy term from URL')));
        return ['taxonomy_term' => $context];
    }
}
