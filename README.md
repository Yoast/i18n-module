[![Code Climate](https://codeclimate.com/github/Yoast/i18n-module/badges/gpa.svg)](https://codeclimate.com/github/Yoast/i18n-module)

# Yoast i18n module
Promote your own translation site for people who are using your plugin in another language than `en_US`. 

## Example of the rendered promo box

[![Example promo box](http://uploads.yoast.nl/i18n-promo-box_1A1B3FFF.png)

## How to use this module
Just include the library as a submodule, make sure the class is loaded and instantiate it like this:

```php
$wpseo_i18n = new yoast_i18n(
	array(
		'textdomain'     => 'wordpress-seo',
		'project_slug'   => 'wordpress-seo',
		'plugin_name'    => 'WordPress SEO by Yoast',
		'hook'           => 'wpseo_admin_footer',
		'glotpress_url'  => 'http://translate.yoast.com',
		'glotpress_name' => 'Yoast Translate',
		'glotpress_logo' => 'https://cdn.yoast.com/wp-content/uploads/i18n-images/Yoast_Translate.svg',
		'register_url '  => 'http://translate.yoast.com/projects#utm_source=plugin&utm_medium=promo-box&utm_campaign=i18n-promo',
	)
);
```
