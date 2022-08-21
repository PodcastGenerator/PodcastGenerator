<?php

############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

function checkedAttr($val, $state)
{
    if ($val == $state) {
        return 'checked';
    }
    return '';
}

function selectedAttr($val, $state)
{
    return $val == $state ? 'selected' : '';
}

function displayBlockCss($val)
{
    return 'display: ' . ($val == 'yes' ? 'block' : 'none') . ';';
}

function htmlOptionRadios($name, $value, $options)
{
    foreach ($options as $opt) {
        $id = $name . '_' . $opt['value'];
        $checked = ($value == $opt['value']) ? ' checked' : '';
        ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="<?= $name ?>" id="<?= $id ?>" value="<?= $opt['value'] ?>"<?= $checked ?>>
                <label class="form-check-label" for="<?= $id ?>"><?= $opt['label'] ?></label>
            </div>
        <?php
    }
}

function htmlOptionSelect($name, $value, $options, $selectCssClass = null)
{
    ?>
        <select name="<?= $name ?>" id="<?= $name ?>" class="<?= $selectCssClass ?>">
    <?php
    foreach ($options as $opt) {
        $selected = ($value == $opt['value']) ? ' selected' : '';
        ?>
            <option value="<?= $opt['value'] ?>"<?= $selected ?>><?= $opt['label'] ?></option>
        <?php
    }
    ?></select><?php
}

$yesNoOptions = array(
    [ 'value' => 'yes', 'label' => _('Yes') ],
    [ 'value' => 'no', 'label' => _('No') ]
);
