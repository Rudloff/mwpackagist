<?php

namespace MWPackagist\Tests;

use MWPackagist\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->repo = new Repository();
    }

    public function testGetJSON()
    {
        $json = json_decode($this->repo->getJSON(true));

        $vector = $json->packages->{'mediawiki/Vector'};
        $this->assertEquals($vector[0]->name, 'mediawiki/Vector');
        $this->assertEquals($vector[0]->type, 'mediawiki-skin');

        $vector = $json->packages->{'mediawiki/VisualEditor'};
        $this->assertEquals($vector[0]->name, 'mediawiki/VisualEditor');
        $this->assertEquals($vector[0]->type, 'mediawiki-extension');
    }

    public function testGetJSONWithCache()
    {
        $json = json_decode($this->repo->getJSON(false));

        $vector = $json->packages->{'mediawiki/Vector'};
        $this->assertEquals($vector[0]->name, 'mediawiki/Vector');
        $this->assertEquals($vector[0]->type, 'mediawiki-skin');

        $vector = $json->packages->{'mediawiki/VisualEditor'};
        $this->assertEquals($vector[0]->name, 'mediawiki/VisualEditor');
        $this->assertEquals($vector[0]->type, 'mediawiki-extension');
    }
}
