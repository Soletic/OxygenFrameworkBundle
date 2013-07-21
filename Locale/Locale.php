<?php
namespace Oxygen\FrameworkBundle\Locale;

use Symfony\Component\Intl\Locale\Locale as BaseLocale;

/**
 * Extend Default Locale class of Symfony
 * 
 * @author lolozere
 *
 */
class Locale extends BaseLocale {
	
	private static $locale;
	
	/**
     * Returns the default locale according the request
     *
     * @return string The default locale code.
     */
    public static function getDefault()
    {
        return self::$locale;
    }
    
    /**
     * Set default locale
     *
     * @param string $locale The locale code
     */
    public static function setDefault($locale)
    {
    	self::$locale = $locale;
    }
	
}