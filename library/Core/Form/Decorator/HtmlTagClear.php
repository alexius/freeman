<?php

class Core_Form_Decorator_HtmlTagClear extends Zend_Form_Decorator_HtmlTag
{
 /**
     * Render content wrapped in an HTML tag
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $tag       = $this->getTag();
        $placement = $this->getPlacement();
        $noAttribs = $this->getOption('noAttribs');
        $openOnly  = $this->getOption('openOnly');
        $closeOnly = $this->getOption('closeOnly');
        $this->removeOption('noAttribs');
        $this->removeOption('openOnly');
        $this->removeOption('closeOnly');

        $attribs = null;
        if (!$noAttribs) {
            $attribs = $this->getOptions();
        }
      
        switch ($placement) {
            case self::APPEND:
                if ($closeOnly) {
                    $elem = $content . $this->_getCloseTag($tag);
                }
                if ($openOnly) {
                    $elem =  $content . $this->_getOpenTag($tag, $attribs);
                }
                $elem = $content
                     . $this->_getOpenTag($tag, $attribs)
                     . $this->_getCloseTag($tag);
            case self::PREPEND:
                if ($closeOnly) {
                    $elem =  $this->_getCloseTag($tag) . $content;
                }
                if ($openOnly) {
                    $elem =  $this->_getOpenTag($tag, $attribs) . $content;
                }
                $elem =  $this->_getOpenTag($tag, $attribs)
                     . $this->_getCloseTag($tag)
                     . $content;
            default:
                $elem =  (($openOnly || !$closeOnly) ? $this->_getOpenTag($tag, $attribs) : '')
                     . $content
                     . (($closeOnly || !$openOnly) ? $this->_getCloseTag($tag) : '');
        }
        return $elem . '<div class="clear"></div>';
    }
}