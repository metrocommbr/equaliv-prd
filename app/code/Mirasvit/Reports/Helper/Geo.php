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



namespace Mirasvit\Reports\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

//@todo
class Geo extends AbstractHelper
{
    const LIMIT = 10;

    /**
     * @var int
     */
    protected $tries = 0;

    /**
     * @param array $locations
     * @return array
     */
    public function findInMapQuestApi($locations)
    {
        $result = [];

        $get = [];
        foreach ($locations as $id => $location) {
            $result[$id] = [];
            $location = str_replace(' ', '%20', (string)$location);
            $get[] = 'location=' . $location;
        }

        $get = implode('&', $get);
        $url = 'http://www.mapquestapi.com/geocoding/v1/batch?key=Kmjtd|luua2qu7n9,7a=o5-lzbgq&'
            . $get . '&outFormat=json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.mapquestapi.com/geocoding/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);

        $keys = array_keys($locations);

        if (isset($response['results'])) {
            foreach ($response['results'] as $idx => $locations) {
                $id = $keys[$idx];

                foreach ($locations['locations'] as $location) {
                    if ($location['postalCode']) {
                        $result[$id][] = $location;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $locations
     * @return array
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function findInGoogle($locations)
    {
        sleep(1);

        $result = [];
        foreach ($locations as $id => $location) {
            $result[$id] = [];

            $location = explode(':', (string)$location);
            $country = trim($location[0]);
            $code = trim($location[1]);

            do {
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?language=' . $country . '&components=country:'
                . $country . '|postal_code:' . $code;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_REFERER, 'http://www.mapquestapi.com/geocoding/');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $content = json_decode(curl_exec($ch), true);

                if (isset($content['status'])) {
                    switch ($content['status']) {
                        case "ZERO_RESULTS":
                            break 2;
                        case "OVER_QUERY_LIMIT":
                        case "REQUEST_DENIED":
                            if ($this->tries++ > self::LIMIT) {
                                $this->tries = 0;
                                unset($result[$id]);
                                break 2;
                            }
                            break;
                    }
                }
            } while (isset($content['status']) && "OK" !== $content['status']);

            foreach ($content['results'] as $item) {
                $result[$id][] = $item;
            }
        }

        return $result;
    }

    /**
     * @param \Mirasvit\Reports\Model\Postcode $postcode
     *
     * @return void
     */
    public function synchronize($postcode)
    {
        $data = [
            'postcode'  => $postcode->getPostcode()."",
            'country'   => $postcode->getCountryId()."",
            'state'     => $postcode->getState()."",
            'province'  => $postcode->getProvince()."",
            'place'     => $postcode->getPlace()."",
            'community' => $postcode->getCommunity()."",
            'lat'       => $postcode->getLat()."",
            'lng'       => $postcode->getLng()."",
        ];
        $query = http_build_query($data);
        $url = 'http://mirasvit.com/media/geo/?'.$query;
        get_headers($url);
    }

    /**
     * @param string $code
     * @return string
     */
    public function formatPostcode($code)
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper((string)$code));
    }

    /**
     * @param string $name
     * @return string
     */
    public function formatName($name)
    {
        $name = (string)$name;

        if (strlen($name) <= 3) {
            return $name;
        }

        $name = $this->ucname($name);

        return $name;
    }

    /**
     * @param string $string
     * @return string
     */
    public function ucname($string)
    {
        if (strpos(mb_strtolower($string), '?') === false) {
            $string = mb_strtolower($string, 'UTF-8');
        }

        $string = ucwords($string);

        foreach (['-', '\''] as $delimiter) {
            if (strpos($string, $delimiter) !== false) {
                $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
            }
        }

        return $string;
    }
}
