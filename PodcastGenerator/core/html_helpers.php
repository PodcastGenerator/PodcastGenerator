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

function displayBlockCss($val)
{
    return 'display: ' . ($val == 'yes' ? 'block' : 'none') . ';';
}

function htmlOptionRadios($name, $value, $options)
{
    foreach ($options as $opt) {
        $checked = ($value == $opt['value']) ? ' checked' : '';
        ?>
            <label>
                <input type="radio" name="<?= $name ?>" value="<?= $opt['value'] ?>"<?= $checked ?>>
                <?= $opt['label'] ?>
            </label>
        <?php
    }
}

$yesNoOptions = array(
    [ 'value' => 'yes', 'label' => _('Yes') ],
    [ 'value' => 'no', 'label' => _('No') ]
);
