<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signal Lights</title>
    <style>
        .circle {
            width: 50px;
            height: 50px;
            background-color: red;
            border-radius: 50%;
            display: inline-block;
        }
        button {
            margin-top: 10px;
            margin-right: 10px;
        }
        .error {
            color: red;
        }
        .signal-box{
            display: inline-block;
            width:70px;
            border:1px solid black;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Signal Lights</h1>
        
        <div style="margin-bottom: 10px;">
        @foreach(range(1, 4) as $index)
            <div class="signal{{ $index }} signal-box">
                Signal {{ $index }}
                <div class="circle circle{{ $index }}"></div><br>
            </div>
        @endforeach
        </div>

        <div class="signals" style="margin-bottom: 10px;">
            Enter the Signals sequence : 
            @foreach(range(1, 4) as $index)
                <div style="display: inline-block;width:70px;">
                    <input type="number" name="signal{{ $index }}" placeholder="" min="1" max="4" step="1" style="display: inline-block;width:50px;">
                </div>
            @endforeach
        </div>
        
        <!-- Two input boxes for time interval -->
        <div class="time-interval">
            <div>
                <label for="green-interval">Green Interval:</label>
                <input type="number" id="green-interval" name="green_interval" placeholder="enter time">
                (enter time in seconds)
            </div>
            <br>
            <div>
                <label for="yellow-interval">Yellow Interval:</label>
                <input type="number" id="yellow-interval" name="yellow_interval" placeholder="enter time">
                (enter time in seconds)
            </div>
            <br>
        </div>
        <!-- Start and stop buttons -->
        <div class="controls">
            <button id="start-btn">Start</button>
            <button id="stop-btn">Stop</button>
            <button id="reload-btn">Reload</button>
        </div>
        <div class="error-message" style="margin-top: 10px; display: none;"></div>
    </div>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        var currentIndex = 0;
        var sequence = [];
        var isRunning = false;
        var timeoutIds = [];
        var inputValues = [];

        // Function to check if an array has duplicates
        function hasDuplicates(array) {
            return (new Set(array)).size !== array.length;
        }

        // Function to validate input fields
        function validateInputs() {
            // Validate input fields
            var isValid = true;
            var isValid1 = true;

            $(".signals input[type='number'], #green-interval, #yellow-interval").each(function() {
                if ($(this).val() === "") {
                    isValid = false;
                    return false; // Exit the loop early
                }
            });

            if (!isValid) {
                $(".error-message").text("Please fill in all input fields.").show();
                return false;
            } else {
                $(".signals input[type='number']").each(function() {
                    var val = parseInt($(this).val());
                    if (isNaN(val) || val < 1 || val > 4) {
                        isValid1 = false;
                        return false; // Exit the loop early
                    }
                    inputValues.push(val);
                });

                if (!isValid1) {
                    $(".error-message").text("Please fill in all Sequence input fields with numbers between 1 and 4.").show();
                    return false;
                } else if (hasDuplicates(inputValues)) {
                    $(".error-message").text("Sequence input fields numbers should not be repeated.").show();
                    inputValues = [];
                    return false;
                } else {
                    $(".error-message").hide();
                    // Disable all input fields
                    $("input").prop("disabled", true);
                    return true;
                }
            }
        }

        // Function to start changing signals
        function startSignals() {
            var currentSignal = sequence[currentIndex];

            // Reset all circles
            $(".circle").css("background-color", "red");

            // Set current circle to green
            $(currentSignal.signal).css("background-color", "green");

            // Schedule the next signal change
            timeoutIds.push(setTimeout(function() {
                // Set current circle to yellow after green interval
                $(currentSignal.signal).css("background-color", "yellow");

                // Schedule the next signal change
                timeoutIds.push(setTimeout(function() {
                    // Set current circle to red after yellow interval
                    $(currentSignal.signal).css("background-color", "red");

                    // Increment currentIndex or reset to 0 if it reaches the end of the sequence
                    currentIndex = (currentIndex + 1) % sequence.length;

                    // Call startSignals function recursively for the next signal
                    if (isRunning) {
                        startSignals();
                    }
                }, currentSignal.yellowInterval));
            }, currentSignal.greenInterval));
        }

        // Start button click event
        $("#start-btn").click(function() {
            // If already running or inputs are invalid, return
            if (isRunning || !validateInputs()) return;

            // Reset currentIndex, sequence, and inputValues
            currentIndex = 0;
            sequence = [];
            inputValues = [];

            // Enable stop button and disable start button
            $(this).prop("disabled", true);
            $("#stop-btn").prop("disabled", false);

            // Get sequence input
            $(".signals input[type='number']").each(function() {
                sequence.push({
                    signal: ".circle" + $(this).val(),
                    greenInterval: $("#green-interval").val() * 1000, // Convert seconds to milliseconds
                    yellowInterval: $("#yellow-interval").val() * 1000, // Convert seconds to milliseconds
                });
            });

            // Start changing signals
            isRunning = true;
            startSignals();
        });

        // Stop button click event
        $("#stop-btn").click(function() {
            // If not running, return
            if (!isRunning) return;

            // Stop changing signals
            isRunning = false;
            // Clear all timeout events
            timeoutIds.forEach(function(timeoutId) {
                clearTimeout(timeoutId);
            });
            // Reset all circles to red
            $(".circle").css("background-color", "red");
            // Disable stop button and enable start button
            $(this).prop("disabled", true);
            $("#start-btn").prop("disabled", false);
        });

        // Initially disable the stop button
        $("#stop-btn").prop("disabled", true);

        // Reload button click event
        $("#reload-btn").click(function() {
            location.reload();
        });
    });
</script>


</body>
</html>
