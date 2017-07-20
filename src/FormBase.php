<?php

namespace Modus\Forms;

use Aura\Filter\SubjectFilter;

abstract class FormBase
{
    /**
     * @var SubjectFilter
     */
    protected $filter;

    /**
     * The fields that are part of the form. Each form should define its own fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $suppliedData = [];

    protected $errorClass = 'has-error';

    /**
     * @param SubjectFilter $filter
     */
    public function __construct(SubjectFilter $filter)
    {
        if(empty($this->fields)) {
            throw new \InvalidArgumentException('A list of fields is required for the form!');
        }

        $this->filter = $filter;
        $this->configureValidations();
    }

    /**
     * Configure and define the validations we need in order to know if the form is valid.
     *
     * @return mixed
     */
    abstract protected function configureValidations();

    /**
     * Takes the POST data and populates the form with the data.
     *
     * @param array $data
     */
    public function populateData(array $data = [])
    {
        foreach ($this->fields as $field) {
            if (isset($data[$field])) {
                $this->suppliedData[$field] = $data[$field];
            } else {
                $this->suppliedData[$field] = null;
            }
        }

    }

    /**
     * Validates the form according to the rules specified in the form field validator.
     *
     * @return bool
     */
    public function validate()
    {
        return $this->filter->apply($this->suppliedData);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->validate();
    }

    /**
     * @param $field
     * @return string|array
     */
    public function getError($field, $type = 'string')
    {
        switch ($type) {

            case 'string':
                return $this->filter->getFailures()->getMessagesForFieldAsString($field);
                break;

            case 'array':
                return $this->filter->getFailures()->getMessagesForField($field);
                break;

            case 'object':
                return (object)$this->filter->getFailures()->getMessagesForField($field);
                break;

            default:
                throw new \InvalidArgumentException('Invalid type of "' . $type . '" was provided!');
        }

    }

    /**
     * @param $field
     * @return bool
     */
    public function hasError($field)
    {
        return (bool)$this->filter->getFailures()->forField($field);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->filter->getFailures()->getMessages();
    }

    /**
     * Return a predefined error class that can be used in displaying the error form.
     *
     * @param $field
     * @return string
     */
    public function getErrorClass($field)
    {
        if ($this->hasError($field)) {
            return $this->errorClass;
        }

        return null;
    }

    /**
     * @param $field
     * @return null
     */
    public function getValue($field)
    {
        if(isset($this->suppliedData[$field])) {
            return $this->suppliedData[$field];
        }

        return null;
    }

    /**
     * We don't want to dump all the validators in an error case.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'fields' => $this->fields,
            'suppliedData' => $this->suppliedData,
        ];
    }

    /**
     * Set the value of a particular field.
     *
     * @param $field
     * @param $value
     */
    public function setValue($field, $value)
    {
        if (in_array($field, $this->fields)) {
            $this->suppliedData[$field] = $value;
        }
    }
}