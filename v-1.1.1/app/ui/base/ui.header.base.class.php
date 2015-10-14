<?php

/*
 * hold header ui functions
 */
require_once "ui/ui.common.class.php";

class uiHeaderBase extends uiCommon {

    public function __construct() {
        parent::__construct();
    }

    /**
     * return top nav panel
     * 
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    public function get_top_nav($langs = array()) {

        $markup = ' <nav id="site_navbar"  class="navbar navbar-default navbar-fixed-top" role="navigation">
                        ' . $this->topNavOptions($langs) . '                
                    </nav>'  ;

        return $markup;
    }

    /*
     * return hader ui html
     */

    public function getHeaderUi() {
        $CSS_URL = CSS_URL;
        $JS_URL = JS_URL;
        $SITE_NAME = SITE_NAME;
        $APP_IMG_URL =APP_IMG_URL;
        $googleAnalytics = $this->googleAnalytics();
        $langTag = $this->langTag();

        $html = <<<EOT
<!DOCTYPE html>
<html {$langTag}>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="COAL Community Oriented Applied Learning">
        <meta name="author" content="Joo Min (Cai) Yeo">
        <title>{$SITE_NAME}</title>
        
        <link rel="shortcut icon" href="{$APP_IMG_URL}/tab.ico">

         <!-- jquery ui  -->
        <!--<link rel="stylesheet" href="{$CSS_URL}/jquery-ui.min.css" />-->
        
        <!-- Bootstrap compiled and minified CSS  -->
        <link rel="stylesheet" href="{$CSS_URL}/bootstrap.min.css"> 
            
        <!-- For datepicker -->
       <link href="{$CSS_URL}/bootstrap-datetimepicker.min.css" rel="stylesheet"> 

        <!-- FancyBox -->
        <link href="{$CSS_URL}/jquery.fancybox.css" rel="stylesheet">

        <!-- Font Icons, need for fancy box popups -->
        <link href="{$CSS_URL}/fonts.css" rel="stylesheet">
        
        
        <!-- Modernizr -->
        <script src="{$JS_URL}/modernizr.js"></script>    

        <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
        <link href="{$CSS_URL}/bootstrap-notify.css" rel="stylesheet" media="screen">
            
        <!-- Custom CSS -->
        <link href="{$CSS_URL}/stylish-portfolio.css" rel="stylesheet">
        
        <!-- Custom Fonts -->
        <link href="{$CSS_URL}/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
        <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
  
        <link href="{$CSS_URL}/default.css" rel="stylesheet" media="screen">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        {$googleAnalytics}
    </head>
    <body>   
EOT;


        return $html;
    }

    /*
     * return content wrapper
     */

    public function getContentWrapper($centent = '') {
        $html = <<<EOT
<div id="centent">
    {$centent}
</div>   
EOT;
        return $html;
    }

    /*
     * no user found alert
     */

    public function noUserFound() {
        $cnt = '<div class="alert alert-gray"><strong>Sorry! No user found.</strong></div>';
        return $cnt;
    }

}

?>