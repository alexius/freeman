/**
 * @description jQuery transliterate plugin
 * @license MIT <http://opensource.org/licenses/mit-license.php>
 * @author Dino Ivankov <dinoivankov@gmail.com>
 * @version 1.1
 * http://code.google.com/p/jquery-transliteration-plugin/
 */;
(function ($) 
{
    $.fn.transliterate = function (opts) 
    {
        var options = $.extend({}, $.fn.transliterate.defaults, opts);
        return this.each(function () 
        {
            var count = this.childNodes.length;
            if (isEligible(this))
            {
                if (count && $(this).attr('tagName').toLowerCase() != 'textarea')
                {
                    // textarea exception - although textarea has innerHTML we should rather set its value, so if it's only element passed to the constructor, it will be treated as empty and val() will be transliterated'
                    while (count--) 
                    {
                        var node = this.childNodes[count];
                        if (node.nodeType === 1) {
                            $(node).transliterate(options);
                        }
                        else if (node.data)
                        {
                            if (!$(node).data('tempReplacements')) {
                                $(node).data('tempReplacements', []);
                            }
                            node.data = transliterateText(node.data);
                        };
                    };
                }
                else 
                {
                     if (options.translitareteTo == false)
                    {
                        if (options.transliterateFormValues && $(this).val() && ($(this).attr('tagName').toLowerCase() == 'input' || $(this).attr('tagName').toLowerCase() == 'textarea')){
                            $(this).val(transliterateText($(this).val()));
                        } else if ($(this).html() != "") {
                            $(this).html(transliterateText($(this).html()));
                        };
                    }
                    else if  (options.translitareteTo == true)  
                    {        
                        if (options.transliterateFormValues && $
                        (this).val() && ($(this).attr('tagName').toLowerCase() == 'input' || $(this).attr('tagName').toLowerCase() == 'textarea')){
                            var trans = transliterateText($(this).val());
                            
                            $(options.translitareteToField).val(trans);
                        } else if ($(this).html() != "") {
                            $(options.translitareteToField).html(transliterateText($(this).html()).replace(/^\s*|\s*$/g,''));
                        };  
                    }
                };
            };
            /**
             * Takes element or tagName string as the param, checks against the
             * exclude lists and returns false if element is not to be transliterated.
             * @param mixed el
             * @return boolean
             * @todo add classname, id
             */
            function isEligible(el)
            {
                var result = true;
                var tag;
                if (typeof el != "string") {
                    tag = $(el).attr('tagName').toLowerCase();
                }
                else {
                    tag = el.toLowerCase();
                }
                for (var i = 0; i < options.excludes.length; i++) {
                    if (options.excludes[i] == tag) {
                        result = false;
                    }
                }
                return result;
            };
            /**
             * Takes text string as parameter and transliterates it based on set options
             * @param String text
             * @return String transliterated text
             */
            function transliterateText(text)
            {
                var _text = new String(text);
                if (_text)
                {
                    /*
                    * preprocessing - performing all multi-char replacements
                    * before 1:1 transliteration based on options
                    */
                    _text = multiReplace(_text, options.maps[options.direction].multiPre);
                    /*
                    * 1:1 transliteration - transliterating the text using
                    * character maps supplied in options
                    */
                    _text = charTransliteration(_text);
                    /*
                    * postrocessing - performing all multi-char replacements after
                    * 1:1 transliteration based on options
                    */
                    _text = multiReplace(_text, options.maps[options.direction].multiPost);
                };
                return _text;
            };
            /**
             * Transliterates char to char using charmap
             * @param String text
             * @return String
             */
            function charTransliteration(text)
            {
                var _text = new String(text);
                if (_text)
                {
                    var fromChars = options.maps[options.direction].charMap[0].split('');
                    var toChars = options.maps[options.direction].charMap[1].split('');
                    var charMap = {};
                    for (var i = 0; i < fromChars.length; i++) {
                        var c = i < toChars.length ? toChars[i] : fromChars[i];
                        charMap[fromChars[i]] = c;
                    };
                    var re = new RegExp(fromChars.join("|"), "g");
                    _text = _text.replace(re, function (c) 
                    {
                        if (charMap[c]) {
                            return charMap[c];
                        }
                        else {
                            return c;
                        };
                    });
                };
                return _text;
            };
            /**
             * multiReplace - replaces all occurrences of all present elements of
             * multiMap[0] with multiMap[1] in a string and returns the string
             * @param String text
             * @param Array[][] multiMap
             */
            function multiReplace(text, multiMap)
            {
                if (multiMap[0])
                {
                    var len = multiMap[0].length;
                    for (var i = 0; i < len; i++)
                    {
                        var tempReplacements = $(node).data('tempReplacements');
                        var pattern = multiMap[0][i];
                        var regex = new RegExp(pattern);
                        var replacement = multiMap[1][i];
                        if (replacement.match(regex))
                        {
                            var _tempReplacement = (new Date).getTime();
                            while (_tempReplacement == (new Date).getTime()) {
                                _tempReplacement = _tempReplacement;
                            };
                            var _tempReplacements = tempReplacements;
                            tempReplacements = [];
                            for (var k = 0; k < _tempReplacements.length; k++)
                            {
                                if (_tempReplacements[k][0] == multiMap[0][i]) {
                                    continue 
                                }
                                else {
                                    tempReplacements.push(_tempReplacements[k]);
                                };
                            };
                            tempReplacements.push([multiMap[0][i], _tempReplacement]);
                            $(node).data('tempReplacements', tempReplacements);
                            while (regex.test(text)) {
                                text = text.replace(regex, _tempReplacement);
                            };
                        }
                        else if (pattern.match(new RegExp(replacement)))
                        {
                            for (var j = 0; j < tempReplacements.length; j++)
                            {
                                var tempRegex = new RegExp(tempReplacements[j][1]);
                                while (text.match(tempRegex)) {
                                    text = text.replace(tempRegex, tempReplacements[j][0]);
                                };
                            };
                        };
                        while (regex.test(text)) {
                            text = text.replace(regex, replacement);
                        };
                    };
                };
                return text;
            };
        });
    };
    /**
     * default option set for transliterate plugin
     */
    $.fn.transliterate.defaults = 
    {
        direction : 'c2l',
        translitareteTo: false,
        translitareteToField: null,
        transliterateFormValues : true, excludes : ['html', 'head', 'style', 'title', 
        'link', 'meta', 'script', 'object', 'iframe', 'canvas'], maps : 
        {
            l2c : 
            {
                charMap : ['abcdefghijklmnoprstuvzšđžčćABCDEFGHIJKLMNOPRSTUVZŠĐŽČĆ', 'абцдефгхијклмнопрстувзшђжчћАБЦДЕФГХИЈКЛМНОПРСТУВЗШЂЖЧЋ'], 
                multiPre : [[], []], multiPost : [['&\u043d\u0431\u0441\u043f;', '&\u0430\u043c\u043f;', 
                '\u043bј', '\u043dј', '\u041bј', '\u041d\u0458', '\u041bЈ', '\u041d\u0408', '\u0434ж', 
                '\u0414\u0436', '\u0414\u0416'], ['&nbsp;', '&amp;', '\u0459', '\u045a', '\u0409', '\u040a', 
                '\u0409', '\u040a', '\u045f', '\u040f', '\u040f']] 
            },
            c2l : 
            {
                charMap : ['абцдефгхийклмнопрстувзђћАБЦДЕФГХИЈКЛМНОПРСТУВЗЂЋ ь.,:ыэЭ', 'abcdefghijklmnoprstuvzđćABCDEFGHIJKLMNOPRSTUVZĐĆ-  --yee'], 
                multiPre : [[], []], 
                multiPost : [['\u0459', '\u045a', '\u0409', '\u040a', '\u045f', '\u040f', '\u044f', '\u042f',
                    '\u0429', '\u0449', '\u0447', '\u0427',
                    '\u0448','\u0428','\u0436','\u0416','\u042e','\u044e'], 
                ['lj', 'nj', 'Lj', 'Nj', 'Dž', 'Dž', 'ya', 'Ya', 'Shh', 'shh', 'ch', 'Ch',
                'sh','Sh','zh','Zh','Ju','ju']] 
            },
            yu2ascii : 
            {
                charMap : ['абцдефгхијклмнопрстувзшђжчћАБЦДЕФГХИЈКЛМНОПРСТУВЗШЂЖЧЋabcdefghijklmnoprstuvzšđžčćABCDEFGHIJKLMNOPRSTUVZŠĐŽČĆ', 
                'abcdefghijklmnoprstuvzsđzccABCDEFGHIJKLMNOPRSTUVZSĐZCCabcdefghijklmnoprstuvzsđzccABCDEFGHIJKLMNOPRSTUVZSĐZCC'], 
                multiPre : [[], []], multiPost : [['\u0459', '\u045a', '\u0409', '\u040a', '\u045f', '\u040f', 
                'đ', 'Đ'], ['lj', 'nj', 'Lj', 'Nj', 'Dž', 'Dž', 'dj', 'Dj']] 
            }
        }
    };
})(jQuery);
