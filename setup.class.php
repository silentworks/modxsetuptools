<?php
/**
 * MODX Component Temp Setup File
 *
 * This is useful for development, when you need to add settings in Manager for your extra UI.
 *
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @version     1.0.4
 */
class Setup {

    protected $modx;
    protected $namespace;
    protected $config = array(
        'tvs' => '',
        'chunks' => '',
        'plugins' => '',
        'snippets' => '',
        'logLevel' => modX::LOG_LEVEL_INFO
    );

    /**
     * @param modX $modx
     * @param $namespace
     * @param array $config
     * @return Setup
     */
    public static function factory(modX $modx, $namespace = '', array $config = array())
    {
        return new self($modx, $namespace, $config);
    }

    /**
     * @param $modx
     * @param $namespace
     * @param $config
     */
    public function __construct($modx, $namespace = '', $config)
    {
        $this->namespace = $namespace;
        $this->modx = $modx;
        if (! empty($config) && is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * @param $key
     * @param $value
     * @return Setup
     */
    public function createSetting($key, $value)
    {
        $key = $this->namespace . '.' . $key;

        $ct = $this->modx->getCount('modSystemSetting', array(
            'key' => $key
        ));

        if (empty($ct)) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('key', $key);
            $setting->set('value', $value);
            $setting->set('namespace', $this->namespace);
            $setting->set('area', 'Paths');
            $setting->save();

            $this->log(sprintf('Save Setting %s', $key));
        }
        return $this;
    }

    /**
     * @param $path
     * @param bool $save
     * @return mixed
     */
    public function createNamespace($path, $save = false)
    {
        $ns = $this->modx->getCount('modNamespace', array(
            'name' => $this->namespace
        ));

        if (empty($ns)) {
            $namespace = $this->modx->newObject('modNamespace');
            $namespace->set('name', $this->namespace);
            $namespace->set('path', $path);

            if ($save) {
                $namespace->save();
            }

            return $namespace;
        }
    }

    /**
     * @param string $controller
     * @param bool $save
     * @return mixed
     */
    public function createAction($controller = 'controllers/index', $save = false)
    {
        $action = $this->modx->newObject('modAction');
        $action->fromArray(array(
            'parent' => 0,
            'controller' => $controller,
            'haslayout' => 1,
            'lang_topics' => sprintf('%s:default', $this->namespace),
            'assets' => '',
        ),'',true,true);

        if ($save) {
            $action->save();
        }
        return $action;
    }

    /**
     * @param string $text
     * @param string $description
     * @param string $parent
     * @param bool $save
     * @return mixed
     */
    public function createMenu($text = '', $description = '', $parent = 'components', array $extraParams = array(), $save = false)
    {
        $cm = $this->modx->getCount('modMenu', array(
            'text' => $text
        ));

        $options = array(
            'text' => $text,
            'parent' => $parent,
            'description' => $description,
            'icon' => '',
            'menuindex' => 0,
            'params' => '',
            'handler' => '',
        );

        if (!empty($extraParams)) {
            $options = array_merge($options, $extraParams);
        }

        if (!empty($cm)) {
            $this->log(sprintf('Menu already exist with the name %s', $text));
        } else {
            $menu = $this->modx->newObject('modMenu');
            $menu->fromArray($options,'',true,true);

            if ($save) {
                $menu->save();
            }
            return $menu;
        }
    }

    /**
     * @param array $snippet
     * @return Setup
     */
    public function createSnippet(array $snippet)
    {
        $snippets = array();
        $i = 0;

        if (is_array($snippet)) {
            foreach ($snippet as $k => $opts) {
                $i++;
                $filename = strtolower($k);
                $file = $this->config['snippets'] . 'snippet.' . $filename . '.php';

                if (file_exists($file)) {
                    $content = $this->getFileContent($file);
                } else {
                    $content = $opts['content'];
                }

                /* Count Item */
                $cnt = $this->modx->getCount('modSnippet', array(
                    'name' => $k
                ));

                if (!empty($cnt)) {
                    $this->log(sprintf('Snippet already exist with the name %s', $k));
                } else {
                    if (! is_array($opts)) {
                        $opts = array('desc' => $opts);
                    }

                    $options = array(
                        'name' => $k,
                        'description' => $opts['desc'],
                        'snippet' => $content,
                    );

                    if (in_array('static', $opts)) {
                        $options = array_merge($options, array(
                            'static' => $opts['static'],
                            'static_file' => str_replace(MODX_ROOT, '', $file),
                        ));
                    }

                    $snippets[$i] = $this->modx->newObject('modSnippet');
                    $snippets[$i]->fromArray($options, '', true, true);
                    $snippets[$i]->save();
                }
            }
        }
        return $this;
    }

    /**
     * @param array $chunk
     * @return Setup
     */
    public function createChunk(array $chunk)
    {
        $chunks = array();
        $i = 0;

        if (is_array($chunk)) {
            foreach ($chunk as $k => $opts) {
                $i++;
                $filename = strtolower($k);
                $file = $this->config['chunks'] . $filename . '.chunk.tpl';

                if (file_exists($file)) {
                    $content = $this->getFileContent($file, false);
                } else {
                    $content = $opts['content'];
                }

                /* Count Item */
                $cnt = $this->modx->getCount('modChunk', array(
                    'name' => $k
                ));

                if (!empty($cnt)) {
                    $this->log(sprintf('Chunk already exist with the name %s', $k));
                } else {
                    if (! is_array($opts)) {
                        $opts = array('desc' => $opts);
                    }

                    $options = array(
                        'name' => $k,
                        'description' => $opts['desc'],
                        'snippet' => $content,
                    );

                    if (in_array('static', $opts)) {
                        $options = array_merge($options, array(
                            'static' => $opts['static'],
                            'static_file' => str_replace(MODX_ROOT, '', $file),
                        ));
                    }

                    $chunks[$i] = $this->modx->newObject('modChunk');
                    $chunks[$i]->fromArray($options, '', true, true);
                    $chunks[$i]->save();
                }
            }
        }
        return $this;
    }

    /**
     * @param array $plugin
     * @return Setup
     */
    public function createPlugin(array $plugin)
    {
        $plugins = array();
        $i = 0;

        if (is_array($plugin)) {
            foreach ($plugin as $k => $opts) {
                $i++;
                $filename = strtolower($k);
                $file = $this->config['plugins'] . 'plugin.' . $filename . '.php';

                // Check if file exist otherwise pass as string
                if (file_exists($file)) {
                    $content = $this->getFileContent($file);
                } else {
                    $content = $opts['content'];
                }

                /* Count Item */
                $cnt = $this->modx->getCount('modPlugin', array(
                    'name' => $k
                ));

                if (!empty($cnt)) {
                    $this->log(sprintf('Plugin already exist with the name %s', $ch));
                } else {
                    if (! is_array($opts)) {
                        $opts = array('desc' => $opts);
                    }

                    $options = array(
                        'name' => $k,
                        'description' => $opts['desc'],
                        'plugincode' => $content,
                    );

                    if (in_array('static', $opts)) {
                        $options = array_merge($options, array(
                            'static' => $opts['static'],
                            'static_file' => str_replace(MODX_ROOT, '', $file),
                        ));
                    }

                    $plugins[$i] = $this->modx->newObject('modPlugin');
                    $plugins[$i]->fromArray($options, '', true, true);

                    $events = array();
                    foreach ($opts['events'] as $event) {
                        $events[$event] = $this->modx->newObject('modPluginEvent');
                        $events[$event]->fromArray(array(
                            'event' => $event,
                            'priority' => 0,
                            'propertyset' => 0,
                        ), '', true, true);
                    }

                    if (! empty($events)) {
                        $plugins[$i]->addMany($events);
                    }

                    $plugins[$i]->save();
                    unset($events);
                }
            }
        }
        return $this;
    }

    /**
     * @param array $chunk
     * @return Setup
     */
    public function createTV(array $templateVariable)
    {
        $tvs = array();
        $i = 0;

        if (is_array($templateVariable)) {
            foreach ($templateVariable as $k => $tv) {
                $i++;
                $filename = strtolower($k);
                $file = $this->config['tvs'] . 'tv.' . $filename . '.php';

                /* Count Item */
                $cnt = $this->modx->getCount('modTemplateVar', array(
                    'name' => $k
                ));

                if (!empty($cnt)) {
                    $this->log(sprintf('TV already exist with the name %s', $ch));
                } else {
                    $tvs[$i] = $this->modx->newObject('modTemplateVar');
                    $tvs[$i]->fromArray(array(
                        'type' => $tv['type'],
                        'caption' => in_array('caption', $tv) ? $tv['caption'] : '',
                        'name' => $k,
                        'description' => in_array('desc', $tv) ? $tv['desc'] : '',
                        'category' => in_array('category', $tv) ? $tv['category'] : 0,
                        'locked' => in_array('locked', $tv) ? $tv['locked'] : 0,
                        'elements' => in_array('elements', $tv) ? $tv['elements'] : NULL,
                        'rank' => in_array('rank', $tv) ? $tv['rank'] : 0,
                        'display' => in_array('display', $tv) ? $tv['display'] : 'default',
                        'display_params' => in_array('display_params', $tv) ? $tv['display_params'] : NULL,
                        'default_text' => in_array('default_text', $tv) ? $tv['default_text'] : NULL,
                    ), '', true, true);
                    $tvs[$i]->save();
                }
            }
        }
        return $this;
    }

    /**
     * @param $message
     * @throws Exception
     */
    public function log($message)
    {
        if (empty($this->config['logLevel'])) {
            throw new Exception('Logging level needs to be set');
        }
        $this->modx->log($this->config['logLevel'], $message);
    }

    /**
     * @param $filename
     * @param bool $snippet
     * @return string
     */
    private function getFileContent($filename, $snippet = true)
    {
        $o = file_get_contents($filename);
        if ($snippet) {
            $o = str_replace('<?php','',$o);
            $o = str_replace('?>','',$o);
        }
        $o = trim($o);
        return $o;
    }
}
