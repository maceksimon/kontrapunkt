<?php

declare(strict_types=1);

// from https://support.advancedcustomfields.com/forums/topic/setting-the-gutenberg-block-render-callback-dynamically/

use Timber\Timber;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Block
{
  private $name;
  private $title;
  private $description;
  private $category;
  private $icon;
  private $keywords;
  private $post_types;
  private $template;
  private $validationFields;
  private $fieldsBuilder;

  public function __construct($name, $title)
  {
    $this->setName($name);
    $this->setTitle($title);

    $this->category = 'custom';
    $this->icon = file_get_contents(get_template_directory() . '/templates/gutenberg/default-icon.svg');
    $this->template = '@component/' . $this->getName() . '/' . $this->getName() . '.twig';
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    $this->title = $title;
    return $this;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  public function getCategory()
  {
    return $this->category;
  }

  public function setCategory($category)
  {
    $this->category = $category;
    return $this;
  }

  public function getIcon()
  {
    return $this->icon;
  }

  public function setIcon($icon)
  {
    $this->icon = $icon;
    return $this;
  }

  public function getKeywords()
  {
    return $this->keywords;
  }

  public function setKeywords($keywords)
  {
    $this->keywords = $keywords;
    return $this;
  }

  public function getPostTypes()
  {
    return (array) $this->post_types;
  }

  public function setPostTypes($post_types)
  {
    $this->post_types = $post_types;
    return $this;
  }

  public function getTemplate()
  {
    return $this->template;
  }

  public function setTemplate($template)
  {
    $this->template = $template;
    return $this;
  }

  public function getValidationFields()
  {
    return (array) $this->validationFields;
  }

  public function setValidationFields($validationFields)
  {
    $this->validationFields = $validationFields;
    return $this;
  }

  public function fieldsBuilder()
  {
    if (! $this->fieldsBuilder instanceof FieldsBuilder) {
      $this->fieldsBuilder = new FieldsBuilder($this->getName());
      $this->fieldsBuilder->setLocation('block', '==', 'acf/' . $this->getName());
    }
    return $this->fieldsBuilder;
  }

  public function register()
  {
    acf_register_block_type([
      'name' => $this->getName(),
      'title' => $this->getTitle(),
      'description' => $this->getDescription(),
      'category' => $this->getCategory(),
      'icon' => $this->getIcon(),
      'keywords' => $this->getKeywords(),
      'render_callback' => [$this, 'render'],
      'mode' => 'auto',
      'supports' => [
        'anchor' => TRUE,
      ],
    ]);

    $this->registerFields();
  }

  private function registerFields()
  {
    if ($this->fieldsBuilder instanceof FieldsBuilder) {
      acf_add_local_field_group($this->fieldsBuilder->build());
    }
  }

  private function context($block, $content = '', $is_preview = false)
  {

    $context = Timber::context();
    $post_id = get_queried_object_id();
    $fields = get_field_objects();
    $content = [];
    if (! empty($fields)) {
      foreach ($fields as $key => $field) {
        $value = Helpers::fieldFormatter($field, $post_id);
        if (! empty($value)) {
          $content[$key] = $value;
        }
      }
    }

    $content['is_preview'] = $is_preview;
    $content['wrapper_id'] = isset($block['anchor']) ? $block['anchor'] : str_replace('block_', 'component-', $block['id']);
    $content['wrapper_classes'] = isset($block['className']) ? $block['className'] : '';

    $content = apply_filters('block_' . str_replace('-', '_', $this->getName()) . '_content', $content);

    $context['content'] = $content;

    return $context;
  }
  public function compile($block, $content = '', $is_preview = false)
  {

    $context = $this->context($block, $content = '', $is_preview = false);
    $content = $context['content'];
    $validationFields = $this->getValidationFields();

    $show = TRUE;
    foreach ($validationFields as $field) {
      if (! isset($content[$field]) || empty($content[$field])) {
        $show = FALSE;
      }
    }

    if ($show) {
      // ability to alter template path based on content
      $template = apply_filters('block_' . str_replace('-', '_', $this->getName()) . '_template', $this->getTemplate(), $content);
      return Timber::compile($template, $context);
    } else {
      return Timber::compile('@component/alert/alert.twig', [
        'content' => [
          'message' => '<strong>' . $block['title'] . ':</strong> Pro zobrazení vyplňte požadované údaje',
          'type' => 'warning',
          'container' => 'container',
        ]
      ]);
    }
  }

  public function render($block, $content = '', $is_preview = false)
  {
    print $this->compile($block, $content, $is_preview);
  }
}
