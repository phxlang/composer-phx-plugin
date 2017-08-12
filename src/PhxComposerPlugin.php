<?php

namespace Phx\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PhxComposerPlugin implements PluginInterface
{

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $io->write('<info>Injecting PHX autoloader into Composer</info>');
        $composer->setAutoloadGenerator(new PhxAutoloadGenerator(
            $composer->getEventDispatcher(),
            $io
        ));
    }
}