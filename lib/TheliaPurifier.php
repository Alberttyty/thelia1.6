<?php
/* Encapsulation de HtmlPurifier */
require_once (__DIR__ . '/htmlpurifier/HTMLPurifier.auto.php');

class TheliaPurifier
{
		private static $instance = false;

		private static $purifier = false;

		private function __construct()
		{
				ActionsModules::instance()->appel_module("htmlpurifierinit");
				$this->set_front_mode();
		}

		public static function instance()
		{
				if (self::$instance === false) self::$instance = new TheliaPurifier();
				return self::$instance;
		}

		public function purifier($texte, $config = null)
		{
				return self::$purifier->purify($texte, $config);
		}

		/*
		 * The standard, restrictive, front-office htmlpurifier configuration
		 */
		public function set_front_mode()
		{
				$config = HTMLPurifier_Config::createDefault();
				$config->set('Core.Encoding', "UTF-8");

				self::$purifier = new HTMLPurifier($config);
		}

		/*
		 * A more permissive config for htmlpurifier in the back-office
		 */
		public function set_admin_mode()
		{
				$mask = '#^(http|https)://(%s)#';

				$allowURI = Variable::lire('htmlpurifier_whiteList','www.youtube.com/embed/\nplayer.vimeo.com/video/\nmaps.google.*/');

		    $config = HTMLPurifier_Config::createDefault();
		    $config->set('Core.Encoding', "UTF-8");
		    $config->set('HTML.DefinitionID', 'Thelia back-office content filter');
		    $config->set('HTML.DefinitionRev', 1);
		    $config->set('Attr.EnableID', true);
		    $config->set('CSS.AllowTricky', true);
		    $config->set('HTML.Allowed', 'a,strong,em,div,p,span,img,li,ul,ol,sup,sub,small,big,code,blockquote,h1,h2,h3,h4,h5, iframe');
		    $config->set('HTML.AllowedAttributes', 'a.href,a.title,img.src,img.alt,img.title,img.width,img.height,*.style,*.id,*.class, iframe.width, iframe.height, iframe.src, iframe.frameborder');
		    $config->set('AutoFormat.Linkify', true);
		    $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
				//Filter.Youtube est déprécié, à remplacer dans thelia 1.5.3.5, voir début de la méthode
				//$config->set('Filter.YouTube', true);
				$config->set('HTML.SafeObject',true);
	      $config->set('Output.FlashCompat', true);
	      $config->set('HTML.SafeIframe', true);
	      $config->set('URI.SafeIframeRegexp',  sprintf($mask,str_replace("\n", "|",$allowURI)));

		    $config->set('HTML.TidyLevel', 'medium');

		    // Recreate a new instance with this config
		    self::$purifier = new HTMLPurifier($config);
		}
}
?>
