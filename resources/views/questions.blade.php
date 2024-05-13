@extends('layouts.main')

@section('title', 'Quiz Questions')


@section('content')
<h1>Quiz Exam</h1>

<div class="container m-5 mx-auto p-4 text-center" style="max-width: 700px;">


    @if(!$renderQuestion)
    <div class="card" id="register">
        <div class="card-body">
            <h5 class="card-title">Enter name to start the quiz</h5>
            <form id="login" action="{{ route('user.create') }}" method="post">
                @csrf
                <div class="form-group">

                    <input type="text" name="name" class="form-control" placeholder="Your name" required>
                </div>
                <button type="submit" class="btn btn-primary mt-4">Start Quiz</button>
            </form>
        </div>
    </div>
    @endif



    <div id="questions_w" @if (!$renderQuestion) style="display:none" @endif>

        @if ($renderQuestion and !$allAnswered)

        <h4>Based on your previous session you have the following questions to answer</h4>
        <div class="session_info">
            <p class="mb-0"><strong>Answered: {{count($answeredQuestions)}}</strong></p>
            <p class="mb-0"><strong>Skipped: {{count($skippedQuestions)}}</strong></p>
            <p><strong>Unanswered: {{count($unasweredQuestions)}}</strong></p>
        </div>


        @endif

        @php
        if ($renderQuestion) {
        echo $renderQuestion;
        }
        @endphp


    </div>


   @if ($user_id)
       
    <div class="mt-4">
        <form action="{{ route('logout') }}" method="get">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>

    @endif


</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {

        // on login form send the request to the server using the ajax
        $('#login').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var type = form.attr('method');
            var data = form.serialize();

          
            $('.session_info').hide();

            $.ajax({
                url: url,
                data: data,
                type: type,
                dataType: 'json',
                success: function(response) {

                    if (response.isSuccess) {
                        $('#register').hide();
                        $('#questions_w').show();
                        $('#questions_w').html(response.data);
                    } else {
                        alert(response.message);
                    }

                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });


        // On dynamically created #nxt_btn send the request to the server
        $(document).on('click', '#nxt_btn', function() {
            var qid = $(this).data('qid');
            var url = "{{ route('question.answer') }}";
            var type = 'post';

            $('.session_info').hide();

            // get the answer radio value
            var answer = $('input[name="answer"]:checked').val();

            if (!answer) {
                alert('Please select an answer');
                return;
            }

            // disable the button to prevent multiple clicks and change text
            $(this).attr('disabled', 'disabled').text('Loading...');

            var resetBtn = function() {
                $('#nxt_btn').removeAttr('disabled').text('Next');
            }

            $.ajax({
                url: url,
                data: {
                    question_id: qid,
                    answer: answer,
                    user_id: "{{$user_id}}",
                    _token: "{{ csrf_token() }}"
                },
                type: type,
                dataType: 'json',
                success: function(response) {

                    if (response.isSuccess) {
                        $('#questions_w').html(response.data);
                    } else {
                        resetBtn();
                        alert(response.message);
                    }

                },
                error: function(xhr, status, error) {
                    resetBtn();
                    alert(xhr.responseText);
                }
            });
        });


        // On dynamically created #skip_btn send the request to the server
        $(document).on('click', '#skip_btn', function() {
            var qid = $(this).data('qid');
            var url = "{{ route('question.skip') }}";
            var type = 'post';

            // disable the button to prevent multiple clicks and change text
            $(this).attr('disabled', 'disabled').text('Loading...');

            var resetBtn = function() {
                $('#skip_btn').removeAttr('disabled').text('Skip');
            }

            $.ajax({
                url: url,
                data: {
                    question_id: qid,
                    user_id: "{{$user_id}}",
                    _token: "{{ csrf_token() }}",
                    
                },
                type: type,
                dataType: 'json',
                success: function(response) {

                    if (response.isSuccess) {
                        $('#questions_w').html(response.data);
                    } else {
                        resetBtn();
                        alert(response.message);
                    }

                },
                error: function(xhr, status, error) {
                    resetBtn();
                    alert(xhr.responseText);
                }
            });
        });


    });
</script>

@endsection