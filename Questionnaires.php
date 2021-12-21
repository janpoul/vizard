<?php
/**
 * Created by PhpStorm.
 * User: janpoul
 * Date: 09.09.19
 * Time: 13:44
 */

namespace Controllers\Site;

use \LPS\Models\ViewQuestionnaires;
use \LPS\Models\SiteQuestionnaires;
use \LPS\Models\Logger;


class Questionnaires extends \LPS\AdminController
{

    const SINGLE_QUESTIONNAIRE = true;
    const DEFAULT_QUESTIONNAIRE_KEY = 'salessupport';

    public function index()
    {
         $this->getAns()->add('questions', ViewQuestionnaires::getByKey('salessupport')->getQuestions());
    }
    
    /**
    * Отправляем ответы
    */
    public function sendAnswers()
    {
        $fields = $this->request->query->all();
        $email = $this->account->getUser()->getEmail();
        if (array_key_exists('question', $fields)) {
            $answers = $fields['question'];
        } else {
            $answers = null;
        }
        
        if (!empty($email)) {
            ViewQuestionnaires::addAnswers(['email' => $email, 'answers' => $answers]);
        }
        
    }
    
    /**
    * Получаем ответы
    */
    public function getAnswers()
    {
        $this->getAns()->add('link', ViewQuestionnaires::getResult());
    }

    /**
     * Редактирование опроса
     */
    public function editQuestionnaire(){
    
        $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);
        $id = $this->request->request->get('id');
        if(!empty($id)) {
        
            $question = $this->request->request->get('question');
            $answers = $this->request->request->get('answer');
            $checks = $this->request->request->get('check', array());
            $multi_answer = $this->request->request->get('multi_answer', array());
            $notes = $this->request->request->get('note', array());

            $data[$id] = array(
                'question'     => $question,
                'answers'      => $answers,
                'check'        => !empty($checks) ? $checks:'',
                'note'         => !empty($notes) ? $notes : '',
                'multi_answer' => !empty($multi_answer) ? 1 : 0
            );
            $questionnaire->editQuestions($data);
        }
        $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);

        $this->getAns()
            ->add('questions', $questionnaire->getQuestions())
            ->add('title', 'Опрос для работников интернет-магазина');
    }

    /**
    * Добавлеия вопроса
    */
    public function addQuestions(){
    
        $ans = $this->setJsonAns();
        $question = $this->request->request->get('question');
        if (empty($question)){
            $ans->setEmptyContent()->addError(['key' =>'question','error'=>'empty']);
        } else {
            $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);
            $question = $questionnaire->addQuestion(array(
                'question' => $question,
                'answers' => array(
                    1 => 'Ответ 1',
                    2 => 'Ответ 2'
                ),
                'multi_answer' => 0
            ));
            
            Logger::add(array(
                'type' => Logger::LOG_TYPE_CREATE,
                'entity_type' => 'questionnaire',
                'entity_id' => $question['id'],
                'additional_data' => array('title' => $question['question'])
            ));
            $ans->add('question', $question);
        }
    }
    
    /**
    * Удаление вопроса
    */
    public function deleteQuestion(){
    
        $this->setJsonAns();
        $ids = $this->request->request->get('id', array());

        $this->getAns()->setTemplate('Controllers/Site/Questionnaires/questionList.tpl');
        $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);
        foreach ($ids as $id) {
            $questionnaire->deleteQuestion($id);
        }
        $this->getAns()->add('questions', $questionnaire->getQuestions());
    }

    public function editPopup(){

        $this->setJsonAns();
        $id = $this->request->request->get('rule_id');
        if(!empty($id)) {
        $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);
        $questions = $questionnaire->getQuestions();

            $this->getAns()->add('entity_type', 'questionnaire')
            ->add('entity_id' , $id)
                ->add('question', $questions[$id]);
            $this->getAns()->add('id', $id);
        }

        $this->getAns()->setTemplate('Controllers/Site/Questionnaires/editPopup.tpl');
    }

    /**
    * Редактирование впросов
    */
    public function editQuestion(){
    
        $ans = $this->setJsonAns()->setEmptyContent();
        $id = $this->request->request->get('id');
        if (empty($id)){

            $ans->addError(array(
                'key' => 'id',
                'error' => 'empty'
            ));
        } else {
            $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);
            $questions = $questionnaire->getQuestions();
            $questions_to_del = $questions[$id];

           Logger::add(array(
                'type' => Logger::LOG_TYPE_CREATE,
                'entity_type' => 'questionnaire',
                'entity_id' => $id,
                'additional_data' => array('title' => $questions_to_del['question'])
            ));
            $ans->addData('status', 'ok');
        }
    }

    /**
    * Список вопросов
    */
    public function questionList(){
    
        $questionnaire = new SiteQuestionnaires(self::DEFAULT_QUESTIONNAIRE_KEY);
        $question_id = $this->request->request->get('question_id');
        $new_position = $this->request->request->get('position');
        if ($question_id !== NULL && $new_position !== NULL){
            $questionnaire->sort($question_id, $new_position);
        }
        $this->getAns()->add('questions', $questionnaire->getQuestions());
    }
}
