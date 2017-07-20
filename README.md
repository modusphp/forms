# Forms

Forms is a dead simple library for validating incoming data, whether that data be from the $_POST superglobal or from the CLI, or from any other source.

## Setup

In order to use Forms, you need to extend the `FormsBase` class, and implement two values: `$fields` and `configureValidations()`. 

The `$fields` property contains an array of fields you'll be passing into the form, for the purposes of separating the valid content from content you don't care about. It should be an array of valid strings:

```php
$fields = [
  'username',
  'password',
  'password_verify',
];
```

Once you have defined the `$fields` property, you can build your validations in the `configureValidations()` method.

```php
protected function configureValidations()
{
  $filter = $this->filter;
  $filter->addSoftRule('username', $filter::IS, 'alnum');
  $filter->addSoftRule('username', $filter::IS_NOT, 'blank');
  $filter->addSoftRule('password', $filter::IS_NOT, 'blank');
  $filter->addSoftRule('password_verify', $filter::IS, 'equalToField', 'password');
}
```
## Using Forms

Once you have completed basic setup, now you're ready to use Forms.

To populate the form, call the `populateData` method with an array of data to be populated and filtered.

To filter the data, call `validate()`. 

The `getError`, `hasErrors` and `getErrors` functions all provide access to various errors, if `validate()` should return false. You can use `isValid()` to determine if there are errors to be retrieved.
