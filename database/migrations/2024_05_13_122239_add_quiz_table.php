<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // Create quizes table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('option1');
            $table->string('option2');
            $table->string('option3');
            $table->string('option4');
            $table->integer('answer');
            
            $table->timestamps();
        });


        // Ajax related questions
        DB::table('quizzes')->insert([
            [
                'question' => 'What is AJAX?',
                'option1' => 'Asynchronous JavaScript and XML',
                'option2' => 'Asynchronous JavaScript and XHTML',
                'option3' => 'Asynchronous JavaScript and XAML',
                'option4' => 'Asynchronous JavaScript and XUL',
                'answer' => 1,
            ],
            [
                'question' => 'What is the full form of AJAX?',
                'option1' => 'Asynchronous JavaScript and XML',
                'option2' => 'Asynchronous JavaScript and XHTML',
                'option3' => 'Asynchronous JavaScript and XAML',
                'option4' => 'Asynchronous JavaScript and XUL',
                'answer' => 1,
            ],
            [
                'question' => 'What is the use of AJAX?',
                'option1' => 'Update a web page without reloading the page',
                'option2' => 'Request data from a server - after the page has loaded',
                'option3' => 'Receive data from a server - after the page has loaded',
                'option4' => 'All of the above',
                'answer' => 4,
            ],
            [
                'question' => 'What is the use of XMLHttpRequest object in AJAX?',
                'option1' => 'To update a web page',
                'option2' => 'To request data from a server',
                'option3' => 'To receive data from a server',
                'option4' => 'To send data to a server',
                'answer' => 2,
            ],
            [
                'question' => 'What is the correct syntax for creating a new XMLHttpRequest object?',
                'option1' => 'var xmlhttp = new XMLHttpRequest();',
                'option2' => 'var xmlhttp = new XMLHttpRequestObject();',
                'option3' => 'var xmlhttp = new Microsoft.XMLHTTP();',
                'option4' => 'var xmlhttp = new Msxml2.XMLHTTP();',
                'answer' => 1,
            ],
        ]);
        

        DB::table('quizzes')->insert([
            [
                'question' => 'What does HTML stand for?',
                'option1' => 'Hyper Text Markup Language',
                'option2' => 'Hyperlinks and Text Markup Language',
                'option3' => 'Home Tool Markup Language',
                'option4' => 'Hyper Tool Markup Language',
                'answer' => 1,
            ],
            [
                'question' => 'Who is making the Web standards?',
                'option1' => 'The World Wide Web Consortium',
                'option2' => 'Google',
                'option3' => 'Microsoft',
                'option4' => 'Mozilla',
                'answer' => 1,
            ],
            [
                'question' => 'Choose the correct HTML element for the largest heading:',
                'option1' => '<h1>',
                'option2' => '<heading>',
                'option3' => '<h6>',
                'option4' => '<head>',
                'answer' => 1,
            ],
            [
                'question' => 'What is the correct HTML for creating a hyperlink?',
                'option1' => '<a>http://www.w3schools.com</a>',
                'option2' => '<a url="http://www.w3schools.com">W3Schools.com</a>',
                'option3' => '<a href="http://www.w3schools.com">W3Schools</a>',
                'option4' => '<a name="http://www.w3schools.com">W3Schools.com</a>',
                'answer' => 3,
            ],
            [
                'question' => 'Which character is used to indicate an end tag?',
                'option1' => '^',
                'option2' => '*',
                'option3' => '<',
                'option4' => '/',
                'answer' => 4,
            ],
        ]);



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
