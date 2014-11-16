# Yoast i18n module

Handle i18n for plugins and promote your own translation site in the process. 

## How to use this module
Just include the library as a submodule, make sure the class is loaded and extend the abstract class within it with just an init method and instantiate it, like this:

```php
class yoast_i18n_wpseo extends yoast_i18n {
	
	/** 
	 * Initialize with some variables.
	 */
	public function init() {
		$this->textdomain              = 'wordpress-seo';
		$this->project_slug            = 'wordpress-seo';
		$this->plugin_name             = 'WordPress SEO by Yoast';
		$this->hook 		           = 'wpseo_admin_footer';
		$this->translate_project_url   = 'http://translate.yoast.com/';
		$this->translate_project_name  = 'Yoast Translate';
		$this->translate_project_logo  = 'https://cdn.yoast.com/wp-content/uploads/i18n-images/Yoast_Translate.svg';
	}
	
}
$yoast_i18n_wpseo = new yoast_i18n_wpseo();
```

