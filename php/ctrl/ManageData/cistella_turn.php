<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table() {
        return 'cistella_turn';
    }
        public function title(){
        return "Torns";
    }
    public function col_sort() {
        return "['date_turn','desc']";
    }
    protected function form_fields() {
        return "[{
                name:'id',
                editable:false
            }, {
                name:'date_turn', label:'Data torn',
                width:'120', align:'center',
                editrules:{date:true, required:true}
             }, {
                // Requetit ja que el desplegable no mostra el valor seleccionar si s'usa formatter:'select'
                name:'uf_name', label:'Unitat familiar',
                width:'350',
                editable:false
            }, {
                name:'uf_id', label:'Unitat familiar',
                // width:'350',
                editrules: {required:true,edithidden:true,searchhidden:true},
                edittype: 'select', //formatter:'select',
                editoptions: {
                    dataUrl:'php/ctrl/ManageData_select.php?table=aixada_uf_active'
                }   
        }]";
    }
    protected function sql() {
        // Requetit ja que el desplegable no mostra el valor seleccionar si s'usa formatter:'select'
        return
            "select
                t01.id, t01.uf_id, t01.date_turn, t01.ts,
                uf.name as uf_name
            from cistella_turn t01
            left join aixada_uf uf on t01.uf_id=uf.id";
    }
}
?>
