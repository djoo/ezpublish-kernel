<?php
/**
 * This file is part of the eZ Publish Kernel package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformInstallerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpSchemaCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('ezplatform:dump-schema');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getContainer()->get('database_connection');
        $sql = $connection
            ->getSchemaManager()
            ->createSchema()
            ->toSql($connection->getDatabasePlatform());
        print_r($sql);
        
    }
}
