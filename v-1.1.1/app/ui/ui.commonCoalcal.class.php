<?php

/*
 * hold common ui functions only for commonRun100days and 
 * this is extened in htmlElements class so will be able to access in all ui classes
 */

class commonCoalcal {
    /*
     * variables
     */

    public function __construct() {
        
    }

    /**
     * return js and html code need for google Analytics
     */
    public function googleAnalytics() {

        $analyticsAcc = GOOGLE_ANALYTICS_ACCOUNT;
        $analyticsCode = '';
        if (GOOGLE_ANALYTICS_ACCOUNT != '') {
            $analyticsCode .= <<<EOT
        <!-- Analytics -->
        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '{$analyticsAcc}']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>
        <!-- End Analytics -->
EOT;
        }

        return $analyticsCode;
    }

    /**
     * fix case of words which all words are in uppercase to fix to title style     
     * If text has all in uppercase first convert to lower case and then to have all words first character upper case . 
     * For others just make sure all words has first letter uppercase.
     */
    public function caseFixToTile($text) {

        if (strtoupper($text) == $text) {
            //full string is upper case so we fix this to have all works first letter to be uppercase
            $text = strtolower($text);
        }
        //make sure all other cases first word first letter is uppercaes
        $text = ucwords($text);

        //force words in $GLOBALS['titleForceLowerCases'] to be lower for title 
        if (!empty($GLOBALS['titleForceLowerCases'])) {
            $regex = array();
            $replace = array();
            //convert $GLOBALS['titleForceLowerCases'] to regex
            foreach ($GLOBALS['titleForceLowerCases'] as $word) {
                //make sure lower case
                $word = strtolower($word);

                //for place where word is at middle of sentences 
                $regex[] = '/\s' . $word . '\s/i';
                $replace[] = ' ' . $word . ' ';

                //for place where word is at end of sentences 
                $regex[] = '/\s' . $word . '$/i';
                $replace[] = ' ' . $word;

                //for place where word is at end of sentences and just bfore .
                $regex[] = '/\s' . $word . '\.$/i';
                $replace[] = ' ' . $word . '.';

                //for if word is at start of sentece whihc is in middle
                $regex[] = '/\.\s' . $word . '\s/i';
                $replace[] = '. ' . $word . ' ';
                $regex[] = '/\.' . $word . '\s/i';
                $replace[] = '.' . $word . ' ';
            }



            $text = preg_replace($regex, $replace, $text);
        }

        return $text;
    }

}

?>