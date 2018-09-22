<?php

namespace CodeHuiter\Services;

use CodeHuiter\Modifiers\StringModifier;

class Language
{
    /** @var array  */
    protected $cache = [];

    protected $loaded = [];

    /** @var string  */
    protected $language = 'undefined';

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get string in language volume
     * @param string $alias alias of the string
     * @param array $replacePairs key -> value replace pairs
     * @return string
     */
    public function get($alias, $replacePairs = [])
    {
        if (!isset($this->cache[$alias])) {
            $volumeArr = explode(':', $alias);
            $volume = (count($volumeArr) > 1) ? $volumeArr[0] : 'default';

            if (!isset($this->loaded[$volume])) {
                $this->loadVolume($volume);
            }
            if (!isset($this->cache[$alias])) {
                $this->writeToVolume($volume, $alias, array_keys($replacePairs));
                $this->cache[$alias] = 'Undefined#' . $alias;
            }
        }
        if ($replacePairs) {
            return StringModifier::replace($this->cache[$alias], $replacePairs);
        }
        return $this->cache[$alias];
    }

    protected function loadVolume($volume)
    {
        $volumeFile = $this->getVolumeFile($volume);
        if (!file_exists($volumeFile)) {
            return;
        }
        require($volumeFile);

        if (isset($lang) && is_array($lang)) {
            foreach ($lang as $key => $value) {
                $this->cache[$key] = $value;
            }
        }

        $this->loaded[$volume] = true;
    }

    protected function writeToVolume($volume, $alias, $keys)
    {
        $volumeFile = $this->getVolumeFile($volume);
        if (!file_exists($volumeFile)) {
            file_put_contents($volumeFile, "<?php\n\n");
        }
        $addedKeys = ($keys) ? ' ' . implode(' ', $keys) : '';
        file_put_contents($volumeFile, "\$lang['{$alias}'] = '{$alias}{$addedKeys}';\n", FILE_APPEND);
    }

    protected function getVolumeFile($volume)
    {
        return APP_PATH . 'Language/' . $this->language . '/' . $volume . '.php';
    }
}
