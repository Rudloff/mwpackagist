<?php

/**
 * MediawikiPackage class.
 */

namespace MWPackagist;

use Composer\Package\CompletePackage;
use Composer\Semver\VersionParser;
use Composer\Package\Link;

/**
 * Composer package for a MediaWiki extension or skin.
 */
class MediawikiPackage extends CompletePackage
{
    /**
     * Convert MediaWiki version string to semantic versioning.
     *
     * @param string $version Version string to convert
     * @param string $hash Git commit hash
     *
     * @return string Semantic version
     */
    private static function convertVersion($version, $hash = null)
    {
        if ($version == 'master') {
            return 'dev-master';
        } else {
            $version = str_replace('REL', '', $version);
            $version = str_replace('_', '.', $version);

            if (isset($hash)) {
                return $version . '+' . $hash;
            } else {
                return $version;
            }
        }
    }

    /**
     * MediawikiPackage constructor.
     * @param string $name Package name
     * @param string $type "extension" or "skin"
     * @param string $version Version string
     * @param string $hash Git commit hash
     * @param string $url Download URL
     * @param string $sourceUrl VCS URL
     */
    public function __construct($name, $type, $version, $hash, $url, $sourceUrl)
    {
        $parser = new VersionParser();

        $fullVersion = self::convertVersion($version, $hash);
        $fullName = 'mediawiki/' . $name;

        parent::__construct($fullName, $parser->normalize($fullVersion), $fullVersion);

        // We don't want a lowercase name.
        $this->name = $fullName;

        $this->setDistUrl($url);
        $this->setDistType('tar');
        $this->setType('mediawiki-' . $type);
        $this->setHomepage('https://www.mediawiki.org/wiki/' . ucfirst($type) . ':' . $name);
        $this->setSourceUrl($sourceUrl);
        $this->setSourceType('git');
        $this->setSourceReference($hash);
        $this->setSupport(
            ['source' => 'https://phabricator.wikimedia.org/r/project/mediawiki/' . $type . 's/' . $name]
        );

        $constraint = $parser->parseConstraints('~1.0');
        $this->setRequires(
            [
                new Link(
                    $fullName,
                    'composer/installers',
                    $constraint,
                    'requires',
                    $constraint->getPrettyString()
                )
            ]
        );
    }
}
