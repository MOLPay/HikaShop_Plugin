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
                <label for="data[payment][payment_params][accountType]"><?php
                        echo JText::_('Account Type');
                ?></label>
        </td>
        <td>
                <select name="data[payment][payment_params][accountType]">
                        <option value="sandbox" 
                                <?php if($this->escape(@$this->element->payment_params->accountType) == "sandbox"){ echo "selected"; } ?>
                        >Sandbox</option>
                        <option value="production"
                                <?php if($this->escape(@$this->element->payment_params->accountType) == "production"){ echo "selected"; } ?>
                        >Production</option>
                </select>
        </td>
</tr>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][merchantID]"><?php
                        echo JText::_('Merchant ID');
                ?></label>
        </td>
        <td>
                <input type="text" name="data[payment][payment_params][merchantID]" value="<?php echo $this->escape(@$this->element->payment_params->merchantID); ?>" />
        </td>
</tr>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][mpschannel]"><?php
                        echo JText::_('Channel');
                ?></label>
        </td>
        <td>
                <?php
                $molpay = new molpay();
                $channelList = array();
                $listChannel = "";

                foreach ($this->element->payment_params as $key => $value)
                {
                        if($key == "mpschannel")
                        {
                                $channelList[] = $value;
                        }
                }

                foreach ($molpay->retChannel() as $k1 => $v1)
                {
                        $listChannel .= "<input type=checkbox name='data[payment][payment_params][mpschannel][]' value='" . $k1 . "'";

                        foreach($channelList as $k2 => $v2)
                        {
                                foreach ($v2 as $k3 => $v3)
                                {
                                        if($v3 == $k1)
                                                $listChannel .= " checked";
                                }
                        }
                        $listChannel .= ">&nbsp;" . $v1 . "<br/>";
                }
                echo $listChannel;
                ?>
        </td>
</tr>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][verifyKey]"><?php
                        echo JText::_('Verify Key');
                ?></label>
        </td>
        <td>
                <input type="text" name="data[payment][payment_params][verifyKey]" value="<?php echo $this->escape(@$this->element->payment_params->verifyKey); ?>" />
        </td>
</tr>
<tr>
        <td class="key">
                <label for="data[payment][payment_params][secretKey]"><?php
                        echo JText::_('Secret Key');
                ?></label>
        </td>
        <td>
                <input type="text" name="data[payment][payment_params][secretKey]" value="<?php echo $this->escape(@$this->element->payment_params->secretKey); ?>" />
        </td>
</tr>
