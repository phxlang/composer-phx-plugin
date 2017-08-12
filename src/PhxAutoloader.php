<?php

namespace Phx\Composer;

use Composer\Autoload\ClassLoader;
use Phx\Common\PhxTranspilerBuilder;
use Phx\Common\Transpiler;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PhxAutoloader extends ClassLoader
{

    /**
     * @var Transpiler
     */
    private $transpiler;

    /**
     * @var \ReflectionMethod
     */
    private $findFileWithExtensionMethod;

    public function __construct()
    {
        $this->findFileWithExtensionMethod = new \ReflectionMethod(PhxAutoloader::class, 'findFileWithExtension');
        $this->findFileWithExtensionMethod->setAccessible(true);
    }

    public function loadClass($class)
    {
        if (true === parent::loadClass($class)) {
            return true;
        }

        if (false !== $filePath = $this->findFileWithExtensionMethod->invoke($this, $class, '.phx')) {
            if (null === $this->transpiler) {
                $this->transpiler = PhxTranspilerBuilder::create()->build();
            }

            includeCode($this->transpiler->fromFile($filePath));
            return true;
        }
    }
}

function includeCode($code) {
    eval($code);
}
