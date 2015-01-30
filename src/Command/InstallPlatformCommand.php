<?php
/**
 * This file is part of the eZ Publish Kernel package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformInstallerBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallPlatformCommand extends ContainerAwareCommand
{
    /** @var \Doctrine\DBAL\Connection */
    private $db;

    protected function configure()
    {
        $this->setName( 'ezplatform:install' );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        try {
            if (!$this->configuredDatabaseExists()) {
                $output->writeln(
                    sprintf(
                        "The configured database '%s' does not exist",
                        $this->db->getDatabase()
                    )
                );
                exit;
            }
        } catch (ConnectionException $e) {
            $output->writeln("An error occured connecting to the database:");
            $output->writeln($e->getMessage());
            $output->writeln("Please check the database configuration in parameters.yml");
            exit;
        }
    }

    private function configuredDatabaseExists()
    {
        $this->db = $this->getContainer()->get( 'database_connection' );
        try {
            $this->db->connect();
        } catch ( ConnectionException $e ) {
            // 1049 is MySQL's code, enhance
            if ( $e->getPrevious()->getCode() == 1049 )
            {
                return false;
            }
            throw $e;
        }
        return true;
    }
}
