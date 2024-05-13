<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizUser;
use App\Models\Result;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150|unique:users,name',
        ]);

       
        // if failed
        if ($validator->fails()) {
            return response()->error('Validation failed!', $validator->errors()->first());
        }
        

        try {

            $user = QuizUser::create([
                'name' => $request->name
            ]);


            $questions = Quiz::inRandomOrder()->get();

            Session::put('questions', $questions->pluck('id')->toArray());
            Session::put('user_id', $user->id);
            Session::put('skip_questions', []);
            Session::put('answered_ids', []);
            Session::put('answered', []);

            $question = Quiz::find(Session::get('questions')[0]);
            $questionRender = view('questions.view.question', compact('question'))->render();

            return response()->success('User created successfully!', $questionRender);

        } catch (QueryException $e) {
            
            if (strpos($e->getMessage(), 'duplicate key') !== false) {
                return response()->error('Failed to create user! Same name user already registered.');
            }

            return response()->error('Failed to create user!', $e->getMessage());

        } catch (\Exception $e) {

            return response()->error('Failed to create user!', $e->getMessage());
        }
        

    }


    public function skipAnswer (Request $request) {


        $validator = Validator::make($request->all(), [
            'question_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->error('Validation failed!', $validator->errors()->first());
        }

        $user_id = $request->user_id;

        // If id not available on the session
        if (!in_array($request->question_id, Session::get('questions'))) {
            return response()->error('Invalid question id!');
        } 

        // If id already answered
        if (in_array($request->question_id, Session::get('answered_ids'))) {
            return response()->error('Question already answered!');
        }

        // If id already skipped
        if (in_array($request->question_id, Session::get('skip_questions'))) {
            return response()->error('Question already skipped!');
        }


        try {

            $skipQuestions = Session::get('skip_questions');
            $skipQuestions[] = $request->question_id;

            Session::put('skip_questions', $skipQuestions);

           // questions data
              $questionsData = self::getQuestionsList();

            // If all questions answered
            if (count($questionsData['unasweredQuestions']) == 0) {
                Session::put('all_answered', true);
            }

            // If have next question
            if (count($questionsData['unasweredQuestions']) > 0) {
                $question = Quiz::find(reset($questionsData['unasweredQuestions']));
                $questionRender = view('questions.view.question', compact('question'))->render();
                return response()->success('Question skipped successfully!', $questionRender);
            }

            // We are here it means all answered
            self::saveResult();


            $correctWrong = self::getCorrectWrongAnsCount();

           
            $correct_ans = $correctWrong['correct'];
            $wrong_ans = $correctWrong['wrong'];
            $skip_questions = count(Session::get('skip_questions'));
            
            $resultView = view('result', compact('correct_ans', 'wrong_ans', 'skip_questions'))->render();

            // We are here it means all questions answered so return result
            return response()->success('All questions answered successfully!', $resultView);


        } catch (\Exception $e) {
            return response()->error('Failed to skip question!', $e->getMessage());
        }

    }


    public function answer (Request $request) {

        $validator = Validator::make($request->all(), [
            'question_id' => 'required|integer',
            'answer' => 'required|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->error('Validation failed!', $validator->errors()->first());
        }

        // If id not available on the session
        if (!in_array($request->question_id, Session::get('questions'))) {
            return response()->error('Invalid question id!');
        }

        // If id already answered
        if (in_array($request->question_id, Session::get('answered_ids'))) {
            return response()->error('Question already answered!');
        }

        $user_id = $request->user_id;



        try {

            $answeredIds = Session::get('answered_ids');
            $answeredIds[] = $request->question_id;

            Session::put('answered_ids', $answeredIds);

            $answered = Session::get('answered');
            $answered[$request->question_id] = $request->answer;

            Session::put('answered', $answered);

            // if the id is in the skipped questions then remove it
            $skipQuestions = Session::get('skip_questions');
            if (in_array($request->question_id, $skipQuestions)) {
                $skipQuestions = array_diff($skipQuestions, [$request->question_id]);
                Session::put('skip_questions', $skipQuestions);
            }

            // questions data
            $questionsData = self::getQuestionsList($user_id);

            // If all questions answered
            if (count($questionsData['unasweredQuestions']) == 0) {
                Session::put('all_answered', true);
            }

            // If have next question
            if (count($questionsData['unasweredQuestions']) > 0) {
                $question = Quiz::find(reset($questionsData['unasweredQuestions']));
                $questionRender = view('questions.view.question', compact('question'))->render();
                return response()->success('Question answered successfully!', $questionRender);
            }

            // We are here it means all answered
            self::saveResult();

            $correctWrong = self::getCorrectWrongAnsCount();

            $correct_ans = $correctWrong['correct'];
            $wrong_ans = $correctWrong['wrong'];
            $skip_questions = count(Session::get('skip_questions'));
            
            $resultView = view('result', compact('correct_ans', 'wrong_ans', 'skip_questions'))->render();

            // We are here it means all questions answered so return result
            return response()->success('All questions answered successfully!', $resultView);


        } catch (\Exception $e) {
            return response()->error('Failed to answer question!', $e->getMessage());
        }

    }


  
    public static function getQuestionsList($user_id = null) {

        $skippedQuestions = Session::get('skip_questions', []);
        $answeredIds = Session::get('answered_ids', []);
        $questionsIds = Session::get('questions', []);
        $unasweredQuestions = [];

        // Unaswered questions
        $unasweredQuestions = array_diff($questionsIds, $skippedQuestions,$answeredIds);


        // If user id is provded then it means the user refresh the page and we allow the user to continue the skip quiz
        if($user_id && count($unasweredQuestions) == 0) {
            $unasweredQuestions = $skippedQuestions;
        }

        return [
            'skippedQuestions' => $skippedQuestions,
            'unasweredQuestions' => $unasweredQuestions,
            'answeredQuestions' => $answeredIds,
        ];


    }


    public static function getCorrectWrongAnsCount() {

        $answered = Session::get('answered', []);
        $questions = Quiz::whereIn('id', Session::get('questions'))->get();
        $correct = 0;
        $wrong = 0;

        foreach ($questions as $question) {

            if (!isset($answered[$question->id])) {
                continue;
            }

            if ($answered[$question->id] == $question->answer) {
                $correct++;
            } else {
                $wrong++;
            }
        }

        return [
            'correct' => $correct,
            'wrong' => $wrong,
        ];

    }



    public static function saveResult() {

        $questionsData = self::getQuestionsList();
        $result = self::getCorrectWrongAnsCount();

        $user_id = Session::get('user_id');

        $resultRow = Result::where('user_id', $user_id)->first();

        if (!$resultRow) {
            $resultRow = new Result();
        }

        $resultRow->user_id = $user_id;
        $resultRow->skipped = count($questionsData['skippedQuestions']);
        $resultRow->correct = $result['correct'];
        $resultRow->wrong = $result['wrong'];

        // If already have then use same otherwise use the session data
        $resultRow->question_ids = $resultRow->question_ids ?? implode(',', Session::get('questions'));

        $resultRow->save();

        return $resultRow;

    }


    public static function ifAllAnswered($ignoreSkip = true) {
       
        // Get total questions from the session
        $questions = Session::get('questions', []);
        $answered = Session::get('answered_ids', []);
        $skipped = Session::get('skip_questions', []);

        $performedCount = count($answered);

        if ($ignoreSkip) {
            $performedCount += count($skipped);
        
        }

        // If all answered
        if (count($questions) == $performedCount ) {
            return true;
        }

        return false;

    }

  
}
