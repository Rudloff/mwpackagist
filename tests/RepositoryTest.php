<?php
/**
 * RepositoryTest class.
 */
namespace MWPackagist\Tests;

use MWPackagist\Repository;

/**
 * Class used to test the Repository class.
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup variables used by the tests.
     */
    protected function setUp()
    {
        $this->repo = new Repository(__DIR__.'/../cache_test/');
    }

    /**
     * Test the getJSON() function.
     *
     * @return void
     */
    public function testGetJSON()
    {
        $this->repo->getJSON(true);
        $json = json_decode(file_get_contents(__DIR__.'/../cache_test/extensions.json'));

        $vector = $json->packages->{'mediawiki/Vector'}->{'1.26+186325f'};
        $this->assertEquals($vector->name, 'mediawiki/Vector');
        $this->assertEquals($vector->type, 'mediawiki-skin');
        $this->assertEquals(
            $vector->dist->url,
            'https://extdist.wmflabs.org/dist/skins/Vector-REL1_26-186325f.tar.gz'
        );
        $this->assertEquals(
            $vector->homepage,
            'https://www.mediawiki.org/wiki/Skin:Vector'
        );
        $this->assertEquals(
            $vector->source->url,
            'https://gerrit.wikimedia.org/r/mediawiki/skins/Vector.git'
        );
        $this->assertEquals(
            $vector->support->source,
            'https://phabricator.wikimedia.org/r/project/mediawiki/skins/Vector'
        );

        $ve = $json->packages->{'mediawiki/VisualEditor'}->{'1.26+a733545'};
        $this->assertEquals($ve->name, 'mediawiki/VisualEditor');
        $this->assertEquals($ve->type, 'mediawiki-extension');
        $this->assertEquals(
            $ve->dist->url,
            'https://extdist.wmflabs.org/dist/extensions/VisualEditor-REL1_26-a733545.tar.gz'
        );
        $this->assertEquals(
            $ve->homepage,
            'https://www.mediawiki.org/wiki/Extension:VisualEditor'
        );
        $this->assertEquals(
            $ve->source->url,
            'https://gerrit.wikimedia.org/r/mediawiki/extensions/VisualEditor.git'
        );
        $this->assertEquals(
            $ve->support->source,
            'https://phabricator.wikimedia.org/r/project/mediawiki/extensions/VisualEditor'
        );
    }

    /**
     * Test the getJSON() function with cache enabled.
     *
     * @return void
     */
    public function testGetJSONWithCache()
    {
        $this->repo->getJSON(false);
        $json = json_decode(file_get_contents(__DIR__.'/../cache_test/extensions.json'));

        $vector = $json->packages->{'mediawiki/Vector'}->{'1.26+186325f'};
        $this->assertEquals($vector->name, 'mediawiki/Vector');
        $this->assertEquals($vector->type, 'mediawiki-skin');
        $this->assertEquals(
            $vector->dist->url,
            'https://extdist.wmflabs.org/dist/skins/Vector-REL1_26-186325f.tar.gz'
        );
        $this->assertEquals(
            $vector->homepage,
            'https://www.mediawiki.org/wiki/Skin:Vector'
        );
        $this->assertEquals(
            $vector->source->url,
            'https://gerrit.wikimedia.org/r/mediawiki/skins/Vector.git'
        );
        $this->assertEquals(
            $vector->support->source,
            'https://phabricator.wikimedia.org/r/project/mediawiki/skins/Vector'
        );

        $ve = $json->packages->{'mediawiki/VisualEditor'}->{'1.26+a733545'};
        $this->assertEquals($ve->name, 'mediawiki/VisualEditor');
        $this->assertEquals($ve->type, 'mediawiki-extension');
        $this->assertEquals(
            $ve->dist->url,
            'https://extdist.wmflabs.org/dist/extensions/VisualEditor-REL1_26-a733545.tar.gz'
        );
        $this->assertEquals(
            $ve->homepage,
            'https://www.mediawiki.org/wiki/Extension:VisualEditor'
        );
        $this->assertEquals(
            $ve->source->url,
            'https://gerrit.wikimedia.org/r/mediawiki/extensions/VisualEditor.git'
        );
        $this->assertEquals(
            $ve->support->source,
            'https://phabricator.wikimedia.org/r/project/mediawiki/extensions/VisualEditor'
        );
    }
}
