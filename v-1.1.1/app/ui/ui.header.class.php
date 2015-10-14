<?php

/*
 * hold header ui functions
 */
require_once "base/ui.header.base.class.php";

class uiHeader extends uiHeaderBase {

    public function __construct() {
        parent::__construct();
    }

    /**
     * return side nav panel
     * 
     * @param array $langs list of lanuages array, each elment is row from languages table
     */
    public function get_side_nav($langs = array()) {
        
        $sideNav=  $this->sideNavOptions($langs);
        
        $markup = <<<EOT
        <!-- Navigation -->
        <a id="menu-toggle" href="#" class="btn btn-dark btn-lg toggle"><i class="fa fa-bars"></i></a>
        <nav id="sidebar-wrapper">
          {$sideNav}
        </nav>
EOT;
        return $markup;
    }
    
    public function getLoadingDiv(){
        $html='<nav id="loading-wrapper" class="hide">
                    <img src="'.APP_IMG_URL.'/loading-24x24.gif" style="">
               </nav>' ;
        return $html;
    }


    /*
     * return content wrapper
     */

    public function getContentWrapper($centent = '') {
        $html = <<<EOT
    {$centent}
EOT;
        return $html;
    }

}

?>