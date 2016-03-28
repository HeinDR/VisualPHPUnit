<?php
/**
 * VisualPHPUnit
 *
 * VisualPHPUnit is a visual front-end for PHPUnit.
 *
 * PHP Version 5.6<
 *
 * @author    Johannes Skov Frandsen <localgod@heaven.dk>
 * @copyright 2011-2015 VisualPHPUnit
 * @license   http://opensource.org/licenses/BSD-3-Clause The BSD License
 * @link      https://github.com/VisualPHPUnit/VisualPHPUnit VisualPHPUnit
 */
namespace Visualphpunit\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Visualphpunit\Core\Parser;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Visualphpunit\Core\Suite;

/**
 * Visualphpunit console command
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 */
class Run extends Command
{

    /**
     *
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('vpu')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of files to test')
            ->addOption('archive', 'a', InputOption::VALUE_NONE, 'Archive test result');
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new Parser();
        $result = $parser->run($input->getArgument('files'));
        if ($input->getOption('archive')) {
            Suite::store($this->getDbConnection(), $result);
        }
    }

    /**
     * Get database connection
     *
     * Get connection to database to store result of suite
     *
     * @return \Doctrine\DBAL\Connection
     */
    private function getDbConnection()
    {
        $config = json_decode(file_get_contents('../vpu.json'), true);
        $connectionParams = array(
            'path' => $config['config']['database']['path'],
            'driver' => $config['config']['database']['driver']
        );
        return DriverManager::getConnection($connectionParams, new Configuration());
    }
}
