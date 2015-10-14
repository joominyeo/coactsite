<?php
/*
 * hold common ui html element functions
 * 
 * note: this class has been extended in uiCommon class  
 */

if (!class_exists('commonCoalcal'))
    require_once "ui/ui.commonCoalcal.class.php";

class htmlElementsBase extends commonCoalcal{
    /*
     * variables
     */

    public function __construct() {
        parent::__construct();
    }

    /*
     * Options select html element
     * 
     * $value : value for option,
     * $selected : set this "selected" to select this option,
     * $text : text to display on option
     */

    public function htmlSelectOptions($value = '', $text = '', $name = '', $id = '', $selected = '', $class = '', $style = '') {
        $elem = '<option';
        //element specfic attributes
        $elem .=$this->htmlAttribute('value', $value);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);
        $elem .=' ' . $selected . '>';
        $elem .= $text;
        $elem .='</option>';
        return $elem;
    }

    /**
     * select html element
     * 
     * $extraAttributes ex: set to 'disable="disable"' if need to disable/read only;
     */

    public function htmlSelect($options = '', $name = '', $id = '', $extraAttributes   = '', $onchange = '', $class = '', $style = '') {
        $elem = '<select';
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);
        //element specfic attributes
        $elem .=$this->htmlAttribute('onchange', $onchange);
        $elem .=' ' . $extraAttributes;
        $elem .='>';

        if (!empty($options))
            $elem .=$options;

        $elem .='</select>';
        return $elem;
    }

    /*
     * button html element
     *        
     */

    public function htmlButton($text = '', $onclick = '', $class = '', $style = '', $name = '', $id = '') {
        $elem = '<button';
        //element specfic attributes
        $elem .=$this->htmlAttribute('type', 'button');
        $elem .=$this->htmlAttribute('onclick', $onclick);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);
        $elem .='>';

        if (!empty($text))
            $elem .=$text;

        $elem .= '</button>';
        return $elem;
    }

    /*
     * input html element
     *         
     */

    public function htmlInput($value = '', $name = '', $id = '', $onclick = '', $class = '', $style = '', $type = 'button') {
        $elem = '<input';
        $elem .=$this->htmlAttribute('type', $type);
        $elem .=$this->htmlAttribute('onclick', $onclick);
        $elem .=$this->htmlAttribute('value', $value);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);
        $elem .='>';

        return $elem;
    }

    /*
     * return html coomon element attributes
     */

    public function htmlCommonAttributes($class = '', $style = '', $name = '', $id = '') {
        $elemAtb = '';
        $elemAtb .=$this->htmlAttribute('id', $id);
        $elemAtb .=$this->htmlAttribute('name', $name);
        $elemAtb .=$this->htmlAttribute('class', $class);
        $elemAtb .=$this->htmlAttribute('style', $style);
        return $elemAtb;
    }

    /*
     * return html attribute markup, if empty attribute value given empty return
     */

    public function htmlAttribute($name, $value = '') {
        $elemAtb = '';
        if (!empty($value))
            $elemAtb .=' ' . $name . '="' . $value . '"';
        return $elemAtb;
    }

    /*
     * a(link) html element
     */

    public function htmlA($href = '', $text = '', $onclick = '', $class = '', $style = '', $name = '', $id = '', $rel = '') {
        $elem = '<a';
        //element specfic attributes
        $elem .=$this->htmlAttribute('href', $href);
        $elem .=$this->htmlAttribute('rel', $rel);
        $elem .=$this->htmlAttribute('onclick', $onclick);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);

        $elem .='>';
        if (!empty($text))
            $elem .=$text;
        $elem .='</a>';
        return $elem;
    }

    /**
     * span html element
     */
    public function htmlSpan($innerHtml = '', $onclick = '', $class = '', $style = '', $name = '', $id = '') {
        $elem = '<span';
        //element specfic attributes
        $elem .=$this->htmlAttribute('onclick', $onclick);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);

        $elem .='>';
        if (!empty($innerHtml))
            $elem .=$innerHtml;
        $elem .='</span>';
        return $elem;
    }

    /**
     * img html element
     */
    public function htmlImg($src, $onclick = '', $class = '', $alt = '', $style = '', $name = '', $id = '') {
        $elem = '<img src="' . $src . '" alt="' . $alt . '" ';
        //element specfic attributes
        $elem .=$this->htmlAttribute('onclick', $onclick);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);
        $elem .='>';
        return $elem;
    }

    /*
     * span html element
     */

    public function htmlDiv($innerHtml = '', $onclick = '', $class = '', $style = '', $name = '', $id = '') {
        $elem = '<div';
        //element specfic attributes
        $elem .=$this->htmlAttribute('onclick', $onclick);
        //get common attributes
        $elem .=$this->htmlCommonAttributes($class, $style, $name, $id);

        $elem .='>';
        if (!empty($innerHtml))
            $elem .=$innerHtml;
        $elem .='</div>';
        return $elem;
    }

}



?>