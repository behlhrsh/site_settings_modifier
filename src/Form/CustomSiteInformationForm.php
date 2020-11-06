<?php

namespace Drupal\site_settings_modifier\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\system\Form\SiteInformationForm;
use Drupal\Core\Messenger\Messenger;
use Drupal\path_alias\AliasManagerInterface;

/**
 * Configure site information settings for this site.
 *
 * @internal
 */
class CustomSiteInformationForm extends SiteInformationForm {

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * Constructs a SiteInformationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The Messenger.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AliasManagerInterface $alias_manager, PathValidatorInterface $path_validator, RequestContext $request_context, Messenger $messenger) {
    parent::__construct($config_factory, $alias_manager, $path_validator, $request_context);
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path_alias.manager'),
      $container->get('path.validator'),
      $container->get('router.request_context'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_config = $this->config('system.site');
    $form = parent::buildForm($form, $form_state);

    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => t('Site API Key'),
      '#default_value' => $site_config->get('siteapikey') ? $site_config->get('siteapikey') : $this->t('No API Key yet'),
      '#description' => t('Please add Site API key here.'),
    ];
    if ($site_config->get('siteapikey')) {
      $form['actions']['submit']['#value'] = $this->t('Update Configuration');
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('system.site')
      ->set('siteapikey', $form_state->getValue('siteapikey'))
      ->save();
    $this->messenger->addMessage($this->t('Site API Key has been saved: @value', ['@value' => $form_state->getValue('siteapikey')]));
    parent::submitForm($form, $form_state);
  }

}
