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



namespace Mirasvit\Reports\Block\Adminhtml\Geo\Import;

use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\ProductMetadataInterface;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Reports\Config\Source\GeoImportFile
     */
    protected $systemConfigSourceGeoImportFile;

    /**
     * @var \Mirasvit\Reports\Model\Postcode
     */
    protected $postcode;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Form constructor.
     * @param ProductMetadataInterface $productMetadata
     * @param \Mirasvit\Reports\Config\Source\GeoImportFile $systemConfigSourceGeoImportFile
     * @param \Mirasvit\Reports\Model\Postcode $postcode
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        \Mirasvit\Reports\Config\Source\GeoImportFile $systemConfigSourceGeoImportFile,
        \Mirasvit\Reports\Model\Postcode $postcode,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        $this->systemConfigSourceGeoImportFile = $systemConfigSourceGeoImportFile;
        $this->postcode = $postcode;
        $this->formFactory = $formFactory;
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'method'  => 'post',
            'action'  => $this->getUrl('*/*/processImport'),
            'enctype' => 'multipart/form-data',
        ]);

        $general = $form->addFieldset('general', ['legend' => __('Import Information')]);

        $general->addField('import', 'hidden', [
            'name'  => 'import',
            'value' => 1,
        ]);

        $general->addField('files', 'multiselect', [
            'name'     => 'files',
            'label'    => __('Files to import'),
            'required' => true,
            'values'   => $this->systemConfigSourceGeoImportFile->toOptionArray(),
        ]);

        if (strtolower($this->productMetadata->getEdition()) === 'community') {
            $general->addField('unknown', 'label', [
                'name'  => 'files',
                'label' => __('Number of unknown postal codes'),
                'value' => $this->postcode->getNumberOfUnknown(),
                'note'  => __('Every hour, the extension will fetch information for 100 postal codes (cron job)'),
            ]);
        } else {
            $general->addField('unknown', 'label', [
                'name'  => 'files',
                'note'  => __('Every hour, the extension will fetch information for 100 postal codes (cron job)'),
            ]);
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
