<?php
/**
 * @package     HikaShop for Joomla!
 * @version     3.0.0
 * @author      hikashop.com
 * @copyright   (C) 2010-2017 HIKARI SOFTWARE. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][merchantID]"><?php
                        echo JText::_('merchantID');
                ?></label>
        </td>
        <td>
                <input type="text" name="data[payment][payment_params][merchantID]" value="<?php echo $this->escape(@$this->element->payment_params->merchantID); ?>" />
        </td>
</tr>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][verifyKey]"><?php
                        echo JText::_('verifyKey');
                ?></label>
        </td>
        <td>
                <input type="text" name="data[payment][payment_params][verifyKey]" value="<?php echo $this->escape(@$this->element->payment_params->verifyKey); ?>" />
        </td>
</tr>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][privateKey]"><?php
                        echo JText::_('privateKey');
                ?></label>
        </td>
        <td>
                <input type="text" name="data[payment][payment_params][privateKey]" value="<?php echo $this->escape(@$this->element->payment_params->privateKey); ?>" />
        </td>
</tr>
