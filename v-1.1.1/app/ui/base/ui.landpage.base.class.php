<?php

/*
 * hold footer ui functions
 */
require_once "ui/ui.common.class.php";

class uiLandPageBase extends uiCommon {

    public function __construct() {
        parent::__construct();
    }

//    /**
//     * reuturn ui html for landing page
//     * 
//     */
//    public function getLandingPageUi() {
//
//        $html = $this->landingSection1();
//
//        return $html;
//    }

//    /**
//     * return landingSection1 for landing page ui    
//     * 
//     */
//    protected function landingSection1() {
//        $siteName=SITE_NAME;
//        $html = <<<EOT
//<div class="container-fluid">
//    <div class="row "> 
//        <div class="col-md-12">
//            <br>
//            <div class="jumbotron">
//                <h1>{$siteName}</h1>
//                <p>{$siteName}</p>                
//                <br><br>
//            </div>
//        </div>                  
//    </div>    
//</div>
//EOT;
//        return $html;
//    }

}

?>