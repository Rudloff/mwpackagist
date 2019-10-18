<?php

/**
 * BuildCommand class.
 */

namespace MWPackagist;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Satis\Builder\PackagesBuilder;
use Composer\Satis\Builder\WebBuilder;
use Composer\Satis\PackageSelection\PackageSelection;
use Composer\Package\RootPackage;
use Composer\Repository\VcsRepository;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\IO\NullIO;
use Composer\Config;

/**
 * CLI command that builds the repository.
 */
class BuildCommand extends Command
{
    /**
     * Command name.
     * @var string
     */
    protected static $defaultName = 'build';

    /**
     * Set command options.
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Build the repository');
    }

    /**
     * Execute the command.
     * @param  InputInterface  $input  Input
     * @param  OutputInterface $output Output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helperSet = $this->getHelperSet();
        if (isset($helperSet)) {
            $io = new ConsoleIO($input, $output, $helperSet);
        } else {
            // If console IO is not available for some reason.
            $io = new NullIO();
        }

        $rootDir = dirname(__DIR__);

        $rootPackage = new RootPackage('rudloff/mwpackagist', '2.0', '2.0');
        $rootPackage->setHomepage('https://mwpackagist.netlib.re/');
        $rootPackage->setDescription('Install and manage MediaWiki extensions/skins with Composer');

        // We don't want to look on Packagist.
        unset(Config::$defaultRepositories['packagist'], Config::$defaultRepositories['packagist.org']);

        $composer = Factory::create($io);

        // Look for the core in this repository.
        $composer->getRepositoryManager()
            ->addRepository(
                new VcsRepository(
                    ['url' => 'https://github.com/wikimedia/mediawiki.git'],
                    $io,
                    Factory::createConfig($io)
                )
            );

        // Fetch core packages.
        $packageSelection = new PackageSelection($output, $rootDir, [], false);
        $corePackages = $packageSelection->select($composer, true);

        // Fetch extensions and skins.
        $repo = new Repository($io);
        $contribPackages = $repo->getAllPackages();

        $packages = array_merge($corePackages, $contribPackages);

        // Build JSON file.
        $builder = new PackagesBuilder($output, $rootDir, [], false);
        $builder->dump($packages);

        // Build HTML page.
        $web = new WebBuilder($output, $rootDir . '/repo/', [], false);
        $web->setRootPackage($rootPackage);
        $web->dump($packages);
    }
}
