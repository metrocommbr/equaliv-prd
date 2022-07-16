<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-reports
 * @version   1.4.4
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Reports\Console\Command;

use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;

class CronCommand extends Command
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var State
     */
    private $state;

    /**
     * CronCommand constructor.
     * @param ObjectManagerInterface $objectManager
     * @param State $state
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        State $state
    ) {
        parent::__construct();

        $this->objectManager = $objectManager;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:reports:cron')
            ->setDescription('Run module cronjobs')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');

        $jobs = [
            \Mirasvit\Reports\Cron\PostcodeUnknown::class,
            \Mirasvit\Reports\Cron\PostcodeUpdate::class,
        ];

        foreach ($jobs as $job) {
            try {
                $output->writeln("<info>Running $job</info>");
                $this->objectManager->get($job)->execute(true);
            } catch (\Exception $e) {
                $output->writeln("<error>{$e->getMessage()}</error>");
            }
        }
    }
}
