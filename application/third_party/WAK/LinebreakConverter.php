<?php


class LinebreakConverter extends BaseConverter implements ConverterInterface
{
    public function toJson(\DOMElement $node)
    {
        return array(
            'type' => 'linebreaks',
            'data' => array(
                'linebreak' => 'br'
            )
        );
    }

    public function toHtml(array $data)
    {
	   return '<br />';
    }
}
