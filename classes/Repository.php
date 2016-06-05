<?php

namespace MWPackagist;

class Repository
{

    private $apiUrl = 'https://www.mediawiki.org/w/api.php';

    public function __construct()
    {
        $this->cache = new \Gilbitron\Util\SimpleCache();
    }

    private function convertVersion($version)
    {
        $version = str_replace('REL', '', $version);
        $version = str_replace('_', '.', $version);
        return $version;
    }

    private function getPackages($subset, $range, $skin = false, $force = false)
    {
        if (!$force && $this->cache->is_cached($range)) {
            $extInfoJson = $this->cache->get_cache($range);
        } else {
            $url = $this->apiUrl.
            '?action=query&format=json&list=extdistbranches';
            if ($skin) {
                $url .= '&edbskins=';
            } else {
                $url .= '&edbexts=';
            }
            $extInfoJson = $this->cache->do_curl($url.implode('|', $subset));
            $this->cache->set_cache($range, $extInfoJson);
        }
        $extInfo = json_decode($extInfoJson);
        if ($skin) {
            $type = 'mediawiki-skin';
        } else {
            $type = 'mediawiki-extension';
        }
        foreach ($subset as $plugin) {
            $composerName = 'mediawiki/'.$plugin;
            $package = array();
            if ($skin) {
                $list = $extInfo->query->extdistbranches->skins;
            } else {
                $list = $extInfo->query->extdistbranches->extensions;
            }
            foreach ($list->$plugin as $version => $url) {
                $package[] = array(
                    'name'=>$composerName,
                    'version'=>self::convertVersion($version),
                    'dist'=>array(
                        'url'=>$url,
                        'type'=>'tar'
                    ),
                    'type'=>$type,
                    'require'=>array(
                        'composer/installers'=>'~1.0'
                    )
                );
            }
            $packages[$composerName] = $package;
        }
        return $packages;
    }

    public function getJSON($force = false)
    {
        $packages = array();
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
            $packages = array_merge($packages, $this->getPackages($subset, 'extensions-'.$range, false, $force));
        }
        for ($i = 0; $i < count($skins); $i += 50) {
            $subset = array_slice($skins, $i, 50);
            $range = $i.'-'.($i + count($subset) - 1);
            $packages = array_merge($packages, $this->getPackages($subset, 'skins-'.$range, true, $force));
        }
        return json_encode(
            array(
                'packages'=>$packages,
                'includes'=>array(
                    'include.json'=>array('sha1'=>sha1_file(__DIR__.'/../include.json'))
                )
            )
        );

    }
}
