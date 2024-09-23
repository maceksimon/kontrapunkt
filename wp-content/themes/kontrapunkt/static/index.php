<?php

setlocale(LC_ALL, "czech");

// include vendor from parent if exists
// search 5 levels up max
for ($level = 0; $level <= 5; $level++) {
  if($level === 0) {
    if (@include __DIR__ . '/vendor/autoload.php') {
      break;
    }
  }else{
    if (@include dirname(__DIR__, $level) . '/vendor/autoload.php') {
      break;
    }
  }
}
if(!class_exists("\Composer\Autoload\ClassLoader")) {
  die('Install packages using `composer install` in ' . __DIR__);
}

use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Profiler\Profile;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\ProfilerExtension;
use Twig\Extra\String\StringExtension;
use Twig\Profiler\Dumper\TextDumper;
use Twig\Extra\Intl\IntlExtension;
use Parisek\Twig\CommonExtension;
use Parisek\Twig\AttributeExtension;
use Parisek\Twig\TypographyExtension;
use HelloNico\Twig\DumpExtension;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;
// use Symfony\Bridge\Twig\Extension\TranslationExtension;
// use Symfony\Component\Translation\Translator;
// use Symfony\Component\Translation\Formatter\MessageFormatter;
// use Symfony\Component\Translation\IdentityTranslator;
// use Symfony\Component\Translation\Loader\MoFileLoader;

function getSidebar() {
  $sidebar = [];

  $directory = __DIR__ . '/templates/component';
  $directory_iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
  $flattened = new RecursiveIteratorIterator($directory_iterator);
  $regex_iterator = new RegexIterator($flattened, '/\.twig$/');
  foreach ($regex_iterator as $file) {
    $content = file_get_contents($file->getPathname());
    $content = parseTwigComment($content);
    $styleguide = FALSE;
    if(file_exists($file->getPath() . '/styleguide.twig')) {
      $styleguide = 'styleguide.twig';
    }elseif(isset($content['styleguide'])) {
      $styleguide = $content['styleguide'];
    }
    if(isset($content['name'])) {
      $id = pathinfo( $file, PATHINFO_FILENAME);
      $sidebar['component'][] = [
        'id' => $id,
        'title' => $content['name'],
        'weight' => isset($content['weight']) ? intval($content['weight']) : 50,
        'url' => 'component/' . $id,
        'styleguide' => $styleguide,
      ];
    }
  }
  if(isset($sidebar['component'])) {
    usort($sidebar['component'], function ($first, $second) {
      if ($first['weight'] == $second['weight']) {
        $collator = new \Collator('cs');
        return $collator->compare($first['title'], $second['title']);
      }
      return $first['weight'] - $second['weight'];
    });
  }

  $directory = __DIR__ . '/templates/page';
  $directory_iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
  $flattened = new RecursiveIteratorIterator($directory_iterator);
  $regex_iterator = new RegexIterator($flattened, '/\.twig$/');
  foreach ($regex_iterator as $file) {
    $content = file_get_contents($file->getPathname());
    $content = parseTwigComment($content);
    $styleguide = FALSE;
    if(file_exists($file->getPath() . '/styleguide.twig')) {
      $styleguide = 'styleguide.twig';
    }elseif(isset($content['styleguide'])) {
      $styleguide = $content['styleguide'];
    }
    if(isset($content['name'])) {
      $id = pathinfo( $file, PATHINFO_FILENAME);
      $sidebar['page'][] = [
        'id' => $id,
        'title' => $content['name'],
        'weight' => isset($content['weight']) ? intval($content['weight']) : 50,
        'url' => 'page/' . $id,
        'styleguide' => $styleguide,
      ];
    }
  }
  if(isset($sidebar['page'])) {
    usort($sidebar['page'], function ($first, $second) {
      if ($first['weight'] == $second['weight']) {
        $collator = new \Collator('cs');
        return $collator->compare($first['title'], $second['title']);
      }
      return $first['weight'] - $second['weight'];
    });
  }

  return $sidebar;
}

function parseTwigComment($content) {

  // Make sure we catch CR-only line endings.
	$content = str_replace("\r", "\n", $content);

  if (preg_match("/{#([^}]*)#}/", $content, $match ) && $match[1] ) {
    try {
      $match[1] = trim($match[1]);
      return Yaml::parse($match[1]);
    } catch (ParseException $exception) {
      dump('Unable to parse the YAML string:' . $exception->getMessage());
    }
  }
  return FALSE;
}

$loader = new FilesystemLoader(__DIR__);
$loader->addPath(__DIR__ . '/styleguide/templates/', 'styleguide');
$loader->addPath(__DIR__ . '/templates/component', 'component');
$loader->addPath(__DIR__ . '/templates/macro', 'macro');
$loader->addPath(__DIR__ . '/templates/page', 'page');
$loader->addPath(__DIR__ . '/templates', 'static');
$loader->addPath(__DIR__ . '/images/icons', 'icons');
$loader->addPath(__DIR__ . '/images', 'images');
$twig = new Environment($loader, [
  'cache' => false,
  'debug' => true,
  'autoescape' => false,
]);
// $profile = new Profile();
// $twig->addExtension(new ProfilerExtension($profile));
// $dumper = new TextDumper();
// dump($dumper->dump($profile));
$twig->addExtension(new IntlExtension());
$twig->addExtension(new CommonExtension());
$twig->addExtension(new AttributeExtension());
$typography_settings = __DIR__ . '/typography.yml';
$twig->addExtension(new TypographyExtension($typography_settings));
$twig->addExtension(new StringExtension());
$twig->addExtension(new DumpExtension());
$engine = new MarkdownEngine\MichelfMarkdownEngine();
$twig->addExtension(new MarkdownExtension($engine));
$twig->addFunction(new TwigFunction('component_*', function (Environment $env, $context, $template_name, $content = []) {
  try {
    $template_name = str_replace('_', '-', $template_name);
    $template = $env->load('@component/' . $template_name . '/' . $template_name . '.twig');
    $context = array_merge($context, ['content' => $content]);

    // we user render to allow save output to twig variable
    return $template->render($context);
  } catch (\Throwable $e) {
    try {
      $template = $env->load('@component/alert/alert.twig');
      $content = [
        'type' => 'error',
        'container' => 'container',
        'message' => 'Component template <strong>' . $template_name . '.twig</strong> not found',
      ];
      $context = array_merge($context, ['content' => $content]);

      return $template->render($context);
    } catch (\Throwable $e) {
      return '<div>Component template <strong>' . $template_name . '.twig</strong> not found</div>';
    }
  }
}, [
  'needs_environment' => true,
  'needs_context' => true,
  'is_safe' => ['html']
]));
$twig->addFunction(new TwigFunction('component', function (Environment $env, $context, $template_name, $content = []) {
  try {
    $template_name = str_replace('_', '-', $template_name);
    $template = $env->load('@component/' . $template_name . '/' . $template_name . '.twig');
    $context = array_merge($context, ['content' => $content]);

    // we user render to allow save output to twig variable
    return $template->render($context);
  } catch (\Throwable $e) {
    try {
      $template = $env->load('@component/alert/alert.twig');
      $content = [
        'type' => 'error',
        'container' => 'container',
        'message' => 'Component template <strong>' . $template_name . '.twig</strong> not found',
      ];
      $context = array_merge($context, ['content' => $content]);

      return $template->render($context);
    } catch (\Throwable $e) {
      return '<div>Component template <strong>' . $template_name . '.twig</strong> not found</div>';
    }
  }
}, [
  'needs_environment' => true,
  'needs_context' => true,
  'is_safe' => ['html']
]));
$twig->addFunction(new TwigFunction('page_*', function (Environment $env, $context, $template_name, $content = []) {
  try {
    $template_name = str_replace('_', '-', $template_name);
    $template = $env->load('@page/' . $template_name . '/' . $template_name . '.twig');
    $context = array_merge($context, ['content' => $content]);

    // we user render to allow save output to twig variable
    return $template->render($context);
  } catch (\Throwable $e) {
    try {
      $template = $env->load('@component/alert/alert.twig');
      $content = [
        'type' => 'error',
        'container' => 'container',
        'message' => 'Page template <strong>' . $template_name . '.twig</strong> not found',
      ];
      $context = array_merge($context, ['content' => $content]);

      return $template->render($context);
    } catch (\Throwable $e) {
      return '<div>Page template <strong>' . $template_name . '.twig</strong> not found</div>';
    }
  }
}, [
  'needs_environment' => true,
  'needs_context' => true,
  'is_safe' => ['html']
]));

// implemented only in CMS
$filter = new TwigFilter('resizer', function ($value) {
  return $value;
});
$twig->addFilter($filter);
$function = new TwigFunction('__', function ($value) {
  return $value;
});
$twig->addFunction($function);
$function = new TwigFunction('_x', function ($value) {
  return $value;
});
$twig->addFunction($function);

// temporary disabled as Drupal syntax is not working
// we have basic support in CommonExtension
// $translator = new Translator(
//   'cs_CZ',
//   new MessageFormatter(new IdentityTranslator())
// );
// $twig->addExtension(new TranslationExtension($translator));

$prefix = 'styleguide';
$path = pathinfo($_SERVER['SCRIPT_NAME']);
$url = parse_url($_SERVER['REQUEST_URI']);
$url['sections'] = explode('/', ltrim($url['path'], '/'));

$rootPath = '/';
if(strpos($_SERVER['REQUEST_URI'], '/' . $prefix) !== FALSE) {
  $rootPath = '/' . $prefix . '/';
}
if($url['sections'][0] === $prefix) {
  $type = $url['sections'][1] ?? '';
  $template = $url['sections'][2] ?? '';
}else{
  $type = $url['sections'][0] ?? '';
  $template = $url['sections'][1] ?? '';
}

$context = [
  'homeUrl' => $rootPath,
  'templateUrl' => rtrim($path['dirname'], '/'),
  'langcode' => 'cs',
  'styleguide' => [
    'html_title' => 'Styleguide',
    'sidebar' => getSidebar(),
  ],
];

$loader = $twig->getLoader();
if($type === 'component') {
  $twig_template_path = '@component/' . $template . '/' . $template . '.twig';
  $twig_styleguide_path = '@component/' . $template . '/styleguide.twig';
  if($loader->exists($twig_template_path)) {
    $twig_template = $loader->getSourceContext($twig_template_path);
    $twig_anotation = parseTwigComment($twig_template->getCode());
    $context['styleguide']['title'] = $twig_anotation['name'] ?? '';
    $context['styleguide']['page_title'] = $twig_anotation['name'] ?? '';
    $context['styleguide']['description'] = $twig_anotation['description'] ?? '';
    if($loader->exists($twig_styleguide_path)) {
      $context['styleguide']['content'] = $twig->render($twig_styleguide_path, $context);
    }else{
      $context['content'] = $twig_anotation['styleguide']['content'] ?? '';
      $context['styleguide']['content'] = $twig->render($twig_template_path, $context);
    }
    echo $twig->render('@styleguide/styleguide-component.twig', $context);
  }else{
    echo $twig->render('@styleguide/styleguide-404.twig', $context);
  }
}elseif($type === 'page') {
  $twig_template_path = '@page/' . $template . '/' . $template . '.twig';
  $twig_styleguide_path = '@page/' . $template . '/styleguide.twig';
  if($loader->exists($twig_template_path)) {
    $twig_template = $loader->getSourceContext($twig_template_path);
    $twig_anotation = parseTwigComment($twig_template->getCode());
    $context['styleguide']['title'] = $twig_anotation['name'] ?? '';
    $context['styleguide']['page_title'] = $twig_anotation['name'] ?? '';
    if($loader->exists($twig_styleguide_path)) {
      $context['styleguide']['content'] = $twig->render($twig_styleguide_path, $context);
    }else{
      $context['content'] = $twig_anotation['styleguide']['content'] ?? '';
      $context['styleguide']['content'] = $twig->render($twig_template_path, $context);
    }
    echo $twig->render('@styleguide/styleguide-page.twig', $context);
  }else{
    echo $twig->render('@styleguide/styleguide-404.twig', $context);
  }
}else{
  echo $twig->render('@styleguide/styleguide-homepage.twig', $context);
}
