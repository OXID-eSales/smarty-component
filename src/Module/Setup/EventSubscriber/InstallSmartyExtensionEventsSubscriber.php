<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Smarty\Module\Setup\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\Smarty\Module\Setup\Handler\ModuleConfigurationHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InstallSmartyExtensionEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ModuleConfigurationHandlerInterface $blocksConfigurationHandler,
        private ModuleConfigurationHandlerInterface $pluginsConfigurationHandler)
    {
    }

    /**
     * @param FinalizingModuleActivationEvent $event
     */
    public function handleOnModuleActivation(FinalizingModuleActivationEvent $event): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($event->getModuleId(), $event->getShopId());
        $this->blocksConfigurationHandler->handleOnModuleActivation($moduleConfiguration, $event->getShopId());
        $this->pluginsConfigurationHandler->handleOnModuleActivation($moduleConfiguration, $event->getShopId());
    }

    /**
     * @param BeforeModuleDeactivationEvent $event
     */
    public function handleOnModuleDeactivation(BeforeModuleDeactivationEvent $event): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($event->getModuleId(), $event->getShopId());
        $this->blocksConfigurationHandler->handleOnModuleDeactivation($moduleConfiguration, $event->getShopId());
        $this->pluginsConfigurationHandler->handleOnModuleDeactivation($moduleConfiguration, $event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::class   => 'handleOnModuleActivation',
            BeforeModuleDeactivationEvent::class     => 'handleOnModuleDeactivation',
        ];
    }
}
