<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use \App\Models\Quiz;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {


    $skipQuestion = null;
    $skippedQuestions = [];
    $unasweredQuestion = null;
    $unasweredQuestions = [];
    $renderQuestion = null;
    $answeredQuestions = [];

    // If session have the user_id then get the questions
    $user_id = Session::get('user_id', null);

    // Get the result from the db
    $result = Result::where('user_id', $user_id)->first();

    $allAnswered = false;


    // If have the $result then get the question_ids
    if ($result) {

        // If no skipped question then all answered
        if ($result->skipped == 0) {
            $allAnswered = true;
        }
    } else if ($user_id) {

        $allAnswered = UserController::ifAllAnswered(false);

    }


    if ($user_id && !$allAnswered) {

        $questionsAll = UserController::getQuestionsList();
        $skippedQuestions = $questionsAll["skippedQuestions"];
        $unasweredQuestions = $questionsAll["unasweredQuestions"];
        $answeredQuestions = $questionsAll["answeredQuestions"];


        // If session have the user_id and have the skipped question and it's count is not zer then get the question
        if (count($skippedQuestions) > 0) {

            $question = Quiz::find(reset($skippedQuestions));

            $skipQuestion = view('questions.view.question', compact('question'))->render();
        }

        if (count($unasweredQuestions) > 0) {

            $question = Quiz::find(reset($unasweredQuestions));

            $unasweredQuestion = view('questions.view.question', compact('question'))->render();
        }
    }


    $renderQuestion = $skipQuestion ?? $unasweredQuestion;


    if ($allAnswered) {
        $correct_ans = $result->correct;
        $wrong_ans = $result->wrong;
        $skip_questions = $result->skipped;

        $renderQuestion = view('result', compact('correct_ans', 'wrong_ans', 'skip_questions'))->render();
    }



    return view('questions', compact('skippedQuestions', 'unasweredQuestions', 'answeredQuestions', 'renderQuestion', 'user_id', 'allAnswered'));
});


// logout to clear the all session data
Route::get('/logout', function () {
    Session::flush();
    return redirect('/');
})->name('logout');

// create user
Route::post('/user/create', 'App\Http\Controllers\UserController@create')->name('user.create');

// answer question
Route::post('/question/answer', 'App\Http\Controllers\UserController@answer')->name('question.answer');

// skip question
Route::post('/question/skip', 'App\Http\Controllers\UserController@skipAnswer')->name('question.skip');
