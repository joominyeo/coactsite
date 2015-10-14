<?php

/*
 * hold footer ui functions
 */
require_once "base/ui.footer.base.class.php";

class uiFooter extends uiFooterBase {

    public function __construct() {
        parent::__construct();
    }

    protected function footerFoot1() {
        $contact_us = langText('contact_us');

        $html = <<<EOT
<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1 text-center">
                <h4><strong>COAL</strong>
                </h4>
                <p>Troy High School<br>2200 Dorothy Ln<br>Fullerton, CA 92831</p>
                <ul class="list-unstyled">
                  <li><i class="fa fa-envelope-o fa-fw"></i>  <a href="mailto:management@coalcal.com">management@coalcal.com</a></li>
                  <li><i class="fa fa-envelope-o fa-fw"></i>  <a href="javascript:void(0);" onclick="contactSupport('view','contact');" title="{$contact_us}">{$contact_us}</a></li>
                </ul>
                <br>
                <!-- UPDATE WHEN SOCIAL MEDIA IS SET UP
                <ul class="list-inline">
                    <li>
                    <a href="#"><i class="fa fa-facebook fa-fw fa-3x"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-twitter fa-fw fa-3x"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-dribbble fa-fw fa-3x"></i></a>
                    </li>
                </ul>
                -->
                <hr class="small">
                <p class="text-muted">Copyright &copy; COAL 2015</p>
            </div>
        </div>
    </div>
</footer>
EOT;

        return $html;
    }


}

?>