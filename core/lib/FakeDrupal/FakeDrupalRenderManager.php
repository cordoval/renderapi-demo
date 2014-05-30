<?php

namespace FakeDrupal;

use RenderAPI\RenderManagerInterface;
use RenderAPI\RenderableBuilderInterface;
use RenderAPI\RenderableInterface;

class FakeDrupalRenderManager implements RenderManagerInterface {

  public function alter(RenderableBuilderInterface $builder) {
    // Builder model: Call any altering functions.
    foreach (FakeDrupalRenderManager::getAlterCallbacks($builder) as $alterCallback) {
      // Alter callbacks receive the RenderableBuilder, can call methods, and
      // change build class.
      $alterCallback($builder);
    }
  }

  public function decorate(RenderableInterface $renderable) {
    foreach (FakeDrupalRenderManager::getDecoratorClasses($renderable) as $decoratorClass) {
      $renderable = new $decoratorClass($renderable);
    }
    return $renderable;
  }

  public function templateExists(RenderableInterface $renderable) {
    $classFile = $renderable->getBuildClass() . '.php';
    $template = $renderable->getTemplateName() . '.html.twig';
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      foreach (FakeDrupal::getExtensionFiles($extensionName) as $file) {
        if ($classFile === $file) {
          // Make sure template file exists for path.
          return file_exists(FakeDrupal::getExtensionPath($extensionName) . '/' . $template);
        }
      }
    }
    return FALSE;
  }

  /**
   * Call out to alter hooks.
   */
  public static function getAlterCallbacks(RenderableBuilderInterface $builder) {
    $callbacks = array();
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      $hook_name = str_replace('Theme', $extensionName . '_alter_', $builder->getBuildClass());
      if (function_exists($hook_name)) {
        $callbacks[] = $hook_name;
      }
    }
    return $callbacks;
  }

  /**
   * Classes used to decorate the given Renderable.
   */
  public static function getDecoratorClasses(RenderableInterface $renderable) {
    $classes = array();
    $suffix = str_replace('Theme', '' , $renderable->getBuildClass() . 'Decorator.php');
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      foreach (FakeDrupal::getExtensionFiles($extensionName) as $entry) {
        if (FALSE !== strpos($entry, $suffix)) {
          $classes[] = str_replace('.php', '', $entry);
        }
      }
    }
    return $classes;
  }

  /**
   * Returns a fake ranking of directories in which to look for templates.
   */
  public static function getWeightedTemplateDirectories() {
    $directories = array();
    foreach (FakeDrupal::getEnabledExtensions() as $extensionName) {
      $directories[] = FakeDrupal::getExtensionPath($extensionName);
    }
    return array_reverse($directories);
  }

}