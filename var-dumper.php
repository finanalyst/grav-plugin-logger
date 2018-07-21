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
class VarDumperPlugin extends Plugin
{
    protected $dm = 'data-manager'; // because we are operating inside data manager page
    protected $dump_loc = 'var-dumps';

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
            $this->enable( [
                'onAdminTwigTemplatePaths' => ['onAdminTwigTemplatePaths', 0],
                'onPagesInitialized' => ['onPagesInitialized', 100]
            ]);
            return;
        }
        $this->grav['dd'] = $this;

        # verify data directory exists
        if (!file_exists(DATA_DIR . $this->dump_loc )) {
            mkdir(DATA_DIR . $this->dump_loc , 0775, true);
        }
    }

    public function onPagesInitialized() {
        $uri = $this->grav['uri'];
        if (strpos($uri->path(), $this->config->get('plugins.admin.route') . '/' . $this->dm . '/' . $this->dump_loc ) === false) {
            return;
        }
        $pathParts = $uri->paths();
        if (isset($pathParts[1]) && $pathParts[1] === $this->dm
            && isset($pathParts[2]) && $pathParts[2] === $this->dump_loc
            && isset($pathParts[3]) ) {
            if (preg_match( '/^vdmpdelete_(.+)/', $pathParts[3], $match)) {
                $file = DATA_DIR . $this->dump_loc . DS . $match[1] . '.html';
                if (file_exists( $file ) ) {
                    unlink($file);
                }
                $this->grav->redirect("$pathParts[0]/$this->dm/$this->dump_loc" );
            }
        }
    }

    public function onAdminTwigTemplatePaths($event)
    {
        $event['paths'] = [__DIR__ . '/templates'];
    }

    public function dump($inp, $act = 'add', $logf = 'dump_at_') {
        $x = explode('.',microtime(true));
        $path = DATA_DIR . $this->dump_loc . DS . $logf . '-' . date('Y-m-d-\TH-i-s-') . $x[1] . '.html';
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();
        $datafh = File::instance($path);
        $dumper->dump($cloner->cloneVar($inp), $path);
    }
}
