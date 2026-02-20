<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Symfony\Framework;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Src\Identity\Infrastructure\Symfony\IdentitySymfonyProvider;
use Src\Project\Infrastructure\Symfony\ProjectSymfonyProvider;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class SymfonyKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineBundle();
    }

    protected function build(ContainerBuilder $container): void
    {
        (new IdentitySymfonyProvider())->load($container);
        (new ProjectSymfonyProvider())->load($container);
    }

    public function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../../../../../config/symfony/packages/*.yaml');
        $env = $this->getEnvironment();
        if ('test' === $env) {
            // Ensure test-specific config is loaded (import entire test dir below)
        }
        $container->import('../../../../../config/symfony/packages/'.$env.'/');
        $container->import('../../../../../config/symfony/services.yaml');
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 5);
    }
}
