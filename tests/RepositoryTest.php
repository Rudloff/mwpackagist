<?php

namespace MWPackagist\Tests;

use MWPackagist\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->repo = new Repository(__DIR__.'/../cache_test/');
    }

    public function testGetJSON()
    {
        $this->repo->getJSON(true);
        $json = json_decode(file_get_contents(__DIR__.'/../cache_test/extensions.json'));

        $vector = $json->packages->{'mediawiki/Vector'}->{'dev-master'};
        $this->assertEquals($vector->name, 'mediawiki/Vector');
        $this->assertEquals($vector->type, 'mediawiki-skin');
        $this->assertContains(
            'https://extdist.wmflabs.org/dist/skins/',
            $vector->dist->url
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

        $ve = $json->packages->{'mediawiki/VisualEditor'}->{'dev-master'};
        $this->assertEquals($ve->name, 'mediawiki/VisualEditor');
        $this->assertEquals($ve->type, 'mediawiki-extension');
        $this->assertContains(
            'https://extdist.wmflabs.org/dist/extensions/',
            $ve->dist->url
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

    public function testGetJSONWithCache()
    {
        $this->repo->getJSON(false);
        $json = json_decode(file_get_contents(__DIR__.'/../cache_test/extensions.json'));

        $vector = $json->packages->{'mediawiki/Vector'}->{'dev-master'};
        $this->assertEquals($vector->name, 'mediawiki/Vector');
        $this->assertEquals($vector->type, 'mediawiki-skin');
        $this->assertContains(
            'https://extdist.wmflabs.org/dist/skins/',
            $vector->dist->url
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

        $ve = $json->packages->{'mediawiki/VisualEditor'}->{'dev-master'};
        $this->assertEquals($ve->name, 'mediawiki/VisualEditor');
        $this->assertEquals($ve->type, 'mediawiki-extension');
        $this->assertContains(
            'https://extdist.wmflabs.org/dist/extensions/',
            $ve->dist->url
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
