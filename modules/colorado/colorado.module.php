<?php

/**
 * @file A fake custom module.
 */

use RenderAPI\RenderableBuilder;
use RenderAPI\RenderableBuilderInterface;

/**
 * An example of an alter callback.
 */
function colorado_alter_ThemeNode(RenderableBuilderInterface $build) {
  // $build->setBuildClass('ThemeFoo');
  // $build->set('foo', 'bar');
}

/**
 * Some other alter callback.
 */
function colorado_alter_ItemList(RenderableBuilderInterface $build) {
  // $items = $build->get('items');
  // $items[] = r('ThemeFullNode', array(
  //   'node' => node_load(789),
  // ));
  // $build->set('items', $items);
}
