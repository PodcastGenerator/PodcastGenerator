<?php

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

namespace PodcastGenerator;

/**
 * Provides access to Podcast Generator configuration settings.
 */
class Configuration implements \ArrayAccess
{
    /**
     * Loads the site config.php file into a Config object.
     *
     * @param string $configPath  Path of the config.php file for the website.
     * @return Configuration
     */
    public static function load($configPath)
    {
        $configMap = Configuration::buildConfigMap($configPath);
        return new Configuration($configPath, $configMap);
    }

    /**
     * Creates an associative array of config settings from the specified config.php file.
     *
     * @param string $configPath  Path of the config.php file for the website.
     * @return array
     */
    private static function buildConfigMap($configPath): array
    {
        $configMap = array();
        $content = file_get_contents($configPath);
        $lines = explode("\n", $content);
        for ($i = 0; $i < count($lines); $i++) {
            // Skip empty lines
            if (strlen($lines[$i]) == 0) {
                continue;
            }
            // Remove tab at the beginning
            if ($lines[$i][0] == "\t") {
                $lines[$i] = substr($lines[$i], 1);
            }
            // Skip comment and php lines
            if ($lines[$i][0] == '/' || $lines[$i][0] == '#') {
                continue;
            }

            // Skip unparseable lines
            if (
                0 === preg_match(
                    '/\$(?<var>.+?) = (?<value>(?<quote>["\'])(?<str>.+?)?\k{quote}|(?<nonstr>[^"\']+));/',
                    $lines[$i],
                    $matches
                )
            ) {
                continue;
            }

            $key = $matches['var'];
            if (isset($matches['str']) && $matches['str'] != '') {
                if ('"' == $matches['quote']) {
                    // clean escape characters
                    $configMap[$key] = stripcslashes($matches['str']);
                } else {
                    $configMap[$key] = $matches['str'];
                }
            } elseif (isset($matches['nonstr']) && $matches['nonstr'] != '') {
                $val = str_replace("\\", '', $matches['nonstr']);
                if (is_numeric($val)) {
                    $configMap[$key] = $val + 0;
                } elseif (strcasecmp('true', $val) == 0) {
                    $configMap[$key] = true;
                } elseif (strcasecmp('false', $val) == 0) {
                    $configMap[$key] = false;
                } elseif (strcasecmp('null', $val) == 0) {
                    $configMap[$key] = null;
                } else {
                    $configMap[$key] = $val;
                }
            } else {
                $configMap[$key] = '';
            }
        }

        // Pop first (<?php) element
        unset($configMap[""]);

        return $configMap;
    }

    /**
     * The absolute path of the site config.php file.
     *
     * @var string
     */
    private string $path;

    private static array $protectedKeys = array('podcastgen_version', 'absoluteurl');

    private array $map;

    private function __construct($path, $map)
    {
        $this->path = realpath($path);
        $this->map = $map;
    }

    /**
     * Gets the path of the site config.php file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Reloads configuration settings.
     *
     * WARNING: This will reset any unsaved configuration changes.
     *
     * @return void
     */
    public function reload(): void
    {
        $this->map = Configuration::buildConfigMap($this->path);
    }

    public final function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->map);
    }

    public final function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Gets the value for the specified configuration key.
     *
     * @param string $key  The configuration key to look up.
     * @return mixed       The value associated with the configuration key.
     */
    public function get(string $key): mixed
    {
        if (array_key_exists($key, $this->map)) {
            return $this->map[$key];
        }

        trigger_error('Config::get() for unknown key "' . $key . '"', E_USER_WARNING);
        return null;
    }

    public final function offsetSet($offset, $value): void
    {
        $this->set($offset, $value, false);
    }

    public final function offsetUnset($offset): void
    {
        $this->set($offset, null, false);
    }

    /**
     * Sets the value for the specified configuration key.
     *
     * @param string  $key              The configuration key to set.
     * @param mixed   $value            The value associated with the configuration key.
     * @param boolean $saveImmediately  Whether to immediately save back to config.php.
     * @return boolean                  Whether or not the value was saved back to config.php.
     */
    public function set(string $key, mixed $value, bool $saveImmediately = true): bool
    {
        // Only way to set these keys is to update config.php manually!
        if (in_array($key, Configuration::$protectedKeys)) {
            trigger_error('Special key "' . $key . '" may not be set', E_USER_ERROR);
            return false;
        }

        // It's an error to set a nonexistent config key
        if (!array_key_exists($key, $this->map)) {
            trigger_error('Config::set() for unknown key "' . $key . '"', E_USER_ERROR);
            return false;
        }

        // Save into map with appropriate value type
        if (is_bool($value) || is_int($value) || is_float($value)) {
            $this->map[$key] = $value;
        } elseif (is_numeric($value)) {
            $this->map[$key] = $value + 0; // cheap cast
        } elseif (is_string($value)) {
            $this->map[$key] = $value;
        } elseif (is_null($value)) {
            $this->map[$key] = null;
        } else {
            $this->map[$key] = (string) $value; // make sure other types are turned to string!
        }

        // Save updated config?
        if ($saveImmediately) {
            return $this->save();
        }
        return false;
    }

    /**
     * Gets the list of available configuration keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->map);
    }

    /**
     * Writes the configuration settings back to config.php.
     *
     * @return boolean  Whether or not the save was successful.
     */
    public function save(): bool
    {
        $content = file_get_contents($this->path);
        $lines = explode("\n", $content);

        $usedKeys = array();

        for ($i = 0; $i < count($lines); $i++) {
            // Skip empty lines
            if (strlen($lines[$i]) == 0) {
                continue;
            }
            // Skip comment lines
            if ($lines[$i][0] == '/' || $lines[$i][0] == '#') {
                continue;
            }

            // Remove tab at the beginning
            if ($lines[$i][0] == "\t") {
                $lines[$i] = substr($lines[$i], 1);
            }

            foreach ($this->map as $key => $value) {
                if (in_array($key, $usedKeys)) {
                    continue;
                }

                if (substr($lines[$i], 0, strlen($key) + 2) != '$' . $key . ' ') {
                    continue;
                }

                $usedKeys[] = $key;

                // Get the comment first
                $comment = strpos($lines[$i], ';');
                if ($comment) {
                    $comment = substr($lines[$i], $comment);
                    // Cut away semicolon
                    $comment = substr($comment, 1);
                }

                $lines[$i] = '$' . $key . ' = ';

                if (is_null($value)) {
                    $lines[$i] .= 'null;';
                } elseif (is_string($value)) {
                    if (preg_match('/^[\x20-\x7E]+$/', $value)) {
                        // var_export() does all we need
                        $lines[$i] .= var_export($value, true) . ';';
                    } else {
                        // addcslashes() to handle escaping for us
                        $lines[$i] .= '"' . addcslashes($value, "\0..\37$\"\\\177..\377") . '";';
                    }
                } else {
                    $lines[$i] .= var_export($value, true) . ';';
                }

                // Append comment
                $lines[$i] .= $comment;
            }
        }

        // Finally format the config file and make it "beautiful"
        $configStr = '';
        for ($i = 0; $i < count($lines); $i++) {
            if ($lines[$i] == '') {
                continue;
            }
            // Skip empty lines
            $configStr .= $lines[$i] . "\n\n";
        }

        // Write to the actual config
        return !!file_put_contents($this->path, $configStr);
    }
}
