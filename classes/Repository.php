<?php

/**
 * Repository class.
 */

namespace MWPackagist;

use Exception;
use Composer\IO\IOInterface;
use Composer\Util\RemoteFilesystem;
use Composer\Json\JsonFile;

/**
 * Class used to create the Composer repository.
 */
class Repository
{

    /**
     * Interface used to write to the console.
     * @var IOInterface
     */
    private $io;

    /**
     * Helper class used to fetch remote JSON files.
     * @var RemoteFilesystem
     */
    private $rfs;

    /**
     * Base MediaWiki API URL.
     *
     * @var string
     */
    private $apiUrl = 'https://www.mediawiki.org/w/api.php';


    /**
     * Repository constructor.
     * @param IOInterface $io Interface used to write to the console
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
        $this->rfs = new RemoteFilesystem($this->io);
    }

    /**
     * Get packages from MediaWiki's extension repository.
     *
     * @param string[] $subset List of package names to get
     * @param string $range Request range used to split requests in several parts (xx-yy)
     * @param string $type "extension" or "skin"
     *
     * @return MediawikiPackage[] Packages
     * @throws Exception
     */
    private function getPackages(array $subset, $range, $type)
    {
        $params = [
            'action' => 'query',
            'format' => 'json',
            'list' => 'extdistbranches'
        ];

        if ($type == 'skin') {
            $params['edbskins'] = implode('|', $subset);
        } elseif ($type == 'extension') {
            $params['edbexts'] = implode('|', $subset);
        } else {
            throw new Exception('Invalid package type.');
        }

        $extInfo = JsonFile::parseJson(
            (string)$this->rfs->getContents(
                (string)parse_url($this->apiUrl, PHP_URL_HOST),
                $this->apiUrl . '?' . http_build_query($params),
                false
            )
        );

        $packages = [];
        foreach ($subset as $name) {
            $list = $extInfo['query']['extdistbranches'][$type . 's'];

            foreach ($list[$name] as $version => $url) {
                preg_match('/(REL1_[0-9][0-9]|master)-(\w+)\.tar\.gz/', $url, $versionParts);
                if (isset($versionParts[2])) {
                    $packages[] = new MediawikiPackage(
                        $name,
                        $type,
                        $version,
                        $versionParts[2],
                        $url,
                        $list[$name]['source']
                    );
                }
            }
        }

        return $packages;
    }

    /**
     * Get all extensions or skins.
     *
     * @param string[] $packages List of package names to get
     * @param string $type "skin" or "extension"
     *
     * @return MediawikiPackage[] Packages
     * @throws Exception
     */
    private function getAllPackagesFromType(array $packages, $type)
    {
        $packagesNb = count($packages);

        $results = [];
        for ($i = 0; $i < $packagesNb; $i += 50) {
            $subset = array_slice($packages, $i, 50);
            $range = $i . '-' . ($i + count($subset) - 1);
            $results = array_merge($results, $this->getPackages($subset, $range, $type));
        }

        return $results;
    }

    /**
     * Fetch all packages.
     *
     * @return MediawikiPackage[] Packages
     * @throws Exception
     */
    public function getAllPackages()
    {
        $packages = [];

        $json = JsonFile::parseJson(
            (string)$this->rfs->getContents(
                (string)parse_url($this->apiUrl, PHP_URL_HOST),
                $this->apiUrl . '?' .
                http_build_query(['action' => 'query', 'list' => 'extdistrepos', 'format' => 'json']),
                false
            )
        );

        $extensions = $json['query']['extdistrepos']['extensions'];
        $skins = $json['query']['extdistrepos']['skins'];

        $this->io->write('Fetching extensions');
        $packages = array_merge($packages, $this->getAllPackagesFromType($extensions, 'extension'));

        $this->io->write('Fetching skins');
        $packages = array_merge($packages, $this->getAllPackagesFromType($skins, 'skin'));

        return $packages;
    }
}
