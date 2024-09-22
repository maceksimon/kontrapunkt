# WordPress in Examples

## ACF - Filtrování dle hodnot multivalue pole
Pole typu [Post Object](https://www.advancedcustomfields.com/resources/post-object/) a podobné ukládá hodnoty do databáze ve formě serializovaného pole např. a:3:{i:0;s:4:"1108";i:1;s:5:"23884";i:2;s:5:"23731";}

Níže je příklad  z projektu [eBeton](https://gitlab.com/portadesign/wordpress/ebeton/-/blob/master/wp-content/themes/ebeton/index.php)

Důležité je nezapomenout **obalit string do uvozovek**, aby to hledalo pouze přesnou shodu.

```php
$articles = Timber::get_posts($query = [
 'post_type' => 'articles',
 'meta_query' => [[
   'key' => 'writers',
   'value' => '"' . $context['post']->id . '"',
   'compare' => 'LIKE',
 ]],
 'orderby' => 'post published',
 'order' => 'DESC',
 'posts_per_page' => 5,
 'paged' => $paged,
]);
```

## ACF Wysiwyg field - používat basic editor bez možnosti vkládání fotek
Pole typu [Wysiwyg Editor](https://www.advancedcustomfields.com/resources/wysiwyg-editor/) používat jako výchozí bez pokročilých možností, protože s nimi většinou v grafice nepočítáme a předcházíme tak možnosti vložení nevhodného obsahu.

```php
->addWysiwyg('perex', [
  'label' => 'Perex',
  'placeholder' => '',
  'new_lines' => 'wpautop',
  'tabs' => 'all',
  'toolbar' => 'basic',
  'media_upload' => 0,
  'wpml_cf_preferences' => 2,
])
```

## ACF Image / Gallery field - přidat informaci o doporučených rozměrech

```php
->addImage('image', [
  'label' => 'Obrázek',
  'instructions' => 'Doporučený rozměr: 1180x800px',
  'required' => 1,
  'wpml_cf_preferences' => 1,
])
```
