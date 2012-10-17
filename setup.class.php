<?php
/**
 * MODX Component Temp Setup File
 *
 * This is useful for development, when you need to add settings in Manager for your extra UI.
 *
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @version     1.0.0
 */
class Setup {

    protected $modx;
    protected $namespace;

    /**
     * @param $modx
     * @param $namespace
     * @return Setup
     */
    public static function factory($modx, $namespace)
    {
        return new self($modx, $namespace);
    }

    /**
     * @param $modx
     * @param $namespace
     */
    public function __construct($modx, $namespace)
    {
        $this->namespace = $namespace;
        $this->modx = $modx;
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

            $this->modx->log(modX::LOG_LEVEL_INFO, sprintf('Save Setting %s', $key));
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
    public function createMenu($text = '', $description = '', $parent = 'components', $save = false)
    {
        $menu = $this->modx->newObject('modMenu');
        $menu->fromArray(array(
            'text' => $text,
            'parent' => $parent,
            'description' => $description,
            'icon' => '',
            'menuindex' => 0,
            'params' => '',
            'handler' => '',
        ),'',true,true);

        if ($save) {
            $menu->save();
        }
        return $menu;
    }
}