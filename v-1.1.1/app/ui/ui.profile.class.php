<?php

/*
 * hold footer ui functions
 */
require_once "base/ui.profile.base.class.php";

class uiProfile extends uiProfileBase {

    public function __construct() {
        parent::__construct();
    }

    /**
     * return markup for school section
     * 
     * @param array $school school data array
     */
    public function schoolSection($school, $schoolEventsBord, $reload = false) {

        $mMarckup = ' <div class="container-fluid school-header">
                        <div class="row">
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <h2 class="text-center">' . langText('school') . '</h2>
                                <hr class="small">
                            </div>
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                        </div>
                      </div><!-- ./container-fluid -->';
        $mMarckup .= '<div class="container-fluid">';
        $mMarckup .= '<div class="row school-container">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">                            
                            <div class="row school-content">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="row">                                                           
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 school-holder-col">';
        $mMarckup .= $this->schoolColContent($school);
        $mMarckup .= '                  </div>
                                    </div>';
        //school events bord row
        $mMarckup .= $schoolEventsBord;
        $mMarckup .= '        </div>
                            </div>
                        </div>
                    </div>';
        $mMarckup .= '</div><!-- ./container-fluid -->';

        if (!$reload)
            $mMarckup .= '<script src="' . JS_URL . '/school.js"></script>';

        return $mMarckup;
    }

    /**
     * return scholl column content
     * 
     * @param array $school school data array
     */
    protected function schoolColContent($school) {
        $mMarckup = '';
        
        $mMarckup .='<div class="well well-lg">';
        $mMarckup .='<div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <p class="lead text-right">' . $this->et($this->caseFixToTile($school['name'])) . '</p>
                        </div> 
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <img src="' . UPLOAD_IMG_URL . '/' . $school['logo_stored_fname'] . '" class="img-responsive img-thumbnail" alt="image">
                        </div>
                     </div>';
        $mMarckup .='</div>';
//        $mMarckup .='<div class="jumbotron">
//                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
//                            <h3 class="text-center">' . $this->et($this->caseFixToTile($school['name'])) . '</h3>
//                        </div> 
//                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
//                            <img src="' . UPLOAD_IMG_URL . '/' . $school['logo_stored_fname'] . '" class="img-responsive" alt="image">
//                        </div>
//                    </div>';

        return $mMarckup;
    }

    /**
     * return general Settings Tab Tab html
     */
    public function generalSettings($basicSettings, $usernameSettings, $emailSettings, $passwordSettings) {
        $markup = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 general-settings-content">';
        $markup .= '    <div class="row genset-in-header">
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <h2 class="text-center">' . langText('profile_user_settings') . '</h2>
                                <hr class="small">
                            </div>
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                ' . $basicSettings . '
                                ' . $usernameSettings . '
                                ' . $emailSettings . '
                                ' . $passwordSettings . '
                            </div>
                            <div class="col-sm-3 col-md-4 col-lg-4 hidden-xs">
                            </div>
                        </div>';
        $markup .='</div>';

        return $markup;
    }

    /*
     * return settings Username Panel html
     */

    public function settingsUsernamePanel($username) {

        $markup = '  <div class="form-group">     
                        <label for="username">' . langText('username') . '</label>
                        <input type="text" class="form-control" value="' . $this->et($username) . '" id="username" name="username" readonly="readonly" placeholder="' . langText('username') . '">                        
                    </div>';
        return $markup;
    }

    /**
     * return profile settings save button ui html
     */
    public function settingsSaveBtn($panel) {
        $btnSizeClasses = 'col-xs-8 col-sm-6 col-md-4 col-lg-4';

        $markup = ' <div class="form-group button-form-group">
                            <button type="button" id="save_' . $panel . '" class="btn btn-sm btn-default pull-left ' . $btnSizeClasses . '" onclick="saveSettings(\'' . $panel . '\')">' . langText('save') . '</button>
                    </div>';
        return $markup;
    }

    /**
     * schoolEventsBord 
     * 
     * @param type $internships
     * @param type $jobShadows
     * @param type $volunteering
     * @return string
     */
    public function schoolEventsBord($internships, $jobShadows, $volunteering, $viewDialog) {
        $markup = ' <div class="row">
                        <div class="col-sm-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="text-center">Internships</h3></div>
                                <div class="panel-body">
                                    <blockquote>
                                        <p>Here are internship opportunities for you and join for opportunities you interested.</p>
                                    </blockquote>
                                </div>
                                <div id="schBordInternships">
                                    ' . $internships . '
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="text-center">Job Shadows</h3></div>
                                <div class="panel-body">
                                    <blockquote>
                                        <p>Here are job shadows opportunities for you and join for opportunities you interested.</p>
                                    </blockquote>
                                </div>
                                <div id="schBordJobShadows">
                                    ' . $jobShadows . '
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="text-center">Volunteering</h3></div>
                                <div class="panel-body">
                                    <blockquote>
                                        <p>Here are volunteering opportunities for you and join for opportunities you interested.</p>
                                    </blockquote>
                                </div>
                                <div id="schBordVolunteering">
                                    ' . $volunteering . '
                                </div>
                            </div>
                        </div>
                    </div>';
        $markup.=$viewDialog;

        return $markup;
    }

}

?>