<?php

namespace Metrocomm\Prescriptor\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Speciality implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('--Please Select--')],
            ['value' => '1', 'label' => 'NUTRICIONISTA - Clínica Geral'],
            ['value' => '2', 'label' => 'NUTRICIONISTA - Clínica funcional/esportiva'],
            ['value' => '3', 'label' => 'NUTRICIONISTA - Fitoterapia'],
            ['value' => '4', 'label' => 'NUTRICIONISTA - Materno infantil'],
            ['value' => '5', 'label' => 'NUTRICIONISTA - Pediatria'],
            ['value' => '6', 'label' => 'NUTRICIONISTA - Hospitalar'],
            ['value' => '7', 'label' => 'NUTRICIONISTA - Saúde Pública'],
            ['value' => '8', 'label' => 'NUTRICIONISTA - Ortomolecular'],
            ['value' => '9', 'label' => 'NUTRICIONISTA - Obesidade e emagrecimento'],
            ['value' => '10', 'label' => 'NUTRICIONISTA - Estética'],
            ['value' => '11', 'label' => 'NUTRICIONISTA - Bariátrica'],
            ['value' => '12', 'label' => 'NUTRICIONISTA - Cursando'],
            ['value' => '13', 'label' => 'NUTRICIONISTA - Outros'],
            ['value' => '14', 'label' => 'MÉDICO - ClÃ¬nica Geral'],
            ['value' => '15', 'label' => 'MÉDICO - Cirurgia Geral'],
            ['value' => '16', 'label' => 'MÉDICO - Gastroenterologia'],
            ['value' => '17', 'label' => 'MÉDICO - Dermatologia'],
            ['value' => '18', 'label' => 'MÉDICO - Endocrinologia'],
            ['value' => '19', 'label' => 'MÉDICO - Geriatria'],
            ['value' => '20', 'label' => 'MÉDICO - Ginecologia'],
            ['value' => '21', 'label' => 'MÉDICO - Nutrologia'],
            ['value' => '22', 'label' => 'MÉDICO - Oncologia'],
            ['value' => '23', 'label' => 'MÉDICO - Ortopedia'],
            ['value' => '24', 'label' => 'MÉDICO - Pediatria'],
            ['value' => '25', 'label' => 'MÉDICO - Outros'],
            ['value' => '26', 'label' => 'FARMACÊUTICO'],
            ['value' => '27', 'label' => 'DENTISTA']
        ];
    }
}
