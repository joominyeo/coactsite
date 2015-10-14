<?php

/*
 * hold footer ui functions
 */
require_once "base/ui.landpage.base.class.php";

class uiLandPage extends uiLandPageBase {

    public function __construct() {
        
    }

    /**
     * reuturn ui html for landing page
     * 
     * @param string $customCnt custom content to show in custom-section section
     * @param string $schoolCnt school content to show in school-section section
     */
    public function getLandingPageUi($customCnt = '', $schoolCnt = '') {
        $defaultSectionHide = $customSectionHide = $schoolSectionHide = '';
        //if we show school dection we need to hide all default sections and custom section
        if ($schoolCnt != '') {
            $defaultSectionHide = 'hide';
            $customSectionHide = 'hide';
        }else if($customCnt!=''){
            $defaultSectionHide = 'hide';
            $schoolSectionHide = 'hide';
        }else {        
            $schoolSectionHide = 'hide';
            $customSectionHide = 'hide';
        }

        $custom = $this->customSection($customCnt, $customSectionHide);
        $specialAnnouncements = $this->specialAnnouncementsSection($defaultSectionHide);
        $header = $this->headerSection($defaultSectionHide);
        $about = $this->aboutSection($defaultSectionHide);
        $services = $this->servicesSection($defaultSectionHide);
        $portfolio = $this->portfolioSection($defaultSectionHide);
        $dataBase = $this->dataBaseSection($defaultSectionHide);
        $map = $this->mapSection($defaultSectionHide);
        $school = $this->schoolSection($schoolCnt, $schoolSectionHide);

        $html = <<<EOT
{$specialAnnouncements}
{$header}
{$custom}
{$school}
{$about}
{$services}
{$portfolio}
{$dataBase}
{$map}
EOT;

        return $html;
    }

    /**
     * return custom contet wrapped custion section markup
     * @param type $cnt
     * @param string $sectionClass class or classes for section
     * @return type
     */
    protected function customSection($cnt = '', $sectionClass = "") {
        $html = <<<EOT
   <!-- custom section -->
       <section id="custom-section" class="custom-section {$sectionClass}">{$cnt}</section>
EOT;

        return $html;
    }

    /**
     * return school contet wrapped custion section markup
     * @param type $cnt
     * @return type
     */
    protected function schoolSection($cnt = '', $sectionClass = "") {
        $html = <<<EOT
   <!-- custom section -->
       <section id="school-section" class="school-section {$sectionClass}">{$cnt}</section>
EOT;

        return $html;
    }

    /**
     * return home page map section
     * 
     * @return string
     */
    protected function mapSection($sectionClass = "") {
//        $html = <<<EOT
//<!-- Map -->
//<section id="map" class="map">
//    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://mapsengine.google.com/map/embed?mid=zaimlyzifF_s.kxSBkgrCWz-o"></iframe>
//    <br />
//    <small>
//        <a href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Twitter,+Inc.,+Market+Street,+San+Francisco,+CA&amp;aq=0&amp;oq=twitter&amp;sll=28.659344,-81.187888&amp;sspn=0.128789,0.264187&amp;ie=UTF8&amp;hq=Twitter,+Inc.,+Market+Street,+San+Francisco,+CA&amp;t=m&amp;z=15&amp;iwloc=A"></a>
//    </small>
//    </iframe>
//</section>
//EOT;
        $html = <<<EOT
<!-- Map -->
<section id="map" class="map default-section {$sectionClass}">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                <h2>Our Location</h2>
                <hr class="small">
                <div class="google-maps">                
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3312.3541639658447!2d-117.89340899999999!3d33.880531500000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80dcd5c4f923914d%3A0xe65d9e617ad4ec38!2sTroy+High+School!5e0!3m2!1sen!2slk!4v1441690284408" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>
            <!-- /.col-lg-10 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</section>
EOT;

        return $html;
    }

    /**
     * return home page dataBase section
     * 
     * @return string
     */
    protected function dataBaseSection($sectionClass = "") {
        $html = <<<EOT
<!--DataBase-->
<section id="database" class="default-section {$sectionClass}">
    <aside class="database">
        <div class="text-vertical-center">
            <h1>With Our Database, Students Can Find Their Ideal Activity.</h1>
            <br>
            <a href="https://www.obvibase.com/#token/WMUrM4OCzqoy/r/6XCBZrYExhS2" class="btn btn-dark btn-lg">Access the Database&nbsp;&nbsp;&nbsp;<i class="fa fa-database"></i></a>
        </div>
    </aside>
</section>
EOT;

        return $html;
    }

    /**
     * return home page portfolio section
     * 
     * @return string
     */
    protected function portfolioSection($sectionClass = "") {
        $APP_IMG_URL = APP_IMG_URL;
        $html = <<<EOT
<!-- Portfolio -->
<section id="board" class="portfolio default-section {$sectionClass}">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1 text-center">
                <h2>Our Board Members</h2>
                <hr class="small">
                <div class="row" id="project_container">
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/christian yu.jpg">
                                    <h1>Christian Yu <br>President and CEO</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/samir safi.jpg">
                                    <h1>Semir Shafi<br>Vice President</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/cai.jpg">
                                    <div><h1>Joomin Cai Yeo<br>CTO<br>Database & Web<br>Manager</h1></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/bryan ghaly.jpg">
                                    <h1>Bryan Ghaly<br>CFO</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/hyung.jpg">
                                    <h1>Hyung Song<br>Head Business Liason</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img title="The title attribute of the link appears over the image on hover" class="img-portfolio img-responsive" src="{$APP_IMG_URL}/victoria wu.jpg">
                                    <h1>Victoria Wu<br>Graphic Design &<br>Secretary</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/ezekial levin.jpg">
                                    <h1>Ezekiel Levin<br>Legal Guidance Officer</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/ryan castillo.jpg">
                                    <h1>Ryan Castillo<br>PublicityDirector</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="portfolio_container">
                            <div class="portfolio-item">
                                <a class="hovertext">
                                    <img class="img-portfolio img-responsive" src="{$APP_IMG_URL}/will dai.jpg">
                                    <h1>William Dai<br>CTO</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row (nested)
                <a href="#" class="btn btn-dark">View More Items</a>
                -->
            </div>
            <!-- /.col-lg-10 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</section>
EOT;

        return $html;
    }

    /**
     * return home page services section
     * 
     * @return string
     */
    protected function servicesSection($sectionClass = "") {
        $html = <<<EOT
<!-- Services -->
<!-- The circle icons use Font Awesome's stacked icon classes. For more information, visit http://fontawesome.io/examples/ -->
<section id="services" class="services bg-primary default-section {$sectionClass}">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-10 col-lg-offset-1">
                <h2>What We Do</h2>
                <hr class="small">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="service-item">
                            <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-compass fa-stack-1x text-primary"></i>
                            </span>
                            <h4>
                                <strong>Career Help</strong>
                            </h4>
                            <p>We provide students with hands on access to careers of their choice and possibly new options.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="service-item">
                            <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-globe fa-stack-1x text-primary"></i>
                            </span>
                            <h4>
                                <strong>Networking</strong>
                            </h4>
                            <p>Through COAL, our students will build a strong network of contacts.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="service-item">
                            <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-briefcase fa-stack-1x text-primary"></i>
                            </span>
                            <h4>
                                <strong>Internship Opportunities</strong>
                            </h4>
                            <p>Our comprehensive database helps students find an ideal internship.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="service-item">
                            <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-graduation-cap fa-stack-1x text-primary"></i>
                            </span>
                            <h4>
                                <strong>Scholarships</strong>
                            </h4>
                            <p>We reward our best members with scholarships.</p>
                        </div>
                    </div>
                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.col-lg-10 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</section>

EOT;

        return $html;
    }

    /**
     * return home page about section
     * 
     * @return string
     */
    protected function aboutSection($sectionClass = "") {
        $html = <<<EOT
<!-- About -->
<section id="about" class="about default-section {$sectionClass}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>COAL stands for Community Oriented Applied Learning</h2>
                <p class="lead">We want to help students gain skills for actual work experience outside of regular academics.</p>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</section>
EOT;

        return $html;
    }

    /**
     * return home page header section
     * 
     * @return string
     */
    protected function headerSection($sectionClass = "") {
        $singInText=langText('sign_in');
        $signInUrl=SITE_URL . '/login';
        $signUpUrl=SITE_URL . '/register';
        $html = <<<EOT
<!-- Header -->
<header id="top" class="header default-section {$sectionClass}">
    <div class="text-vertical-center">
        <h1>COAL | California</h1>
        <h3><font color="#FFFFFF">Empowering Students to Apply Classroom Knowledge to the Real World</font></h3>
        <br>
        <a href="{$signInUrl}" class="btn btn-dark btn-lg home-btn-login">{$singInText}</a>
        <a href="{$signUpUrl}" class="btn btn-dark btn-lg home-btn-register">Register</a>
    </div>
</header>
EOT;

        return $html;
    }

    /**
     * return special announcements section
     * 
     * @return string
     */
    protected function specialAnnouncementsSection($sectionClass = "") {
        $html = <<<EOT
<!-- Special Announcements -->
<section id="specialannouncements" class="specialannouncements default-section {$sectionClass}">
    <div class="text-vertical-center-kindleC">
        <!--<h1><font color="#FFFFFF">The COAL Kindle Conference</font></h1>
            <h3><font color="#FFFFFF">Empowering Students to Apply Classroom Knowledge to the Real World</font></h3>-->

        <div class="countDownButtons">
            <a href="http://goo.gl/forms/ggQ9fkVzso" target="_blank" class="btn btn-dark btn-lg"><font color="#000000">Become a Speaker</font></a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="http://goo.gl/forms/gRhuFVFz4v" target="_blank" class="btn btn-dark btn-lg"><font color="#000000">Become a Sponsor</font></a>
            <br>
            <a href="#top">
                <span class="fa-stack fa-3x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-angle-double-down fa-stack-1x fa-inverse"></i>
                </span></a>
        </div>
    </div>
</section>
EOT;

        return $html;
    }

}

?>