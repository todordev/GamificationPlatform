<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

extract($displayData, EXTR_OVERWRITE);

/**
 * Layout variables
 * -----------------
 * @var   Gamification\User\Progress\Progress $progress      Progress object.
 * @var   string   $tooltip         Is tooltip has to be displayed?
 * @var   string   $name            The name of the user.
 * @var   boolean  $tooltipTitleNext        Is this field disabled?
 * @var   string   $tooltipTitleCurrent           Group the field belongs to. <fields> section in form XML.
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

$classes    = array();
$html       = array();

/** @var Gamification\User\Progress\Progress $progress */
$userPoints   = $progress->getPoints()->getPointsNumber();

// Prepare current level
if ($tooltip) {
    JHtml::_('bootstrap.tooltip');
    $classes[]    = 'hasTooltip';

    if ($tooltipTitleCurrent !== '') {
        $tooltipTitleCurrent = ' title="' . $tooltipTitleCurrent . '"';
    }
}
?>


<div class="gfy-progress-labels">

<div class="gfy-prgss-lbl-current">
<?php echo htmlspecialchars($progress->getCurrentUnit()->getTitle(), ENT_QUOTES, 'UTF-8');?>
</div>

<?php
// Prepare next level
if ($progress->hasNext()) {
    $nextUnit       = $progress->getNextUnit();
    $nextUnitTitle  = htmlspecialchars($progress->getNextUnit()->getTitle(), ENT_QUOTES, 'UTF-8');

    if ($tooltip and $tooltipTitleNext !== '') {
        $tooltipTitleNext = ' title="'.$tooltipTitleNext.'"';
    }
?>

<div class="gfy-prgss-lbl-next">
<?php echo $nextUnitTitle; ?>
</div>
<?php } ?>

</div>

<div class="clearfix"></div>
<div class="progress">
    <div class="progress-bar progress-bar-success <?php echo implode(' ', $classes); ?>" <?php echo $tooltipTitleCurrent; ?> style="width: <?php echo $progress->getPercentageCurrent(); ?>%;" role="progressbar" aria-valuenow="<?php echo $progress->getPercentageCurrent(); ?>" aria-valuemin="0" aria-valuemax="100" ></div>

    <?php if ($progress->hasNext()) { ?>
    <div class="progress-bar progress-bar-danger <?php echo implode(' ', $classes); ?>" <?php echo $tooltipTitleNext; ?> style="width: <?php echo $progress->getPercentageNext(); ?>%;" role="progressbar" aria-valuenow="<?php echo $progress->getPercentageNext(); ?>" aria-valuemin="0" aria-valuemax="100"></div>
    <?php } ?>
</div>
