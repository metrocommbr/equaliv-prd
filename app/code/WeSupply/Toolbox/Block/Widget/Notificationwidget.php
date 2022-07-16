<?php
namespace  WeSupply\Toolbox\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use WeSupply\Toolbox\Model\Config\Source\NotificationDesignType;
use WeSupply\Toolbox\Block\Notification;

class Notificationwidget extends Notification implements BlockInterface
{
    protected $_template = "widget/notificationwidget.phtml";

    /**
     * @return mixed
     */
    public function getDesign()
    {
        if (!$this->getData('widget_design')){
            $this->setData('widget_design', parent::getDesign());
        }
        return $this->getData('widget_design');
    }

    public function getAlignment()
    {
        if (!$this->getData('widget_alignment')){
            $this->setData('widget_alignment', parent::getAlignment());
        }
        return $this->getData('widget_alignment');
    }

    /**
     * @return string
     */
    public function getWidgetLocationType()
    {
        return NotificationDesignType::SECOND_TYPE_CODE;
    }

}
