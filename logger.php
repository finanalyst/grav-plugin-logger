<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\File\File;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

/**
 * Class LoggerPlugin
 * @package Grav\Plugin
 */
class LoggerPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }
        $this->grav['logger'] = $this;

        # verify data directory exists
        if (!file_exists(DATA_DIR . 'logger' )) {
        mkdir(DATA_DIR . 'logger' , 0775, true);
        }
    }

    public function log($inp, $act = 'add', $logf = 'logfile') {
        $path = DATA_DIR . 'logger' . DS . $logf . '-' . date('Y-m-d-\TH-i--') . microtime(true) . '.html';
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();
        $datafh = File::instance($path);
        $dumper->dump($cloner->cloneVar($inp), $path);
    }
}
