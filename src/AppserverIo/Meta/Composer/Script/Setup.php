<?php

/**
 * AppserverIo\Meta\Composer\Script\Setup
 *
 * PHP version 5
 *
 * @category   Appserver
 * @subpackage Composer
 * @package    TechDivision_ApplicationServer
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Meta\Composer\Script;

use Composer\Script\Event;

/**
 * Class that provides functionality that'll be executed by composer
 * after installation or update of the application server.
 *
 * @category   Appserver
 * @subpackage Composer
 * @package    TechDivision_ApplicationServer
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class Setup
{

    /**
     * OS signature when calling php_uname('s') on Mac OS x 10.8.x/10.9.x.
     *
     * @var string
     */
    const DARWIN = 'Darwin';

    /**
     * OS signature when calling php_uname('s') on Linux Debian/Ubuntu/Fedora and CentOS.
     *
     * @var string
     */
    const LINUX = 'Linux';

    /**
     * OS signature when calling php_uname('s') on Windows.
     *
     * @var string
     */
    const WINDOWS = 'Windows';

    /**
     * The array with the merged and os specific template variables.
     *
     * @var array
     */
    protected static $mergedProperties = array();

    /**
     * The available properties we used for parsing the template.
     *
     * @var array
     */
    protected static $defaultProperties = array(
        'appserver.php.version' => PHP_VERSION,
        'appserver.version' => '1.0.0-alpha',
        'appserver.admin.email' => 'info@appserver.io',
        'container.server.worker.acceptMin' => 3,
        'container.server.worker.acceptMax' => 8,
        'container.http.worker.number' => 64,
        'container.https.worker.number' => 64,
        'container.persistence-container.worker.number' => 64,
        'container.memcached.worker.number' => 8,
        'container.message-queue.worker.number' => 8,
        'php-fpm.host' => '127.0.0.1',
        'php-fpm.port' => 9100,
        'appserver.umask' => 0002,
        'appserver.user' => 'nobody',
        'appserver.group' => 'nobody'
    );

    /**
     * The OS specific configuration properties.
     *
     * @var array
     */
    protected static $osProperties = array(
        'windows' => array(
            'appserver.user' => 'nobody',
            'appserver.group' => 'nobody'
         ),
        'darwin' => array(
            'appserver.user' => 'nobody',
            'appserver.group' => 'staff'
         ),
        'debian' => array(
            'appserver.user' => 'www-data',
            'appserver.group' => 'www-data'
         ),
        'fedora' => array(
            'appserver.user' => 'nobody',
            'appserver.group' => 'nobody'
         ),
        'ubuntu' => array(
            'appserver.user' => 'www-data',
            'appserver.group' => 'www-data'
         ),
        'redhat' => array(
            'appserver.user' => 'nobody',
            'appserver.group' => 'nobody'
         ),
        'centOS' => array(
            'appserver.user' => 'nobody',
            'appserver.group' => 'nobody'
         )
    );

    /**
     * Returns the Linux distribution we're running on.
     *
     * @return string The Linux distribution we're running on
     */
    public static function getLinuxDistro()
    {

        // declare Linux distros(extensible list).
        $distros = array(
            "arch"   => "arch-release",
            "debian" => "debian_version",
            "fedora" => "fedora-release",
            "ubuntu" => "lsb-release",
            'redhat' => 'redhat-release',
            'centOS' => 'centos-release'
        );

        // get everything from /etc directory.
        $etcList = scandir('/etc');

        // loop through /etc results...
        $distro;

        foreach ($etcList as $entry) { // iterate over all found files

            // loop through list of distros..
            foreach ($distros as $distroReleaseFile)  {

                // match was found.
                if ($distroReleaseFile === $entry) {

                    // find distros array key (i.e. distro name) by value (i.e. distro release file)
                    $distro = array_search($distroReleaseFile, $distros);
                    break 2;// break inner and outer loop.
                }
            }
        }

        // return the found distro string
        return $distro;
    }

    /**
     * This method will be invoked by composer after a successfull installation and creates
     * the application server configuration file under etc/appserver/appserver.xml.
     *
     * @param \Composer\Script\Event $event The event that invokes this method
     *
     * @return void
     */
    public static function postInstall(Event $event)
    {

        // $composer = $event->getComposer();
        // $event->getArguments()

        // load the OS signature
        $os = php_uname('s');

        // check what OS we are running on
        switch ($os) {

            // installation running on Linux
            case Setup::LINUX:

                // Get the distribution
                $distribution = $this->getLinuxDistribution();

                Setup::$mergedProperties = array_merge(Setup::$defaultProperties, Setup::$osProperties[$distribution]);
                break;

            // installation running on Mac OS X
            case Setup::DARWIN:

                Setup::$mergedProperties = array_merge(Setup::$defaultProperties, Setup::$osProperties[Setup::DARWIN]);
                break;

            // installation running on Windows
            case Setup::WINDOWS:

                Setup::$mergedProperties = array_merge(Setup::$defaultProperties, Setup::$osProperties[Setup::WINDOWS]);
                break;

            // all other OS are NOT supported actually
            default:

                break;
        }

        // process the template and save the configuration file
        file_put_contents('etc/appserver/appserver.xml', Setup::processTemplate());
    }

    /**
     * Returns the configuration value with the passed key.
     *
     * @return mixed|null The configuration value
     */
    public static function getValue($key)
    {
        if (array_key_exists($key, Setup::$mergedProperties)) {
            return Setup::$mergedProperties[$key];
        }
    }

    /**
     * Processes the template and replace the properties with the OS specific values.
     *
     * @return string The parsed template
     */
    public static function processTemplate()
    {
        ob_start();
        include 'resources/templates/appserver.xml.phtml';
        return ob_get_clean();
    }
}
