<?php
class UNL_OpenMap_Controller
{
    public $options = array('format' => 'html',
                            'view'   => 'map',
                            'mobile' => false);

    public $view_map = array('map'             => 'UNL_OpenMap_Map',


                             'campuses'            => 'UNL_OpenMap_MarkerList_Campuses',
                             'buildings'           => 'UNL_OpenMap_MarkerList_Buildings',
                             'citybuildingmarkers' => 'UNL_OpenMap_MarkerList_Buildings_CityCampus',
                             'eastbuildingmarkers' => 'UNL_OpenMap_MarkerList_Buildings_EastCampus',
                             'emergencyphones'     => 'UNL_OpenMap_MarkerList_EmergencyPhones',
                             'bikeracks'           => 'UNL_OpenMap_MarkerList_BikeRacks',
                             'policestations'      => 'UNL_OpenMap_MarkerList_PoliceStations',
                             'sculptures'          => 'UNL_OpenMap_MarkerList_Sculptures',
                             'search'              => 'UNL_OpenMap_MarkerList_BuildingSearch',
                             'filter'              => 'UNL_OpenMap_FilteredMarkerList',

                             // Marker Lists used for backwards compatibility
                             'rss_all.xml'         => 'UNL_OpenMap_MarkerList_AllMarkers',
                             'rss_city.xml'        => 'UNL_OpenMap_MarkerList_Buildings_CityCampus',
                             'rss_east.xml'        => 'UNL_OpenMap_MarkerList_Buildings_EastCampus',

                             'allbuildings'        => 'UNL_Common_Building',
                             'citybuildings'       => 'UNL_Common_Building_City',
                             'eastbuildings'       => 'UNL_Common_Building_East',
                             'lincolnbuildings'    => 'UNL_Common_Building_Lincoln',
                             'sculptureinfo'       => 'UNL_Common_Artists',

                             'building'            => 'UNL_OpenMap_Marker_Building',
                             'emergencyphone'      => 'UNL_OpenMap_Marker_EmergencyPhone',
                             'bikerack'            => 'UNL_OpenMap_Marker_BikeRack',
                             'sculpture'           => 'UNL_OpenMap_Marker_Sculpture',
                             'policestation'       => 'UNL_OpenMap_Marker_PoliceStation',

                             //'info'                => 'UNL_OpenMap_Marker_Info',
                             //'image'               => 'UNL_OpenMap_Marker_Image',
                             );

    public static $url;

    public $output;

    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        if ($this->options['format'] == 'mobile') {
            $this->options['mobile'] = true;
        }

        if ($this->options['format'] == 'html'
            && $this->options['mobile'] != 'no') {

            $this->options['mobile'] = self::isMobileClient();

            if ($this->options['mobile']) {
                $this->options['format'] = 'mobile';
            }
        }

        if ($this->options['mobile']) {

            $this->options['zoom'] = '15';
            //$this->options['view'] = 'lincoln';
        }

        $this->run();
    }

    function run()
    {
        if (isset($this->options['nugget']) && $this->options['nugget'] == 'image') {
            Savvy_ClassToTemplateMapper::$output_template['UNL_OpenMap'] = 'UNL/OpenMap-partial';
        }

        try {
            if (isset($this->view_map[$this->options['view']])) {
                $this->output = new $this->view_map[$this->options['view']]($this->options);
            } else {
                throw new Exception('Sorry, that view does not exist.', 404);
            }
        } catch(Exception $e) {
            $this->output = $e;
        }
    }

    /**
     * Get the URL to the main site.
     *
     * @return string The URL to the site
     */
    public static function getURL()
    {
        return self::$url;
    }

   /**
    * Add unique querystring parameters to a URL
    *
    * @param string $url               The URL
    * @param array  $additional_params Additional querystring parameters to add
    *
    * @return string
    */
    public static function addURLParams($url, $additional_params = array())
    {
        $params = array();
        if (strpos($url, '?') !== false) {
            list($url, $existing_params) = explode('?', $url);
            $existing_params = explode('&', $existing_params);
            foreach ($existing_params as $val) {
                list($var, $val) = explode('=', $val);
                $params[$var] = $val;
            }
        }

        $params = array_merge($params, $additional_params);

        $url .= '?';

        foreach ($params as $option=>$value) {
            if ($option == 'format'
            && $value = 'html') {
                continue;
            }
            if (!empty($value)) {
                $url .= "&$option=$value";
            }
        }
        $url = str_replace('?&', '?', $url);
        return trim($url, '?;=');
    }

    /**
     * Set the public properties for an object with the values in an associative array
     *
     * @param mixed &$object The object to set, usually a UNL_Procurement_Record
     * @param array $values  Associtive array of key=>value
     * @throws Exception
     *
     * @return void
     */
    public static function setObjectFromArray(&$object, $values)
    {
        if (!isset($object)) {
            throw new Exception('No object passed!');
        }
        foreach (get_object_vars($object) as $key=>$default_value) {
            if (isset($values[$key]) && (!is_null($values[$key]))) {
                $object->$key = $values[$key];
            }
        }
    }

    public static function isMobileClient($options = array())
    {
        if (!isset($_SERVER['HTTP_ACCEPT'], $_SERVER['HTTP_USER_AGENT'])) {
            // We have no vars to check
            return false;
        }

        if (isset($_COOKIE['wdn_mobile'])
            && $_COOKIE['wdn_mobile'] == 'no') {
            // The user has a cookie set, requesting no mobile views
            return false;
        }

        if ( // Check the http_accept and user agent and see
            preg_match('/text\/vnd\.wap\.wml|application\/vnd\.wap\.xhtml\+xml/i', $_SERVER['HTTP_ACCEPT'])
                ||
            (preg_match('/'.
               'sony|symbian|nokia|samsung|mobile|windows ce|epoc|opera mini|' .
               'nitro|j2me|midp-|cldc-|netfront|mot|up\.browser|up\.link|audiovox|' .
               'blackberry|ericsson,|panasonic|philips|sanyo|sharp|sie-|' .
               'portalmmm|blazer|avantgo|danger|palm|series60|palmsource|pocketpc|' .
               'smartphone|rover|ipaq|au-mic|alcatel|ericy|vodafone\/|wap1\.|wap2\.|iPhone|Android' .
               '/i', $_SERVER['HTTP_USER_AGENT'])
           ) && !preg_match('/ipad/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        return false;
    }

    public static function getDataDir()
    {
        return dirname(dirname(dirname(dirname(__FILE__)))).'/data';
    }

    public static function getFileRoot()
    {
        return dirname(dirname(dirname(dirname(__FILE__))));
    }
}