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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeoImportCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:reports:geo-import')
            ->setDescription('Import Postal Codes')
            ->setDefinition([]);

        parent::configure();
    }


    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('frontend');

        /** @var \Mirasvit\Reports\Config\Source\GeoImportFile $source */
        $source = $this->objectManager->create(\Mirasvit\Reports\Config\Source\GeoImportFile::class);

        /** @var \Mirasvit\Reports\Model\PostcodeFactory $postcodeFactory */
        $postcodeFactory = $this->objectManager->create(\Mirasvit\Reports\Model\PostcodeFactory::class);

        foreach ($source->toOptionArray() as $item) {
            $output->write('<info>' . $item['label'] . '...</info>');

            $postcodeFactory->create()->importFile($item['value']);

            $output->writeln('<info>done</info>');
        }
    }
}
