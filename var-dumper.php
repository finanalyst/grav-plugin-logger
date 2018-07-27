<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\File\File;
use Symfony\Component\Yaml\Yaml;
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
    protected $logfile;

    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->enable( [
                'onAdminTwigTemplatePaths' => ['onAdminTwigTemplatePaths', 0],
                'onPagesInitialized' => ['onPagesInitialized', 0]
            ]);
            return;
        }
        $this->grav['dd'] = $this;
        $logf = 'dump_on_';
        $this->logfile = File::instance(DATA_DIR . $this->dump_loc . DS . $logf . date('Y-m-d') . '.yaml');
        # verify data directory exists with correct permissions
        if (!file_exists(DATA_DIR . $this->dump_loc )) {
            mkdir(DATA_DIR . $this->dump_loc , 0775, true);
        }
    }

    // where the delete button is handled.
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
                $file = DATA_DIR . $this->dump_loc . DS . $match[1];
                if (file_exists( $file ) ) {
                    unlink($file);
                }
                $this->grav->redirect("$pathParts[0]/$this->dm/$this->dump_loc" );
            }
        }
    }

    public function onAdminTwigTemplatePaths($event)
    {
//        if ($this->config->get('plugins.var-dumper.dumping') )
            $event['paths'] = [__DIR__ . '/templates'];
            $this->grav['assets']->addCss('plugin://var-dumper/css/var-dumper.css');
    }

    public function dump($inp, $comment = '') {
        $caller = array_shift(debug_backtrace(1));
        $x = explode('.', microtime(true));
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();
        $output = fopen('php://memory', 'r+b');
        $dumper->dump($cloner->cloneVar($inp), $output);
        $output = [
            'html' => stream_get_contents($output, -1, 0),
            'time' => date('H-i-s-') . $x[1],
            'comment' => $comment,
            'line' => $caller['line'],
            'file' => basename($caller['file'])
        ];
        $this->logfile->save($this->logfile->content() . Yaml::dump( [ $output ] ) );
    }
}
