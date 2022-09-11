<?php declare(strict_types=1);

############################################################
# PODCAST GENERATOR
#
# Created by the Podcast Generator Development Team
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################

namespace PodcastGenerator\Models\Admin;

use Exception;

/**
 * Provides basic functionality for form models.
 */
abstract class FormModelBase
{
    protected array $validationMessages = [];

    /**
     * Adds a new validation error message to the model.
     *
     * @param string $field  The name of the form field which failed validation.
     * @param string $error  A message describing the validation error.
     * @return void
     */
    protected function addValidationError(string $field, string $error)
    {
        if (!isset($this->validationMessages[$field])) {
            $this->validationMessages[$field] = array();
        }
        $this->validationMessages[$field][] = $error;
    }

    /**
     * Adds a new validation error message to the model, specifically to call
     * out that a required form field is missing a value.
     *
     * @param string $field      The name of the form field without a value.
     * @param string $fieldName  The display name of the form field.
     * @return void
     */
    protected function addMissingValueValidationError(string $field, string $fieldName)
    {
        $this->addValidationError($field, sprintf(_('%s field is missing.'), $fieldName));
    }

    /**
     * Adds a new validation error message to the model, specifically to call
     * out that a form field has an invalid value.
     *
     * @param string $field      The name of the form field with a bad value.
     * @param string $fieldName  The display name of the form field.
     * @return void
     */
    protected function addBadValueValidationError(string $field, string $fieldName)
    {
        $this->addValidationError($field, sprintf(_('Invalid %s value provided.'), $fieldName));
    }

    /**
     * Checks that all form fields in the model have valid values.
     *
     * @return boolean  `true` if the form is valid; otherwise, `false`.
     */
    abstract public function validate(): bool;

    /**
     * Returns the result of a previous validation of the form model.
     *
     * @return boolean  `true` if the form is valid; otherwise, `false`.
     */
    public function isValid(): bool
    {
        foreach ($this->validationMessages as $field => $errors) {
            if (!empty($errors)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Gets the validation error messages for the provided form field.
     *
     * @param string $field  The name of the field.
     * @return string|null   A string containing the validation error messages
     *                       if any; if the field is valid, this instead returns
     *                       `null`.
     */
    public function validationFor(string $field): ?string
    {
        if (!isset($this->validationMessages[$field]) || empty($this->validationMessages[$field])) {
            return null;
        }

        return implode(' ', $this->validationMessages[$field]);
    }

    /**
     * Gets any validation-related CSS classes which should be applied to the
     * provided form field element.
     *
     * @param string $field  The name of the field.
     * @return string|null   The CSS classes which should be added to the input,
     *                       select, or textarea element for the field.
     */
    public function cssClassFor(string $field): ?string
    {
        if (!isset($this->validationMessages[$field]) || empty($this->validationMessages[$field])) {
            return null;
        }

        return 'is-invalid';
    }

    /**
     * Applies the form fields to the provided model object.
     *
     * @param array|object $object  The model object represented by the form.
     * @return void
     */
    final public function applyChanges(&$object)
    {
        if (!$this->isValid()) {
            throw new Exception('Cannot apply invalid data');
        }

        return $this->apply($object);
    }

    /**
     * Implements the actual application of the form model to the data model.
     *
     * @param array|object $object
     * @return void
     */
    abstract protected function apply(&$object);
}
