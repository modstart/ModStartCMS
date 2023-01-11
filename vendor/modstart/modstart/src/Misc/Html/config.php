<?php

return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'cachePath' => storage_path('cache/purifier'),
    'cacheFileMode' => 0755,
    'settings' => [
        'default' => [
            'HTML.Allowed' => join(',', [
                'b[style],strong[style],i[style],em[style],u[style],a[href|title|style|download],ul[style],ol[style],li[style]',
                'p[style],br,span[style],img[style|width|height|alt|src|data-formula-image]',
                'span[style],h1[style],h2[style],h3[style],h4[style],h5[style],pre[class],code[style]',
                'table[style|cellspacing|width],tbody[style],tbody[style],tr[style],td[style|rowspan|colspan|width|valign],th[style|rowspan|colspan|width|valign]',
                'iframe[src|width|height|frameborder|style]',
                'video[controls|height|poster|preload|src|width|crossorigin]',
                'audio[controls|preload|src|crossorigin]',
                'section[style],blockquote[style]',
                'svg[viewbox|xmlns|xml:space|style|x|y|xmlns:xlink|version|space]',
                'g[transform]',
                'animatetransform[attributename|type|values|calcmode|keytimes|keysplines|dur|repeatcount|begin|restart|fill]',
                'foreignobject[x|y|width|height]',
                'rect[x|y|width|height|style|fill|opacity]',
                'set[attributename|from|to|begin]',
                'animate[attributename|begin|dur|values|fill|from|to|duration]',
                'ellipse[style|cx|cy|rx|ry]',
                'text[x|y|fill|style]',
                'path[style|d]',
            ]),
            'HTML.SafeIframe' => true,
            // https://xxx.com/data/video/xxxx/xx/xx/xxxxxx.mp4
            // /data/xxxx.mp4
            'URI.SafeIframeRegexp' => "%^(http://|https://|//|/)?([a-zA-Z0-9\\./=\\%_\\-\?&]+)$%",
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => false,
            'CSS.AllowImportant' => true,
            'CSS.MaxImgLength' => null,
            'CSS.Proprietary' => true,
            'CSS.Trusted' => true,
            'CSS.AllowTricky' => true,
        ],
        'test' => [
            'Attr.EnableID' => 'true',
        ],
        "youtube" => [
            "HTML.SafeIframe" => 'true',
            "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
        ],
        'custom_definition' => [
            'id' => 'html5-definitions',
            'rev' => 1,
            'debug' => false,
            'elements' => [
                // http://developers.whatwg.org/sections.html
                ['section', 'Block', 'Flow', 'Common'],
                ['nav', 'Block', 'Flow', 'Common'],
                ['article', 'Block', 'Flow', 'Common'],
                ['aside', 'Block', 'Flow', 'Common'],
                ['header', 'Block', 'Flow', 'Common'],
                ['footer', 'Block', 'Flow', 'Common'],

                // Content model actually excludes several tags, not modelled here
                ['address', 'Block', 'Flow', 'Common'],
                ['hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common'],

                // http://developers.whatwg.org/grouping-content.html
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],

                // http://developers.whatwg.org/the-video-element.html#the-video-element
                ['video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src' => 'URI',
                    'type' => 'Text',
                    'width' => 'Length',
                    'height' => 'Length',
                    'poster' => 'URI',
                    'preload' => 'Enum#auto,metadata,none',
                    'controls' => 'Bool',
                ]],
                ['source', 'Block', 'Flow', 'Common', [
                    'src' => 'URI',
                    'type' => 'Text',
                ]],

                // http://developers.whatwg.org/text-level-semantics.html
                ['s', 'Inline', 'Inline', 'Common'],
                ['var', 'Inline', 'Inline', 'Common'],
                ['sub', 'Inline', 'Inline', 'Common'],
                ['sup', 'Inline', 'Inline', 'Common'],
                ['mark', 'Inline', 'Inline', 'Common'],
                ['wbr', 'Inline', 'Empty', 'Core'],

                // http://developers.whatwg.org/edits.html
                ['ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
            ],
            'attributes' => [
                ['iframe', 'allowfullscreen', 'Bool'],
                ['table', 'height', 'Text'],
                ['td', 'border', 'Text'],
                ['th', 'border', 'Text'],
                ['tr', 'width', 'Text'],
                ['tr', 'height', 'Text'],
                ['tr', 'border', 'Text'],
            ],
        ],
        'custom_attributes' => [
            ['a', 'target', 'Enum#_blank,_self,_target,_top'],
        ],
        'custom_elements' => [
            ['u', 'Inline', 'Inline', 'Common'],
        ],
    ],

];
