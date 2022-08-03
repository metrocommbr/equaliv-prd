<?php

namespace Metrocomm\Prescriptor\Block;

use Magento\Customer\Model\Session;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Metrocomm\Prescriptor\Model\Config\Speciality;

class Register extends Template
{
    /**
     * @var Country
     */
    private $country;
    /**
     * @var Speciality
     */
    private $speciality;
    /**
     * @var Session
     */
    private $session;

    public function __construct(
        Session $session,
        Speciality $speciality,
        Country $country,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->country = $country;
        $this->speciality = $speciality;
        $this->session = $session;
    }

    public function getAllCountries()
    {
        return $this->country->toOptionArray();
    }

    public function getAllSpecialities()
    {
        return $this->speciality->toOptionArray();
    }

    public function getFormData()
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $formData = $this->session->getCustomerFormData(true);
            $data = new DataObject();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

}
