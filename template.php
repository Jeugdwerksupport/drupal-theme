<?php
function jeugdwerksupport_preprocess_html(&$variables) {   
    if(!in_array('page-node', $variables['classes_array'])) {
        $variables['classes_array'][] = 'not-page-node';
    }
}
/**
 * Override or insert variables into the page template.
 */
function jeugdwerksupport_process_page(&$variables) {
  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
}

/**
 * Override or insert variables into the node template.
 */
function jeugdwerksupport_preprocess_node(&$variables) {
  $node = $variables['node'];

  // Add template suggestions
  if(!$variables['page']) {
    if($variables['teaser']) {
      $variables['theme_hook_suggestions'][] = 'node__'.$variables['node']->type.'__teaser';
      $variables['theme_hook_suggestions'][] = 'node__'.$variables['node']->nid.'__teaser';
      $variables['theme_hook_suggestions'][] = 'node__teaser';
    }
  }

  // Add class 'node-full'
  if ($variables['view_mode'] == 'full' && node_is_page($node)) {
    $variables['classes_array'][] = 'node-full';
  }

  // Set custom date format
  $variables['date'] = format_date($node->created, 'custom', 'F jS Y');

  // Add summary variable
  if($variables['view_mode'] == 'full') {
    $items = field_get_items('node', $node, 'body', $node->language);
    if($items) {
      // If there's a single summary, just set the summary variable
      if(count($items) == 1) {
        $variables['summary'] = $items[0]['summary'];
      }
      // If there are more field instances, set multiple summary values
      else {
        foreach($items as $item) {
          $variables['summary'][] = $item['summary'];
        }
      }
    }
  }
}

/**
 * Implements theme_menu_tree().
 */
function jeugdwerksupport_menu_tree($variables) {
  return '<ul class="menu clearfix">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_field__field_type().
 */
function jeugdwerksupport_field__taxonomy_term_reference($variables) {
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<h3 class="field-label">' . $variables['label'] . ': </h3>';
  }

  // Render the items.
  $output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
  foreach ($variables['items'] as $delta => $item) {
    $output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $variables['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
  }
  $output .= '</ul>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . (!in_array('clearfix', $variables['classes_array']) ? ' clearfix' : '') . '">' . $output . '</div>';

  return $output;
}