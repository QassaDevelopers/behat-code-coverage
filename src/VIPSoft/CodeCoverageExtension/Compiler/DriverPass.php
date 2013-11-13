<?php
/**
 * Driver Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Driver pass - register only the enabled drivers
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class DriverPass implements CompilerPassInterface
{
    /**
     * Processes container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('behat.code_coverage.driver.proxy')) {
            return;
        }

        $proxy = $container->getDefinition('behat.code_coverage.driver.proxy');
        $enabled = $container->getParameter('behat.code_coverage.config.drivers');

        foreach ($container->findTaggedServiceIds('behat.code_coverage.driver') as $id => $attributes) {
            if (strncmp($id, 'behat.code_coverage.driver.', 27) === 0
                && in_array(substr($id, 27), $enabled)
            ) {
                $proxy->addMethodCall('addDriver', array(new Reference($id)));
            }
        }
    }
}
