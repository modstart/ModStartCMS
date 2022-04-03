<?php

/**
 * HTML5 Multimedia and embedded content
 *
 * https://html.spec.whatwg.org/dev/media.html
 * https://html.spec.whatwg.org/dev/embedded-content.html
 */
class HTMLPurifier_HTMLModule_Media extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Media';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $mediaContent = new HTMLPurifier_ChildDef_Media();
        
        // https://html.spec.whatwg.org/dev/media.html#the-video-element
        $this->addElement('video', 'Flow', $mediaContent, 'Common', array(
            'controls' => 'Bool#controls',
            'height'   => 'Length',
            'poster'   => new HTMLPurifier_AttrDef_URI(true),
            'preload'  => 'Enum#auto,metadata,none',
            'src'      => new HTMLPurifier_AttrDef_URI(true),
            'width'    => 'Length',
            'crossorigin' => 'Enum#anonymous',
        ));
        $this->addElementToContentSet('video', 'Inline');

        // https://html.spec.whatwg.org/dev/media.html#the-audio-element
        $this->addElement('audio', 'Flow', $mediaContent, 'Common', array(
            'controls' => 'Bool#controls',
            'preload'  => 'Enum#auto,metadata,none',
            'src'      => new HTMLPurifier_AttrDef_URI(true),
            'crossorigin' => 'Enum#anonymous',
        ));
        $this->addElementToContentSet('audio', 'Inline');

        // https://html.spec.whatwg.org/dev/embedded-content.html#the-source-element
        $this->addElement('source', false, 'Empty', 'Common', array(
            'media'  => 'Text',
            'sizes'  => 'Text',
            'src'    => new HTMLPurifier_AttrDef_URI(true),
            'srcset' => 'Text',
            'type'   => 'Text',
        ));

        // https://html.spec.whatwg.org/dev/media.html#the-track-element
        $this->addElement('track', false, 'Empty', 'Common', array(
            'kind'    => 'Enum#captions,chapters,descriptions,metadata,subtitles',
            'src'     => new HTMLPurifier_AttrDef_URI(true),
            'srclang' => 'Text',
            'label'   => 'Text',
            'default' => 'Bool#default',
        ));

        // https://html.spec.whatwg.org/dev/embedded-content.html#the-picture-element
        $this->addElement('picture', 'Flow', new HTMLPurifier_ChildDef_Picture(), 'Common');
        $this->addElementToContentSet('picture', 'Inline');

        // https://html.spec.whatwg.org/dev/embedded-content.html#the-img-element
        $img = $this->addBlankElement('img');
        $img->attr = array(
            'srcset' => 'Text',
            'sizes'  => 'Text',
            'loading' => 'Enum#lazy,eager',
            'crossorigin' => 'Enum#anonymous',
        );
    }
}
