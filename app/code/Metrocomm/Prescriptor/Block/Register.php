<?php

namespace Metrocomm\Prescriptor\Block;

use Magento\Customer\Model\Session;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Directory\Model\CountryFactory;
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
    /**
     * @var CountryFactory
     */
    private $countryFactory;

    public function __construct(
        CountryFactory $countryFactory,
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
        $this->countryFactory = $countryFactory;
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

    public function getRegionsOfCountry($countryCode) {
        $regionCollection = $this->countryFactory->create()->loadByCode($countryCode)->getRegions();
        return $regionCollection->loadData()->toOptionArray();
    }

}
