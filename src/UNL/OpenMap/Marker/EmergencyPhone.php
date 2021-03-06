<?php
class UNL_OpenMap_Marker_EmergencyPhone extends UNL_OpenMap_Marker
{
    public function __construct($options = array())
    {
        parent::__construct($options);
        if (!isset($options['title'])) {
            $phones = new UNL_OpenMap_MarkerList_EmergencyPhones($options);
            foreach ($phones[$options['code']] as $key=>$value) {
                $this->$key = $value;
            }
        }
        $this->info = new UNL_OpenMap_Marker_EmergencyPhone_Info($this->toArray() + array('name'=>$this->title));
    }
}