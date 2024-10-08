<?php
namespace SchemableValidator;

use SchemableValidator\FormController;
use SchemableValidator\Interface\Wordpress\Admin as WordpressInterface;

class Template {
  private array $options;
  private array $defaultOptions = [
    'aliases' => [
      'name' => 'name',
      'email' => 'email',
      'body' => 'body'
    ],
    'templates' => [],
  ];
  function __construct(array $options = []) {
    global $SV_INTERFACE_TARGET;
    $form_controller = new FormController();
    $this->options = array_merge($this->defaultOptions, $options);

    $this->interface = null;
    $this->data = $form_controller->get();

    if ($SV_INTERFACE_TARGET === 'wordpress') {
      $this->interface = new WordpressInterface($this->options['templates']);
    }
  }

  function get(string $template_name) {
    $format = isset($this->interface) ? $this->interface->get_template(template_name: $template_name) : $this->options['templates'][$template_name];
    $body = $format;

    foreach ($this->options['aliases'] as $key => $value) {
      $field = $this->data[$this->options['aliases'][$value]] ?? null;

      if (isset($field)) {
        $body = str_replace('{'.$key.'}', $field['value'], $body);
      }
    }
    return $body;
  }
}
