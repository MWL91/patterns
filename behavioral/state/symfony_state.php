<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

$definitionBuilder = new DefinitionBuilder();
$definition = $definitionBuilder->addPlaces(['draft', 'reviewed', 'rejected', 'published'])
    // Transitions are defined with a unique name, an origin place and a destination place
    ->addTransition(new Transition('to_review', 'draft', 'reviewed'))
    ->addTransition(new Transition('publish', 'reviewed', 'published'))
    ->addTransition(new Transition('reject', 'reviewed', 'rejected'))
    ->build()
;

$singleState = true; // true if the subject can be in only one state at a given time
$property = 'currentState'; // subject property name where the state is stored
$marking = new MethodMarkingStore($singleState, $property);
$workflow = new Workflow($definition, $marking);

class BlogPost
{
    public function __construct(private string $currentState)
    {
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $state): void
    {
        $this->currentState = $state;
    }
}

$blogPost = new BlogPost('draft');

$workflow->can($blogPost, 'publish'); // False
$workflow->can($blogPost, 'to_review'); // True

$workflow->apply($blogPost, 'to_review'); // $blogPost is now in place "reviewed"

$workflow->can($blogPost, 'publish'); // True
$workflow->getEnabledTransitions($blogPost); // $blogPost can perform transition "publish" or "reject"
