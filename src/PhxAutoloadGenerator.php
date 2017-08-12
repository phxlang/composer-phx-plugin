<?php

namespace Phx\Composer;

use Composer\Autoload\AutoloadGenerator;
use Composer\Autoload\ClassLoader;
use Composer\Config;
use Composer\Installer\InstallationManager;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PhxAutoloadGenerator extends AutoloadGenerator
{

    /**
     * @param Config $config
     * @param InstalledRepositoryInterface $localRepo
     * @param PackageInterface $mainPackage
     * @param InstallationManager $installationManager
     * @param $targetDir
     * @param bool $scanPsr0Packages
     * @param string $suffix
     */
    public function dump(
        Config $config,
        InstalledRepositoryInterface $localRepo,
        PackageInterface $mainPackage,
        InstallationManager $installationManager,
        $targetDir,
        $scanPsr0Packages = false,
        $suffix = ''
    ) {
        parent::dump($config, $localRepo, $mainPackage, $installationManager, $targetDir, $scanPsr0Packages, $suffix);

        $autoloaderCode = file_get_contents(__DIR__ . '/PhxAutoloader.php');

        $filesystem = new Filesystem();
        $vendorPath = $filesystem->normalizePath(realpath(realpath($config->get('vendor-dir'))));
        $targetDir = $vendorPath.'/'.$targetDir;

        file_put_contents(
            $targetDir.'/ClassLoader.php',
            str_replace('<?php', '// @appended by PHX composer plugin', $autoloaderCode), FILE_APPEND
        );
    }

    /**
     * @param $useClassMap
     * @param $useIncludePath
     * @param $targetDirLoader
     * @param $useIncludeFiles
     * @param $vendorPathCode
     * @param $appBaseDirCode
     * @param $suffix
     * @param $useGlobalIncludePath
     * @param $prependAutoloader
     * @param int $staticPhpVersion
     * @return mixed
     */
    protected function getAutoloadRealFile(
        $useClassMap,
        $useIncludePath,
        $targetDirLoader,
        $useIncludeFiles,
        $vendorPathCode,
        $appBaseDirCode,
        $suffix,
        $useGlobalIncludePath,
        $prependAutoloader,
        $staticPhpVersion = 70000
    ) {
        $realFile = parent::getAutoloadRealFile(
            $useClassMap, $useIncludePath, $targetDirLoader, $useIncludeFiles,
            $vendorPathCode, $appBaseDirCode, $suffix, $useGlobalIncludePath,
            $prependAutoloader, $staticPhpVersion
        );

        return str_replace(ClassLoader::class, PhxAutoloader::class, $realFile);
    }
}
