<?php

abstract class Node
{
    protected string $tagName;
    protected array $attributes = [];
    protected array $childElements = [];
    protected string $content = '';

    public function addChild($node): self
    {
        $this->childElements[] = $node;
        return $this;
    }


    public function addContent(string $content): Node
    {
        $this->content = $content;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function renderChild(): string
    {
        $childRendered = '';

        foreach ($this->childElements as $childElement){
            $childRendered .= $childElement->render();
        }
        return $childRendered;
    }

    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    protected function renderAttributes(): string
    {
        $attributes = '';
        foreach ($this->attributes as $name => $value) {
            $attributes .= " $name='$value'";
        }
        return $attributes;
    }
}


class Div extends Node
{
    public function render(): string
    {
        return "<div{$this->renderAttributes()}>{$this->content}{$this->renderChild()}</div>";
    }
}


class Span extends Node
{
    public function render(): string
    {
        return "<span{$this->renderAttributes()}>{$this->content}{$this->renderChild()}</span>";
    }
}


class Input extends Node
{
    public function setType(string $type): self
    {
        $this->setAttribute('type', $type);
        return $this;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->setAttribute('placeholder', $placeholder);
        return $this;
    }

    public function render(): string
    {

        $attributes = $this->renderAttributes();
        return "<input{$attributes}>";
    }
}


class ListNode extends Node
{
    public function __construct(string $listType = 'ul')
    {
        if ($listType === 'ul') {
            $this->tagName = "ul";
        } elseif ($listType ==='ol') {
            $this->tagName = "ol";
        } else {
            $this->tagName = "ul";
        }
    }

    public function addListItem(string $content): self
    {
        $this->addChild((new ListItem())->addContent($content));
        return $this;
    }

    public function render(): string
    {
        $list = "<{$this->tagName}{$this->renderAttributes()}>";
        $list .= $this->renderChild();
        $list .= "</{$this->tagName}>";
        return $list;
    }
}


class ListItem extends Node
{
    public function render(): string
    {
        return "<li{$this->renderAttributes()}>{$this->content}{$this->renderChild()}</li>";
    }
}


$root = new Div();
$root->setId('section')
    ->setAttribute('class', 'content dark')
    ->addChild((new Span())->addContent('test'));

$input = (new Input())->setType('text')
    ->setAttribute('class', 'form-control is-invalid')
    ->setId('name-input')
    ->setPlaceholder('Name');
$errorFeedback = (new Div())->setAttribute('class', 'valid-feedback')->addContent('Name is required');
$input->addChild($errorFeedback);

$root->addChild($input);

$list = (new ListNode('ol'))->addListItem('Item 1')->addListItem('Item 2');
$root->addChild($list);

$list1 = (new ListNode('ul'))->addListItem('Item 1')->addListItem('Item 2');
$root->addChild($list1);


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<?php echo $root->render(); ?>

</body>
</html>