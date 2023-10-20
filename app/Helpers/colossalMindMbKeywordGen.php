<?php

namespace App\Helpers;

/**
 *
 * Multibyte Keyword Generator
 * Copyright (c) 2009-2012, Peter Kahl. All rights reserved. www.colossalmind.com
 * Use of this source code is governed by a GNU General Public License
 * that can be found in the LICENSE file.
 *
 * https://github.com/peterkahl/multibyte-keyword-generator
 *
 */
class  colossalMindMbKeywordGen
{
    public const COMMON_WORDS_EN_GB = [
        'able',
        'about',
        'above',
        'act',
        'add',
        'afraid',
        'after',
        'again',
        'against',
        'age',
        'ago',
        'agree',
        'all',
        'almost',
        'alone',
        'along',
        'already',
        'also',
        'although',
        'always',
        'am',
        'amount',
        'an',
        'and',
        'anger',
        'angry',
        'animal',
        'another',
        'answer',
        'any',
        'appear',
        'apple',
        'are',
        'arrive',
        'arm',
        'arms',
        'around',
        'arrive',
        'as',
        'ask',
        'at',
        'attempt',
        'aunt',
        'away',
        'back',
        'bad',
        'bag',
        'bay',
        'be',
        'became',
        'because',
        'become',
        'been',
        'before',
        'began',
        'begin',
        'behind',
        'being',
        'bell',
        'belong',
        'below',
        'beside',
        'best',
        'better',
        'between',
        'beyond',
        'big',
        'body',
        'bone',
        'born',
        'borrow',
        'both',
        'bottom',
        'box',
        'boy',
        'break',
        'bring',
        'brought',
        'bug',
        'built',
        'busy',
        'but',
        'buy',
        'by',
        'call',
        'came',
        'can',
        'cause',
        'choose',
        'close',
        'close',
        'consider',
        'come',
        'consider',
        'considerable',
        'contain',
        'continue',
        'could',
        'cry',
        'cut',
        'dare',
        'dark',
        'deal',
        'dear',
        'decide',
        'deep',
        'did',
        'die',
        'do',
        'does',
        'dog',
        'done',
        'doubt',
        'down',
        'during',
        'each',
        'ear',
        'early',
        'eat',
        'effort',
        'either',
        'else',
        'end',
        'enjoy',
        'enough',
        'enter',
        'etc',
        'even',
        'ever',
        'every',
        'except',
        'expect',
        'explain',
        'fail',
        'fall',
        'far',
        'fat',
        'favor',
        'fear',
        'feel',
        'feet',
        'fell',
        'felt',
        'few',
        'fill',
        'find',
        'fit',
        'fly',
        'follow',
        'for',
        'forever',
        'forget',
        'from',
        'front',
        'full',
        'fully',
        'gave',
        'get',
        'gives',
        'goes',
        'gone',
        'good',
        'got',
        'gray',
        'great',
        'green',
        'grew',
        'grow',
        'guess',
        'had',
        'half',
        'hang',
        'happen',
        'has',
        'hat',
        'have',
        'he',
        'hear',
        'heard',
        'held',
        'hello',
        'help',
        'her',
        'here',
        'hers',
        'high',
        'highest',
        'highly',
        'hill',
        'him',
        'his',
        'hit',
        'hold',
        'hot',
        'how',
        'however',
        'i',
        'if',
        'ill',
        'in',
        'include',
        'including',
        'included',
        'indeed',
        'instead',
        'into',
        'iron',
        'is',
        'it',
        'its',
        'just',
        'keep',
        'kept',
        'knew',
        'know',
        'known',
        'late',
        'least',
        'led',
        'left',
        'lend',
        'less',
        'let',
        'like',
        'likely',
        'lone',
        'long',
        'longer',
        'look',
        'lot',
        'make',
        'many',
        'may',
        'me',
        'mean',
        'met',
        'might',
        'mile',
        'mine',
        'moon',
        'more',
        'most',
        'move',
        'much',
        'must',
        'my',
        'near',
        'nearly',
        'necessary',
        'neither',
        'never',
        'next',
        'no',
        'none',
        'nor',
        'not',
        'note',
        'nothing',
        'now',
        'number',
        'of',
        'off',
        'often',
        'oh',
        'on',
        'once',
        'only',
        'or',
        'other',
        'ought',
        'our',
        'out',
        'please',
        'prepare',
        'probable',
        'pull',
        'pure',
        'push',
        'put',
        'raise',
        'ran',
        'rather',
        'reach',
        'realize',
        'reply',
        'require',
        'rest',
        'run',
        'said',
        'same',
        'sat',
        'saw',
        'say',
        'see',
        'seem',
        'seen',
        'self',
        'sell',
        'sent',
        'separate',
        'set',
        'shall',
        'she',
        'should',
        'side',
        'sign',
        'since',
        'so',
        'sold',
        'some',
        'soon',
        'sorry',
        'stay',
        'step',
        'stick',
        'still',
        'stood',
        'such',
        'sudden',
        'suppose',
        'take',
        'taken',
        'talk',
        'tall',
        'tell',
        'ten',
        'than',
        'thank',
        'that',
        'the',
        'their',
        'them',
        'then',
        'there',
        'therefore',
        'these',
        'they',
        'this',
        'those',
        'though',
        'through',
        'till',
        'to',
        'today',
        'told',
        'tomorrow',
        'too',
        'took',
        'tore',
        'tought',
        'toward',
        'tried',
        'tries',
        'trust',
        'try',
        'turn',
        'two',
        'under',
        'until',
        'up',
        'upon',
        'us',
        'use',
        'usual',
        'various',
        'verb',
        'very',
        'visit',
        'want',
        'was',
        'we',
        'well',
        'went',
        'were',
        'what',
        'when',
        'where',
        'whether',
        'which',
        'while',
        'white',
        'who',
        'whom',
        'whose',
        'why',
        'will',
        'with',
        'within',
        'without',
        'would',
        'yes',
        'yet',
        'you',
        'young',
        'your',
        'yours',
    ];

    public const COMMON_WORDS_FA_IR = [
        'به',
        'های',
        'در',
        'ادامه',
        'از',
        'کنید',
        'است',
        'را',
        'کلیک',
        'که',
        'خود',
        'می',
        'آن',
        'ان',
        'تا',
        'بر',
        'است',
        'هست',
        'شده',
        'ها',
        'هم',
        'شود',
        'شد',
        'می',
        'دارد',
        'برای',
        'با',
        'تو',
        'بود',
        'اگر',
        'ما',
        'من',
        'او',
        ' ',
        '،',
        'ها',
        '.',
        'کاملا',
        'حتما',
        'شده',
        'رسید',
        'بر هم',
        'شدن',
        'البته',
        'دارد',
        'رای',
        'برای',
        'داشته',
        'باشند',
        'باشد',
        'این',
        'آن',
        'داشتند',
        'بالا',
        'بیاید ',
        'آنها',
        'برقرار',
        'نداشته',
        'دارند',
        'باید',
        'چند',
        'هستند',
        'چون',
        'بیاید ',
        'به',
        'با',
        'بی',
        'بند',
        'بدهد',
        'بت',
        'بي',
        'بتان',
        'بِ',
        'بُ',
        'بم',
        'باً',
        'بَ',
        'بة',
        'بشو',
        'بى',
        'های',
        'ها',
        'هاي',
        'هاست',
        'هاى',
        'هاش',
        'هام',
        'هاشو',
        'هات',
        'هاتان',
        'هاشان',
        'هایى',
        'در',
        'دری',
        'درم',
        'درهم',
        'درِ',
        'دره',
        'درش',
        'درن',
        'ادامۀ',
        'از',
        'ازه',
        'ازتون',
        'ازش',
        'ازی',
        'ازن',
        'ازي',
        'است',
        'ای',
        'اي',
        'ام',
        'اش',
        'اتان',
        'ات',
        'ایی',
        'امان',
        'اى',
        'اتو',
        'اء',
        'اِ',
        'اه',
        'اهای',
        'ايي',
        'را',
        'راست',
        'رای',
        'راي',
        'که',
        'کی',
        'کِ',
        'کاش',
        'کشان',
        'کتون',
        'کُ',
        'کَ',
        'کي',
        'می',
        'مي',
        'مه',
        'مى',
        'متان',
        'مت',
        'متون',
        'مُ',
        'مَ',
        'مشو',
        'مِ',
        'مش',
        'ماً',
        'متو',
        'مم',
        'مة',
        'مك',
        'مون',
        'خود',
        'خودي',
        'خودی',
        'خودِ',
        'خودت',
        'خودش',
        'خودم',
        'خودمان',
        'خودشان',
        'خودتو',
        'خودمون',
        'خودتون',
        'خودشو',
        'خودتان',
        'خودشم',
        'خودشون',
        'آن',
        'آنها',
        'آنان',
        'آنجا',
        'آنچه',
        'آنم',
        'آنند',
        'آنكه',
        'آنِ',
        'آنت',
        'آنی',
        'تا',
        'تام',
        'تایی',
        'تای',
        'تاشو',
        'تاي',
        'این',
        'اینکه',
        'اینم',
        'اینه',
        'اینی',
        'اینو',
        'بر',
        'برهم',
        'بره',
        'برند',
        'برم',
        'بری',
        'بریم',
        'برِ',
        'برش',
        'برن',
        'بران',
        'بري',
        'شده',
        'شدن',
        'شدت',
        'شدم',
        'شدند',
        'شدی',
        'شدة',
        'شدگان',
        'شدیم',
        'شدد',
        'شدش',
        'ﺑﻪ',
        'هم',
        'همه',
        'همین',
        'همی',
        'همة',
        'همت',
        'همو',
        'کردن',
        'کرد',
        'کرده',
        'کردند',
        'کردم',
        'کردی',
        'کردیم',
        'کردة',
        'کردو',
        'باشد',
        'باشید',
        'باشند',
        'باش',
        'باشیم',
        'باشی',
        'باشم',
        'باشه',
        'باشيم',
        'باشن',
        'باشيد',
        'ﻫﺎ',
        'ﻫﺎي',
        'ﻫﺎی',
        'ﻫﺎى',
        'شود',
        'ﻣﯽ',
        'ﻣﯽ',
        'دارد',
        'دارند',
        'دار',
        'داران',
        'دارم',
        'داره',
        'داری',
        'دارن',
        'داري',
        'دارش',
        'ﺑﺎ',
        'بود',
        'بودن',
        'بودند',
        'بوده',
        'بودم',
        'بودی',
        'بودیم',
        'دهم',
        'دهد',
        'دهند',
        'دهی',
        'تو',
        'توی',
        'اگر',
        '',
    ];

    //declare variables
    public $contents;

    public $encoding;

    public $lang;

    public $ignore; // array; languages to ignore

    // generated keywords
    public $keywords;

    // minimum word length for inclusion into the single word metakeys
    public $wordLengthMin;

    public $wordOccuredMin;

    // minimum word length for inclusion into the 2-word phrase metakeys
    public $word2WordPhraseLengthMin;

    public $phrase2WordLengthMinOccur;

    // minimum word length for inclusion into the 3-word phrase metakeys
    public $word3WordPhraseLengthMin;

    // minimum phrase length for inclusion into the 2-word phrase metakeys
    public $phrase2WordLengthMin;

    public $phrase3WordLengthMinOccur;

    // minimum phrase length for inclusion into the 3-word phrase metakeys
    public $phrase3WordLengthMin;

    //------------------------------------------------------------------
    public function __construct($params)
    {
        // language or default language; if not defined
        if (!isset($params['lang'])) {
            $this->lang = 'en_GB';
        } else {
            $this->lang = $params['lang'];
        } // case sensitive
        // multibyte internal encoding
        if (!isset($params['encoding'])) {
            $this->encoding = 'UTF-8';
        } else {
            $this->encoding = strtoupper($params['encoding']);
        } // case insensitive
        mb_internal_encoding($this->encoding);
        // languages to ignore
        if (isset($params['ignore']) && is_array($params['ignore'])) {
            $this->ignore = $params['ignore'];
        } // array of language codes
        else {
            $this->ignore = false;
        }
        // clean up input string; break along punctuations; explode into array
        if ($this->ignore !== false && in_array($this->lang, $this->ignore)) {
            $this->contents = false;
        } // language to be ignored
        else {
            $this->contents = $this->process_text($params['content']);
        }
        // LOAD THE PARAMETERS AND DEFAULTS
        // single keyword
        if (isset($params['min_word_length'])) { // value 0 means disable
            $this->wordLengthMin = $params['min_word_length'];
        } else {
            // if not set, use this default
            $this->wordLengthMin = 5;
        }
        if (isset($params['min_word_occur'])) {
            $this->wordOccuredMin = $params['min_word_occur'];
        } else {
            // if not set, use this default
            $this->wordOccuredMin = 3;
        }
        //--------------------------------------------------------------
        // 2-word keyphrase
        if (isset($params['min_2words_length']) && $params['min_2words_length'] == 0) { // value 0 means disable
            $this->word2WordPhraseLengthMin = false;
        } else {
            if (isset($params['min_2words_length']) && $params['min_2words_length'] !== 0) {
                $this->word2WordPhraseLengthMin = $params['min_2words_length'];
                $this->phrase2WordLengthMin = $params['min_2words_phrase_length'];
                $this->phrase2WordLengthMinOccur = $params['min_2words_phrase_occur'];
            } else {
                // if not set, use these defaults
                $this->word2WordPhraseLengthMin = 4;
                $this->phrase2WordLengthMin = 8;
                $this->phrase2WordLengthMinOccur = 3;
            }
        }
        //--------------------------------------------------------------
        // 3-word keyphrase
        if (isset($params['min_3words_length']) && $params['min_3words_length'] == 0) { // value 0 means disable
            $this->word3WordPhraseLengthMin = false;
        } else {
            if (isset($params['min_3words_length']) && $params['min_3words_length'] !== 0) {
                $this->word3WordPhraseLengthMin = $params['min_3words_length'];
                $this->phrase3WordLengthMin = $params['min_3words_phrase_length'];
                $this->phrase3WordLengthMinOccur = $params['min_3words_phrase_occur'];
            } else {
                // if not set, use these defaults
                $this->word3WordPhraseLengthMin = 4;
                $this->phrase3WordLengthMin = 12;
                $this->phrase3WordLengthMinOccur = 3;
            }
        }
        //--------------------------------------------------------------
    }

    //------------------------------------------------------------------

    public function process_text($str)
    {
        if (preg_match('/^\s*$/', $str)) {
            return false;
        }
        // strip HTML
        $str = $this->html2txt($str);
        //convert all characters to lower case
        $str = mb_strtolower($str, $this->encoding);
        // some cleanup
        $str = ' '.$str.' '; // pad that is necessary
        $str = preg_replace('#\ [a-z]{1,2}\ #i', ' ', $str); // remove 2 letter words and numbers
        $str = preg_replace('#[0-9\,\.:]#', '',
            $str); // remove numerals, including commas and dots that are part of the numeral
        $str = preg_replace("/([a-z]{2,})('|’)s/", '\\1', $str); // remove only the 's (as in mother's)
        $str = str_replace('-', ' ', $str); // remove hyphens (-)
        // IGNORE WORDS LIST
        // add, remove, edit as needed
        // make sure that paths are correct and necessary files are uploaded to your server
        $common = $this->getConst('COMMON_WORDS_'.strtoupper($this->lang));
        if (isset($common)) {
            //			var_dump($common);
            foreach ($common as $word) {
                $str = str_replace(' '.$word.' ', ' ', $str);
            }
            unset($common);
        }
        // replace multiple whitespaces
        $str = preg_replace('/\s\s+/', ' ', $str);
        $str = trim($str);
        if (preg_match('/^\s*$/', $str)) {
            return false;
        }
        // WORD SEGMENTATION
        // break along paragraphs, punctuations
        $arrA = explode("\n", $str);
        foreach ($arrA as $key => $value) {
            if (strpos($value, '.') !== false) {
                $arrB[$key] = explode('.', $value);
            } else {
                $arrB[$key] = $value;
            }
        }
        $arrB = $this->array_flatten($arrB);
        unset($arrA);
        foreach ($arrB as $key => $value) {
            if (strpos($value, '!') !== false) {
                $arrC[$key] = explode('!', $value);
            } else {
                $arrC[$key] = $value;
            }
        }
        $arrC = $this->array_flatten($arrC);
        unset($arrB);
        foreach ($arrC as $key => $value) {
            if (strpos($value, '?') !== false) {
                $arrD[$key] = explode('?', $value);
            } else {
                $arrD[$key] = $value;
            }
        }
        $arrD = $this->array_flatten($arrD);
        unset($arrC);
        foreach ($arrD as $key => $value) {
            if (strpos($value, ',') !== false) {
                $arrE[$key] = explode(',', $value);
            } else {
                $arrE[$key] = $value;
            }
        }
        $arrE = $this->array_flatten($arrE);
        unset($arrD);
        foreach ($arrE as $key => $value) {
            if (strpos($value, ';') !== false) {
                $arrF[$key] = explode(';', $value);
            } else {
                $arrF[$key] = $value;
            }
        }
        $arrF = $this->array_flatten($arrF);
        unset($arrE);
        foreach ($arrF as $key => $value) {
            if (strpos($value, ':') !== false) {
                $arrG[$key] = explode(':', $value);
            } else {
                $arrG[$key] = $value;
            }
        }
        $arrG = $this->array_flatten($arrG);
        unset($arrF);

        //--------------------------------------------------------------
        return $arrG;
    }

    //------------------------------------------------------------------

    public function html2txt($str)
    {
        if ($str == '') {
            return '';
        }
        $str = preg_replace('#<script.*?>[\s\S]*<\/script>#i', '', $str); // removes JavaScript
        $str = preg_replace('#(</p>\s*<p>|</div>\s*<div>|</li>\s*<li>|</td>\s*<td>|<br>|<br\ ?/>)#i', "\n",
            $str); // we use \n to segment words
        $str = preg_replace("#(\n){2,}#", "\n", $str); // replace multiple with single line breaks
        $str = strip_tags($str);
        $unwanted = [
            '"',
            '“',
            '„',
            '<',
            '>',
            '/',
            '*',
            '[',
            ']',
            '+',
            '=',
            '#',
        ];
        $str = str_replace($unwanted, ' ', $str);
        $str = preg_replace('/&nbsp;/i', ' ', $str); // remove &nbsp;
        $str = preg_replace('/&[a-z]{2,5};/i', '', $str); // remove &trade;  &copy;
        $str = preg_replace('/\s\s+/', ' ', $str); // replace multiple white spaces

        return trim($str);
    }

    //------------------------------------------------------------------

    public function getConst($name)
    {
        return constant("self::{$name}");
    }

    //single words

    public function array_flatten($array, $flat = false)
    {
        if (!is_array($array) || empty($array)) {
            return '';
        }
        if (empty($flat)) {
            $flat = [];
        }
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $flat = $this->array_flatten($val, $flat);
            } else {
                $flat[] = $val;
            }
        }

        return $flat;
    }

    // 2-word phrases

    public function get_keywords()
    {
        if ($this->contents === false) {
            return '';
        }
        $onew_arr = $this->parse_words();
        $twow_arr = $this->parse_2words();
        $thrw_arr = $this->parse_3words();
        // remove 2-word phrases if same single words exist
        if ($onew_arr !== false && $twow_arr !== false) {
            $cnt = count($onew_arr);
            for ($i = 0; $i < $cnt - 1; $i++) {
                foreach ($twow_arr as $key => $phrase) {
                    if ($onew_arr[$i].' '.$onew_arr[$i + 1] === $phrase) {
                        unset($twow_arr[$key]);
                    }
                }
            }
        }
        // remove 3-word phrases if same single words exist
        if ($onew_arr !== false && $thrw_arr !== false) {
            $cnt = count($onew_arr);
            for ($i = 0; $i < $cnt - 2; $i++) {
                foreach ($thrw_arr as $key => $phrase) {
                    if ($onew_arr[$i].' '.$onew_arr[$i + 1].' '.$onew_arr[$i + 2] === $phrase) {
                        unset($thrw_arr[$key]);
                    }
                }
            }
        }
        // remove duplicate ENGLISH plural words
        if (substr($this->lang, 0, 2) == 'en' && $onew_arr !== false) {
            $cnt = count($onew_arr);
            for ($i = 0; $i < $cnt - 1; $i++) {
                for ($j = $i + 1; $j < $cnt; $j++) {
                    if (!(array_key_exists($i, $onew_arr) && array_key_exists($j, $onew_arr))) {

                        continue;
                    }
                    if ($onew_arr[$i].'s' == $onew_arr[$j]) {
                        unset($onew_arr[$j]);
                    }
                    if (array_key_exists($j, $onew_arr) && $onew_arr[$i] == $onew_arr[$j].'s') {
                        unset($onew_arr[$i]);
                    }


                }
            }
        }
        // ready for output - implode arrays
        if ($onew_arr !== false) {
            $onew_kw = implode(',', $onew_arr).',';
        } else {
            $onew_kw = '';
        }
        if ($twow_arr !== false) {
            $twow_kw = implode(',', $twow_arr).',';
        } else {
            $twow_kw = '';
        }
        if ($thrw_arr !== false) {
            $thrw_kw = implode(',', $thrw_arr).',';
        } else {
            $thrw_kw = '';
        }
        $keywords = $onew_kw.$twow_kw.$thrw_kw;

        return rtrim($keywords, ',');
    }

    // 3-word phrases

    public function parse_words()
    {
        if ($this->wordLengthMin === 0) {
            return false;
        } // 0 means disable
        $str = implode(' ', (array) $this->contents);
        $str = $this->strip_punctuations($str);
        // create an array out of the site contents
        $s = explode(' ', $str);
        // iterate inside the array
        foreach ($s as $key => $val) {
            if (mb_strlen($val, $this->encoding) >= $this->wordLengthMin) {
                $k[] = $val;
            }
        }
        if (!isset($k)) {
            return false;
        }
        // count the words; this is the real magic!
        $k = array_count_values($k);

        return $this->occure_filter($k, $this->wordOccuredMin);
    }

    //------------------------------------------------------------------

    public function strip_punctuations($str)
    {
        if ($str == '') {
            return '';
        }
        // edit as needed
        $punctuations = [
            '"',
            "'",
            '’',
            '˝',
            '„',
            '`',
            '.',
            ',',
            ';',
            ':',
            '+',
            '±',
            '-',
            '_',
            '=',
            '(',
            ')',
            '[',
            ']',
            '<',
            '>',
            '{',
            '}',
            '/',
            '\\',
            '|',
            '?',
            '!',
            '@',
            '#',
            '%',
            '^',
            '&',
            '§',
            '$',
            '¢',
            '£',
            '€',
            '¥',
            '₣',
            '฿',
            '*',
            '~',
            '。',
            '，',
            '、',
            '；',
            '：',
            '？',
            '！',
            '…',
            '—',
            '·',
            'ˉ',
            'ˇ',
            '¨',
            '‘',
            '’',
            '“',
            '”',
            '々',
            '～',
            '‖',
            '∶',
            '＂',
            '＇',
            '｀',
            '｜',
            '〃',
            '〔',
            '〕',
            '〈',
            '〉',
            '《',
            '》',
            '「',
            '」',
            '『',
            '』',
            '．',
            '〖',
            '〗',
            '【',
            '】',
            '（',
            '）',
            '［',
            '］',
            '｛',
            '｝',
            '／',
            '“',
            '”',
        ];
        $str = str_replace($punctuations, ' ', $str);

        return preg_replace('/\s\s+/', ' ', $str);
    }
    //------------------------------------------------------------------
    // converts any-dimensional to 1-dimensional array

    public function occure_filter($array, $min)
    {
        $cnt = 0;
        foreach ($array as $word => $occured) {
            if ($occured >= $min) {
                $new[$cnt] = $word;
                $cnt++;
            }
        }
        if (isset($new)) {
            return $new;
        }

        return false;
    }

    //------------------------------------------------------------------

    public function parse_2words()
    {
        if ($this->word2WordPhraseLengthMin === false) {
            return false;
        } // 0 means disable
        foreach ($this->contents as $key => $str) {
            $str = $this->strip_punctuations($str);
            $arr[$key] = explode(' ', $str); // 2-dimensional array
        }
        $z = 0; // key of the 2-word array
        $lines = count($arr);
        for ($a = 0; $a < $lines; $a++) {
            $words = count($arr[$a]);
            for ($i = 0; $i < $words - 1; $i++) {
                if ((mb_strlen($arr[$a][$i],
                            $this->encoding) >= $this->word2WordPhraseLengthMin) && (mb_strlen($arr[$a][$i + 1],
                            $this->encoding) >= $this->word2WordPhraseLengthMin)) {
                    $y[$z] = $arr[$a][$i].' '.$arr[$a][$i + 1];
                    $z++;
                }
            }
        }
        if (!isset($y)) {
            return false;
        }
        // count the words; this is the real magic!
        $y = array_count_values($y);

        return $this->occure_filter($y, $this->phrase2WordLengthMinOccur);
    }

    //------------------------------------------------------------------

    public function parse_3words()
    {
        if ($this->word3WordPhraseLengthMin === false) {
            return false;
        } // 0 means disable
        foreach ($this->contents as $key => $str) {
            $str = $this->strip_punctuations($str);
            $arr[$key] = explode(' ', $str); // 2-dimensional array
        }
        $z = 0; // key of the 3-word array
        $lines = count($arr);
        for ($a = 0; $a < $lines; $a++) {
            $words = count($arr[$a]);
            for ($i = 0; $i < $words - 2; $i++) {
                if ((mb_strlen($arr[$a][$i],
                            $this->encoding) >= $this->word3WordPhraseLengthMin) && (mb_strlen($arr[$a][$i + 1],
                            $this->encoding) >= $this->word3WordPhraseLengthMin) && (mb_strlen($arr[$a][$i + 2],
                            $this->encoding) >= $this->word3WordPhraseLengthMin)) {
                    $y[$z] = $arr[$a][$i].' '.$arr[$a][$i + 1].' '.$arr[$a][$i + 2];
                    $z++;
                }
            }
        }
        if (!isset($y)) {
            return false;
        }
        // count the words; this is the real magic!
        $y = array_count_values($y);

        return $this->occure_filter($y, $this->phrase3WordLengthMinOccur);
    }

    //------------------------------------------------------------------

    public function remove_duplicate_keywords($str)
    {
        if ($str == '') {
            return $str;
        }
        $str = trim(mb_strtolower($str));
        $kw_arr = explode(',', $str); // array
        foreach ($kw_arr as $key => $val) {
            $kw_arr[$key] = trim($val);
            if ($kw_arr[$key] == '') {
                unset($kw_arr[$key]);
            }
        }
        $kw_arr = array_unique($kw_arr);
        // remove duplicate ENGLISH plural words
        if (substr($this->lang, 0, 2) != 'en') {
            return implode(',', $kw_arr);
        }
        $cnt = count($kw_arr);
        for ($i = 0; $i < $cnt; $i++) {
            for ($j = $i + 1; $j < $cnt; $j++) {
                if (!(array_key_exists($i, $kw_arr) && array_key_exists($j, $kw_arr))) {
                    $kw_arr = array_values($kw_arr);
                    continue;
                }
                if ($kw_arr[$i].'s' == $kw_arr[$j]) {
                    unset($kw_arr[$j]);
                } else {
                    if ($kw_arr[$i] == $kw_arr[$j].'s') {
                        unset($kw_arr[$i]);
                    } //--------------
                    else {
                        if (preg_match('#ss$#', $kw_arr[$j])) {
                            if ($kw_arr[$i] === $kw_arr[$j].'es') {
                                unset($kw_arr[$i]);
                            } // addresses VS address
                        } else {
                            if (preg_match('#ss$#', $kw_arr[$i]) && $kw_arr[$i].'es' === $kw_arr[$j]) {
                                unset($kw_arr[$j]);
                                // address VS addresses
                            }
                        }
                    }
                }
                //---------------

                $kw_arr = array_values($kw_arr);
            }
            $kw_arr = array_values($kw_arr);
        }
        // job is done!
        return implode(',', $kw_arr);
    }

    //------------------------------------------------------------------
    public function removeDuplicateKw($keywordsStr)
    {
        return implode(',', array_unique(explode(',', $keywordsStr)));
    }
}
//----------------------------------------------------------------------
