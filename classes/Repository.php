<?php
/**
 * Repository class.
 */
namespace MWPackagist;

/**
 * Class used to create the Composer repository.
 */
class Repository
{
    /**
     * Base MediaWiki API URL.
     *
     * @var string
     */
    private $apiUrl = 'https://www.mediawiki.org/w/api.php';

    /**
     * SimpleCache instance.
     *
     * @var \Gilbitron\Util\SimpleCache
     */
    private $cache;

    /**
     * Repository constructor.
     *
     * @param string $cachePath Path to cache
     */
    public function __construct($cachePath = null)
    {
        $this->cache = new \Gilbitron\Util\SimpleCache();
        $this->cache->cache_extension = '.json';
        $this->cache->cache_time = 86400;
        if (isset($cachePath)) {
            $this->cache->cache_path = $cachePath;
        }
    }

    /**
     * Convert MediaWiki version string to semantic versioning.
     *
     * @param string $version Version string to convert
     * @param string $hash    Git commit hash
     *
     * @return string Semantic version
     */
    private function convertVersion($version, $hash)
    {
        if ($version == 'master') {
            return 'dev-master';
        } else {
            $version = str_replace('REL', '', $version);
            $version = str_replace('_', '.', $version);

            return $version.'+'.$hash;
        }
    }

    /**
     * Get packages from MediaWiki's extension repository.
     *
     * @param string[] $subset List of packages to get
     * @param string   $range  Request range used to split requests in several parts (xx-yy)
     * @param bool     $skin   Do we want skins instead of extensions?
     * @param bool     $force  Ignore cache?
     *
     * @return array List of packages
     */
    private function getPackages($subset, $range, $skin = false, $force = false)
    {
        if ($skin) {
            $type = 'skin';
        } else {
            $type = 'extension';
        }
        if (!$force && $this->cache->is_cached($type.'-'.$range)) {
            $extInfoJson = $this->cache->get_cache($type.'-'.$range);
        } else {
            $url = $this->apiUrl.
            '?action=query&format=json&list=extdistbranches';
            if ($skin) {
                $url .= '&edbskins=';
            } else {
                $url .= '&edbexts=';
            }
            $extInfoJson = $this->cache->do_curl($url.implode('|', $subset));
            $this->cache->set_cache($type.'-'.$range, $extInfoJson);
        }
        $extInfo = json_decode($extInfoJson);
        foreach ($subset as $plugin) {
            $composerName = 'mediawiki/'.$plugin;
            $package = [];
            if ($skin) {
                $list = $extInfo->query->extdistbranches->skins;
            } else {
                $list = $extInfo->query->extdistbranches->extensions;
            }
            foreach ($list->$plugin as $version => $url) {
                preg_match('/(REL1_[0-9][0-9]|master)-(\w+)\.tar\.gz/', $url, $versionParts);
                if (isset($versionParts[2])) {
                    $package[self::convertVersion($version, $versionParts[2])] = [
                        'name'     => $composerName,
                        'version'  => self::convertVersion($version, $versionParts[2]),
                        'keywords' => ['mediawiki'],
                        'dist'     => [
                            'url'  => $url,
                            'type' => 'tar',
                        ],
                        'type'    => 'mediawiki-'.$type,
                        'require' => [
                            'composer/installers' => '~1.0',
                        ],
                        'homepage' => 'https://www.mediawiki.org/wiki/'.ucfirst($type).':'.$plugin,
                        'source'   => [
                            'url'       => $list->$plugin->source,
                            'type'      => 'git',
                            'reference' => $versionParts[2],
                        ],
                        'support' => [
                            'source' => 'https://phabricator.wikimedia.org/r/project/mediawiki/'.$type.'s/'.$plugin,
                        ],
                    ];
                }
            }
            $packages[$composerName] = $package;
        }

        return $packages;
    }

    /**
     * Generate Composer repository JSON.
     *
     * @param bool $force Ignore cache?
     *
     * @return string JSON containing paths to other included JSON files
     */
    public function getJSON($force = false)
    {
        $packages = [];
        $json = json_decode(
            file_get_contents(
                $this->apiUrl.'?action=query&list=extdistrepos&format=json'
            )
        );
        $extensions = $json->query->extdistrepos->extensions;
        $skins = $json->query->extdistrepos->skins;

        for ($i = 0; $i < count($extensions); $i += 50) {
            $subset = array_slice($extensions, $i, 50);
            $range = $i.'-'.($i + count($subset) - 1);
            $packages = array_merge($packages, $this->getPackages($subset, $range, false, $force));
        }
        for ($i = 0; $i < count($skins); $i += 50) {
            $subset = array_slice($skins, $i, 50);
            $range = $i.'-'.($i + count($subset) - 1);
            $packages = array_merge($packages, $this->getPackages($subset, $range, true, $force));
        }
        $json = json_encode(
            ['packages' => $packages]
        );
        $this->cache->set_cache('extensions', $json);
        $includes = ['cache/extensions.json' => ['sha1' => sha1($json)]];
        if (is_file(__DIR__.'/../include.json')) {
            $includes['include.json'] = ['sha1' => sha1_file(__DIR__.'/../include.json')];
        }

        return json_encode(
            [
                'includes' => $includes,
            ]
        );
    }
}
