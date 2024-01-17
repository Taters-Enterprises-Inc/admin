<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pusher for CodeIgniter
 *
 * Simple library that wraps the Pusher PHP library (https://github.com/pusher/pusher-http-php)
 * and give access to the Pusher methods using regular CodeIgniter syntax.
 *
 * This library requires that Pusher PHP Library is installed with composer, and that CodeIgniter
 * config is set to autoload the vendor folder. More information in the CodeIgniter user guide at
 * http://www.codeigniter.com/userguide3/general/autoloader.html?highlight=composer
 *
 * @package     CodeIgniter
 * @category    Libraries
 * @author      Mattias Hedman
 * @license     MIT
 * @link        https://github.com/darkwhispering/pusher-for-codeigniter
 * @version     2.0.0
 */

Class Pusher
{

    public function __construct()
    {
        
        $dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
        $dotenv->load();

        // Get config variables
        $app_id     = $_ENV['PUSHER_APP_ID'];
        $app_key    = $_ENV['PUSHER_KEY'];
        $app_secret = $_ENV['PUSHER_SECRET'];
        $options    = $this->options();

        // // Create Pusher object only if we don't already have one
        if (!isset($this->pusher))
        {
            // Create new Pusher object
            $this->pusher =  new Pusher\Pusher($app_key, $app_secret, $app_id, $options);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Get Pusher object
     *
     * @return  Object
     */
    public function get_pusher()
    {
        return $this->pusher;
    }

    // --------------------------------------------------------------------

    /**
     * Build optional options array
     *
     * @return  array
     */
    private function options()
    {
        $options['cluster']    = $_ENV['PUSHER_CLUSTER'];
        $options['useTLS']      = true;

        $options = array_filter($options);

        return $options;
    }

    // --------------------------------------------------------------------

    /**
    * Enables the use of CI super-global without having to define an extra variable.
    * I can't remember where I first saw this, so thank you if you are the original author.
    *
    * Copied from the Ion Auth library
    *
    * @access  public
    * @param   $var
    * @return  mixed
    */
    public function __get($var)
    {
        return get_instance()->$var;
    }

}
