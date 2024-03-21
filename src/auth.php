<?php

class Node
{
    protected string $tagName;
    protected array $attributes = [];
    protected array $children = [];
    protected string $content;
    public function addContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setTagName(string $tagName): self
    {
        $this->tagName = $tagName;
        return $this;
    }
    public function setAttribute(string $attribute, string $value): self
    {
        $this->attributes[$attribute] = $value;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function setClass(string $class): self
    {
        if (!isset($this->attributes['class'])) {
            $this->attributes['class'] = '';
        }
        $this->attributes['class'] .= "$class";
        return $this;
    }

    public function addChild(Node $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    public function render(): string
    {
        $html = "<{$this->tagName}";
        foreach ($this->attributes as $name => $value) {
            $html .= " $name = '$value'";
        }
        $html .= ">";
        foreach ($this->children as $child) {
            $html .= $child->render();
        }
        $html .= "</{$this->tagName}>";
        return $html;
    }



}

class BlockNode extends Node
{
    public function __construct()
    {
        $this->tagName = "div";
    }
}

class InlineNode extends Node
{


    public function __construct(string $content = '')
    {

        $this->tagName = "span";
        $this->content = $content;
    }
}

class ListNode extends Node
{
    public function __construct(string $listType = 'ul')
    {
        if ($listType == 'ul') {
            $this->tagName = "ul";
        } elseif ($listType == 'ol') {
            $this->tagName = "ol";
        } else {
            $this->tagName = "ul";
        }
    }

    public function addListItem(string $content): self
    {
        $this->addChild((new Node())->setTagName('li')->addContent($content));
        return $this;
    }
}

class InputNode extends Node
{
    public function __construct()
    {
        $this->tagName = "div";
        $this->setAttribute('class', 'form-group');
    }

    public function setInput(string $type, string $id, string $placeholder): self
    {
        $input = (new Node())->setTagName('input')
            ->setAttribute('type', $type)
            ->setId($id)
            ->setAttribute('placeholder', $placeholder);
        $this->addChild($input);
        return $this;
    }

    public function setLabel(string $labelText, string $for): self
    {
        $label = (new Node())->setTagName('label')
            ->addContent($labelText)
            ->setAttribute('for', $for)
            ->setAttribute('class', 'form-label');
        $this->addChild($label);
        return $this;
    }

    public function setErrorMessage(string $message): self
    {
        $error = (new Node())->setTagName('div')
            ->addContent($message)
            ->setAttribute('class', 'error-message');
        $this->addChild($error);
        return $this;
    }
}

$inputNode = (new InputNode())
    ->setInput('text', 'input-field', 'Enter your name')
    ->setLabel('Name', 'input-field')
    ->setErrorMessage('Name is required');

echo $inputNode->render();

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
<div id="section" class="content dark">
    <?php echo $inputNode->render(); ?>
</div>
</body>
</html>