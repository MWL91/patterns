<?php

interface MessageFactory
{
    public function createName(): NameTemplate;

    public function createBody(): BodyTemplate;

    public function getRenderer(): MessageRenderer;
}

class SmsMessageFactory implements MessageFactory
{
    public function createName(): NameTemplate
    {
        return new SmsName();
    }

    public function createBody(): BodyTemplate
    {
        return new SmsTemplate($this->createName());
    }

    public function getRenderer(): MessageRenderer
    {
        return new SmsRenderer();
    }
}

class EmailMessageFactory implements MessageFactory
{
    public function createName(): NameTemplate
    {
        return new EmailTitle();
    }

    public function createBody(): BodyTemplate
    {
        return new EmailBody($this->createName());
    }

    public function getRenderer(): MessageRenderer
    {
        return new EmailRenderer();
    }
}

interface NameTemplate
{
    public function getTemplateString(): string;
}

class SmsName implements NameTemplate
{
    public function getTemplateString(): string
    {
        return "INFO";
    }
}

class EmailTitle implements NameTemplate
{
    public function getTemplateString(): string
    {
        return "\$title;";
    }
}

interface BodyTemplate
{
    public function getTemplateString(): string;
}

abstract class BaseBodyTemplate implements BodyTemplate
{
    protected $titleTemplate;

    public function __construct(NameTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}

class SmsTemplate extends BaseBodyTemplate
{
    public function getTemplateString(): string
    {
        return "{{ content }}";
    }
}

class EmailBody extends BaseBodyTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return <<<HTML
        <div class="page">
            <h1><?= $renderedTitle ?></h1>
            <article class="content"><?= \$content; ?></article>
        </div>
        HTML;
    }
}

interface MessageRenderer
{
    public function render(string $templateString, array $arguments = []): string;
}

class SmsRenderer implements MessageRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        return str_replace('{{ content }}', $arguments['content'], $templateString);
    }
}

class EmailRenderer implements MessageRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        extract($arguments);

        ob_start();
        eval(' ?>' . $templateString . '<?php ');
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}

class Message
{

    public function __construct(public string $title, public string $content)
    {
    }

    public function render(MessageFactory $factory): string
    {
        $pageTemplate = $factory->createBody();

        $renderer = $factory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), [
            'title' => $this->title,
            'content' => $this->content
        ]);
    }
}

$page = new Message('Customer Reminder', 'Pay for your stuff!');
// This will send email content
echo $page->render(new EmailMessageFactory());
echo "\n\n";

// This will send sms content
echo $page->render(new SmsMessageFactory());
