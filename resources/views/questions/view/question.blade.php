<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">{{$question["question"]}}</h5>
        <form method="POST">
            @csrf

            <div class="row">
                @foreach(range(1, 4) as $i)
                <div class="col-md-6"> <!-- Use col-md-6 for medium-sized screens and above -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" value="{{$i}}" required>
                            <label class="form-check-label" for="option{{$i}}">{{$question["option". $i]}}</label>
                        </div>
                    </div>
                </div>
                @if($i % 2 == 0)
                </div><div class="row"> <!-- Close the current row and start a new row after every two options -->
                @endif
                @endforeach
            </div>

            <div class="mb-3 mt-4">
                <button type="button" data-qid="{{$question['id']}}" class="btn btn-primary" id="nxt_btn">Next</button>
                <button type="button" data-qid="{{$question['id']}}" class="btn btn-secondary" id="skip_btn">Skip</button>
            </div>
        </form>
    </div>
</div>
