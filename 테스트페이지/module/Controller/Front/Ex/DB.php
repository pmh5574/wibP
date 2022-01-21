<?php
class MyComponent
{
    protected $db = null;

    /**
     * 생성자
     */
    public function __construct()
    {
        if(!is_onject($this->db)){
            $this->db = \App::load('DB');
        }
    }
        public function updateData($arrData)
        {
            $arrBind = $this ->db->get_binding(DBTableField::tableTestInfo(),$arrData,'update');
            $this->db->bind_param($arrBind['bind'], 'i', $ arrData ['sno']);
            $this->db->set_update_db('es_testTable', $arrBind['param'], 'sno=?',$arrBind['bind']);
        }

}