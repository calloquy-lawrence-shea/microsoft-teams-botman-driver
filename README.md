### A basic guide to Botman.

[Documentation](https://botman.io/2.0/driver-ms-bot-framework)

### Changes

Interactive answers to questions now always come in an array.

```php
/** @var array $value */
$value = $answer->getValue();
```

### Cards

New power systems are now available that the standard driver does not support.

[Hero Card](https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#hero-card)

```php
$message = (new HeroCardMessage())
    ->setTitle("Hi {$user->getFirstName()} I'm Bot")
    ->setText('Test text')
    ->addButton(TapAction::IM_BACK, 'Test', 'test');
```

[Office365 Connector Card](https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#office-365-connector-card)

```php
$message = (new Office365ConnectorMessage())
    ->setTitle('Title')
    ->setSummary('Text')
    ->addSimpleSection('Section', 'Subtitle', 'Text');
```

[Adaptive Card](https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#adaptive-card)

You can create a form with inputs and buttons.

```php
$message = (new FormMessageBuilder('Title'))
    ->addSeparateInput('first', 'first', 'first', 'second')
    ->addTextInput('email', 'Email', 'Enter your email')
    ->addSubmitButton('Submit')
    ->getMessage();
```

**You can create any message by creating a new builder class.**

[List Card](https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#list-card)

```php
$tapAction = new TapAction(TapAction::IM_BACK, "test {$item->getId()}", $item->getTitle());

$message = (new ListMessageBuilder())
    ->setTitle('List of items')
    ->addItem($item->getTitle(), $item->getDescription(), $item->getImageUrl(), $tapAction)
    ->addPrevButton($item->getPrevPage())
    ->getMessage();
```