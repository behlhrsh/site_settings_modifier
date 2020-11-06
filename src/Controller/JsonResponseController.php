<?php

namespace Drupal\site_settings_modifier\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class JsonResponseController.
 */
class JsonResponseController extends ControllerBase {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->configFactory = $container->get('config.factory');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * Get function to return json data for node id entered as seond parameter.
   *
   * @param string $api
   *   The api key entered in site configurations.
   * @param int $node_id
   *   The node id to load & return as an array.
   *
   * @return string
   *   Return Node array as json.
   */
  public function get($api, $node_id) {
    if ($api != $this->configFactory->get('system.site')->get('siteapikey') || empty($node_id)) {
      throw new AccessDeniedHttpException();
    }
    $node = $this->entityTypeManager->getStorage('node')->load($node_id);
    if (empty($node)) {
      throw new AccessDeniedHttpException();
    }
    $data = $node->toArray();
    return new JsonResponse($data);
  }

}
