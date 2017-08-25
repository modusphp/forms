<?php
/**
 * Created by PhpStorm.
 * User: brandon
 * Date: 7/20/17
 * Time: 14:00
 */

use Modus\Forms\FormBase;

class UserForm extends FormBase
{
    protected $fields = [
        'username',
        'password',
        'password_verify',
        'null_field',
    ];

    protected function configureValidations()
    {
        $filter = $this->filter;
        $filter->validate('username')->is('alnum');
        $filter->validate('username')->isNotBlank();
        $filter->validate('password')->is('alnum');
        $filter->validate('password')->isNotBlank();
        $filter->validate('password_verify')->is('equalToField', 'password');

        $filter->useFieldMessage('password_verify', 'The passwords don\'t match!');
    }
}

class NoFieldsForm extends FormBase
{
    protected function configureValidations()
    {
        // TODO: Implement configureValidations() method.
    }
}

class FormBaseTest extends PHPUnit_Framework_TestCase
{
    /** @var  MyForm */
    protected $form;

    protected function setUp()
    {
        $filter_factory = new \Aura\Filter\FilterFactory();

        $filter = $filter_factory->newSubjectFilter();
        $this->filter = $filter;

        $form = new UserForm($filter);

        $data = [
            'username' => 'brandon',
            'password' => 'abc',
            'password_verify' => 'password1',
        ];

        $form->populateData($data);
        $this->form = $form;
    }

    public function testErrorsInFormValidationProduceErrors()
    {
        $this->form->validate();
        $this->assertFalse($this->form->isValid());
    }

    public function testSettingValueSetsValueProperly()
    {
        $this->assertEquals('brandon', $this->form->getValue('username'));
        $this->form->setValue('username', 'debbie');
        $this->assertEquals('debbie', $this->form->getValue('username'));
    }

    public function testEmptyValueReturnsNull()
    {
        $result = $this->form->getValue('notexistant');
        $this->assertNull($result);
    }

    public function testErrorReturnsAString()
    {
        $this->form->validate();
        $error = $this->form->getError('password_verify', 'string');
        $this->assertTrue(is_string($error));
        $this->assertEquals('The passwords don\'t match!', $error);
    }

    public function testErrorReturnsAnArray()
    {
        $this->form->validate();
        $error = $this->form->getError('password_verify', 'array');
        $this->assertEquals(['The passwords don\'t match!'], $error);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidErrorReturnTypeThrowsException()
    {
        $this->form->validate();
        $this->form->getError('password_verify', 'invalid');
    }

    public function testHasErrorReturnsTrueWhenErrorExists()
    {
        $this->form->validate();
        $error = $this->form->hasError('password_verify');
        $this->assertTrue($error);
    }

    public function testHasErrorReturnsFalseWhenErrorDoesNotExist()
    {
        $this->form->validate();
        $error = $this->form->hasError('username');
        $this->assertFalse($error);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFieldsRequiredForForm()
    {
        new NoFieldsForm($this->filter);
    }

    public function testThatGetErrorsReturnsValidResults()
    {
        $this->form->validate();
        $errors = $this->form->getErrors();

        $expectedErrors = [
            'password_verify' => [
                'The passwords don\'t match!',
            ]
        ];

        $this->assertEquals($expectedErrors, $errors);
    }
}
