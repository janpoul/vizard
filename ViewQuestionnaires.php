<?php

namespace LPS\Models;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 * Опросы на сайте
 *
 * @author janpoul
 */

class ViewQuestionnaires {
    const TABLE = 'site_questionnaires';
    const TABLE_SAVE = 'questionnaires_answers';

    private static $registry = array();
    private static $load_fields = array('id', 'questionnaire_key', 'question', 'note', 'multi_answer', 'answers', 'position');

    private $questionnaire_key;
    private $questions = array();

    /**
     * @param type $key
     */
    private function __construct($key){
        $this->questionnaire_key = $key;
        $this->init();
    }

    /**
     *
     * @param string $key
     * @return Questions
     */
    public static function getByKey($key){

        if (empty(self::$registry[$key])){
            self::$registry[$key] = new self($key);
        }
        return self::$registry[$key];
    }

    private function init(){

        $db = \App\Builder::getInstance()->getDB();
        $quest_list = $db->query('SELECT `' . implode('`, `', self::$load_fields) . '` FROM `' . self::TABLE . '` WHERE `questionnaire_key` = ?s ORDER BY RAND()', $this->questionnaire_key)->select('id');
        $this->questions = array();
        foreach($quest_list as $id => $quest){
            $quest['answers'] = json_decode($quest['answers'], TRUE);
            $quest['answer'] = reset($quest['answers']);
            $this->questions[$id] = $quest;
        }
    }

    public function getQuestions(){
        return $this->questions;
    }

    public function getRandomQuestion(){

        $question = \App\Builder::getInstance()->getDB()->query('SELECT `' . implode('`, `', self::$load_fields) . '` FROM `' . self::TABLE . '` WHERE `questionnaire_key` = ?s ORDER BY RAND() LIMIT 1', $this->questionnaire_key)->getRow();
        if (!empty($question) && !empty($question['answers'])){
            $question['answers'] = json_decode($question['answers'], TRUE);
            $question['answer'] = reset($question['answers']);
        }
        return $question;
    }

    /**
     * Создание вопроса
     * @param array $data keys question multi_answer answers
     * @return int
     */
    public function addQuestion($data){

        $db = \App\Builder::getInstance()->getDB();
        $position = $db->query('SELECT MAX(`position`) FROM `' . self::TABLE . '` WHERE `questionnaire_key` = ?s', $this->questionnaire_key)->getCell() + 1;
        $allow_fields = array('question', 'multi_answer', 'answers');
        foreach($data as $key => $field){
            if (!in_array($key, $allow_fields)){
                unset($data[$key]);
            }
        }
        $data['questionnaire_key'] = $this->questionnaire_key;
        $data['position'] = $position;
        $data['answers'] = json_encode($data['answers']);
        $data['note'] = '';
        $id = $db->query('INSERT INTO `' . self::TABLE . '` SET ?a', $data);
        $data['answers'] = json_decode($data['answers'], TRUE);
        $data['id'] = $id;
        $this->questions[$id] = $data;
        return $this->questions[$id];
    }

    /**
     * Удаление вопроса
     * @param int $id id вопроса
     * @return boolean
     */
    public function deleteQuestion($id){

        if (empty($this->questions[$id])){
            return FALSE;
        }
        \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . self::TABLE . '` WHERE `id` = ?d', $id);
        unset($this->questions[$id]);
        return TRUE;
    }

    /**
     * Обновление вопросов
     * @param array $questions
     * @return bool
     */
    public function editQuestions($questions){

        $allow_fields = array('question', 'note', 'multi_answer', 'answers');
        $db = \App\Builder::getInstance()->getDB();
        foreach($questions as $id => $quest){
            if (empty($this->questions[$id])){
                continue;
            }
            if (!empty($quest['answers'])){
                $quest['answers'] = json_encode($quest['answers']);
            }
            $db->query('UPDATE `' . self::TABLE . '` SET ?a WHERE `id` = ?d', $quest, $id);
        }
        return TRUE;
    }

    /**
     *
     * @param int $question_id
     * @param int $new_position
     */
    public function sort($question_id, $new_position){

        $db = \App\Builder::getInstance()->getDB();
        if (empty($this->questions[$question_id])){
            return FALSE;
        }
        $old_position = $this->questions[$question_id]['position'];
        if ($new_position < $old_position) {
            $db->query('
                    UPDATE `' . self::TABLE . '`
                    SET `position`=`position`+1
                    WHERE `position`>=?d AND `position`<?d', $new_position, $old_position
            );
        } else {
            $db->query('
                    UPDATE `' . self::TABLE . '`
                    SET `position`=`position`-1
                    WHERE `position`<=?d AND `position`>?d', $new_position, $old_position
            );
        }
        $db->query('UPDATE `' . self::TABLE . '` SET `position` = ?d WHERE `id` = ?d', $new_position, $question_id);
        $this->init();
        return true;
    }

    public static function addAnswers($data){

        if(!empty($data)) {
            $db = \App\Builder::getInstance()->getDB();
            $data['answers'] = json_encode($data['answers']);
            $db->query('INSERT INTO `' . self::TABLE_SAVE . '` SET ?a, `create` = NOW()', $data);
        }
    }

    /**
     * @throws |PHPExcel_IOFactory
     */
    public static function getResult()
    {

        $db = \App\Builder::getInstance()->getDB();     
        $question_rows = $db->query('SELECT * FROM `' . self::TABLE . '` WHERE 1')->select('id');

        //все ответы
        $rows = $db->query('SELECT * FROM `' . self::TABLE_SAVE . '` WHERE 1')->select('id');
        if (!empty($question_rows) && !empty($rows)) {
            foreach ($question_rows as $question_row) {
                foreach ($question_row as $key=>$field){
                    if ($key==='answers'){
                        $quest[$question_row['id']][$key]=(array) json_decode($field);
                    }else{
                        $quest[$question_row['id']][$key]=$field;
                    }
                }
            }
            $row_exel = 1;

            $xls = new Spreadsheet();

            $xls->setActiveSheetIndex(0);

            $sheet = $xls->getActiveSheet();

            foreach ($rows as $row) {
                if ($row['answers'] !== null) {
                    $questions =(array) json_decode($row['answers']);
                    foreach ($questions as $q_key=>$question) {
                        //$resul = 0;
                        $right=0;
                        $question_answer='';
                        if(is_array($question)){
                            //разбиваем верные ответы на массив на массив
                            $right_answer = explode(',',$quest[$q_key]['check']);
                            $right_count = count($right_answer);
                            foreach ($question as $question_var){
                                $question_answer .= $quest[$q_key]['answers'][$question_var].', ';
                                if(in_array($question_var,$right_answer)){
                                    $right++;
                                }
                            }

                            $resul=($right_count>0)? (1-($right_count-$right)/$right_count):0;
                        }else{
                            $right_answer = explode(',',$quest[$q_key]['check']);
                            $right_count = count($right_answer);
                            //$question_answer = self::magicFind($quest[$q_key]['answers'],$question);
                            $question_answer = $quest[$q_key]['answers'][$question];
                            if($right_count<1) {
                                $resul = ($question === $quest[$q_key]['check']) ? 1 : 0;
                            }else{

                                $resul =  (1-($right_count-$right)/$right_count);
                            }
                        }
                        $row_exel++;                       
                        self::write_cell(1, $row_exel, $row['email'], $sheet, $style = '');                     
                        self::write_cell(2, $row_exel, $row['create'], $sheet, $style = '');                       
                        self::write_cell(3, $row_exel, $quest[$q_key]['question'], $sheet, $style = '');                        
                        self::write_cell(4, $row_exel, $question_answer, $sheet, $style = '');                       
                        self::write_cell(5, $row_exel, $resul, $sheet, $style = '');
                    }
                }
            }

            $objWriter = new Xlsx($xls);

            $file_path = \LPS\Config::getRealDocumentRoot() . '/data/temp/';

            if (!file_exists($file_path)) {
                \LPS\Tools\FileSystem::makeDirs($file_path);              
            }

            $file_name = 'test_result'. '.xlsx';

            $objWriter->save($file_path . $file_name);

            return '/data/temp/' . $file_name;
        }else{
            return '/';
        }
    }

    private static function magicFind($arr,$fin_key){

        $result='';
        foreach ($arr as $key=>$ar){
            if($key===((string)$fin_key)){
                $result=$ar;
            }
        }
        return $result;
    }

    /**
     * Запись ячейки
     * @param $column
     * @param $row
     * @param $value
     * @param $sheet
     * @param string $style
     * @return array
     *
     */
    private static function write_cell($column, $row, $value, $sheet, $style = '')
    {

        $sheet->setCellValueByColumnAndRow(
            $column,
            $row+1,
            $value);
        if (is_array($style)) {
            $sheet->getStyleByColumnAndRow($column, $row+1)->applyFromArray($style);
        }
    }

}
