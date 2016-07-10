<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

extract($displayData, EXTR_OVERWRITE);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var   string   $customData      Custom data of the element.
 */

if (!is_array($customData)) {
    $customData = array();
}

JText::script('COM_GAMIFICATION_KEY');
JText::script('COM_GAMIFICATION_VALUE');

JHtml::script('com_gamification/form/field/customdata.js', false, true, false, false, true);

$containerId = 'js-container-' . $id;
$buttonId    = 'js-btn-' . $id;

$js = '
if (typeof jQuery.gfyData == "undefined") {
    jQuery.gfyData = {};
}
	
jQuery.gfyData.containerId = "'.$containerId.'";
jQuery.gfyData.buttonId = "'.$buttonId.'";
';

$document = JFactory::getDocument();
$document->addScriptDeclaration($js);
?>
<div id="<?php echo $containerId;?>" class="form-inline gfy-customdata <?php echo $class; ?>">
<button class="btn btn-default" id="<?php echo $buttonId;?>" type="button">
    <i class="icon icon-plus"></i>
    <?php echo JText::_('COM_GAMIFICATION_ADD_DATA'); ?>
</button>

    <?php foreach ($customData as $customDataKey => $customDataValue) {
        $customDataIndex = Prism\Utilities\StringHelper::generateRandomString(5);
    ?>
        <div id="<?php echo $customDataIndex; ?>"><input type="text" name="jform[custom_data][<?php echo $customDataIndex; ?>][key]" placeholder="<?php echo strtolower(JText::_('COM_GAMIFICATION_KEY')); ?>" value="<?php echo $customDataKey; ?>" /><input type="text" name="jform[custom_data][<?php echo $customDataIndex; ?>][value]" placeholder="<?php echo strtolower(JText::_('COM_GAMIFICATION_VALUE')); ?>" value="<?php echo $customDataValue; ?>" class="input-xxlarge"/><button class="btn btn-danger btn-mini js-gfy-cdremovebtn" type="button"><i class="icon icon-remove"></i></button></div>
    <?php } ?>
</div>