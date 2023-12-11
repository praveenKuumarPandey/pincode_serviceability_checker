<?php 

$pincode_array= [

"110092" => "Anand Vihar SO",
"110051" => "Azad Nagar SO East Delhi",
"110032" => "Babarpur SO North East Delhi",
"110090" => "Badarpur Khadar BO",
"110032" => "Balbir Nagar SO",
"110053" => "Bhajan Pura SO",
"110032" => "Bhola Nath Nagar SO",
"110053" => "Brahampuri SO",
"110091" => "Chilla BO",
"110094" => "Dayalpur BO",
"110095" => "Dilshad Garden SO",
"110032" => "Distt Court KKD SO",
"110031" => "Gandhi Nagar Bazar SO",
"110031" => "Gandhi Nagar SO East Delhi",
"110053" => "Garhi Mandu BO"

];



?>


@extends('templates.main')

@section('content')

<div class="heading">
    <h1> Pincode Checker</h1>
</div>

<div>

    <h2>Select Pincode</h2>


    {{-- <select name="pincodeList" id="pincodeList">
        <?php
                    foreach($pincode_array as $pincode => $location){
                        ?>
        <option value="<?php echo $pincode ?>">
            <?php echo $location; ?>
        </option>
        <?php
                    }
                ?>
    </select> --}}
    <input type="text" name="pincode_text" id="pincode_text">
    <input type="button" name="pincode_checkBtn" id="pincode_checkBtn" value="Check">
</div>

<div id="resultMessage">
    <div id="error"></div>
    <div class="ajax-loader">
        <img src="{{url('/images/loader.jpg')}}" alt="Image" />
    </div>


    <div id="tat_html"></div>
    <div id="cod_html"></div>


</div>
<div id="returnExchText"></div>
@endsection

@push('scripts')
<script>
    var input = document.getElementById("pincode_text");

input.addEventListener("keypress", function(event) {
  // If the user presses the "Enter" key on the keyboard
  if (event.key === "Enter") {
    // Cancel the default action, if needed
    event.preventDefault();
    // Trigger the button element with a click
    document.getElementById("pincode_checkBtn").click();
  }
});


        $(document).ready(function() {
    $(document).on('click', '#pincode_checkBtn', function() {
        
        var pincode =  $("#pincode_text").val();
        // var a = $(this).parent();
        console.log("pincode is : ");
        console.log(pincode);
        // return false;
        // return false;
        // console.log(a);
        // console.log(pincode);
        var pat1=/^\d{6}$/;

        if(!pat1.test(pincode)){
// alert("Pin code should be 6 digits");
$('#tat_html').html('<div>Invalid Pin code <br/> Pin code should be 6 digits</div>');
                $('#tat_html').addClass("red-text");
                        $('#tat_html').removeClass("green-text");
                  
                    $('#cod_html').html("");
                    $('#returnExchText').html("");
$("#pincode_text").focus();
return false;
}else{
    // $('#error').html(""); 
}




        var op = "";

        $.ajax({
            type: 'get',
            url: 'getDeliveryTatNStatus',
            data: { 'pincode': pincode },
            dataType: 'json',      //return data will be json
            beforeSend: function() {
        // setting a timeout
        $('.ajax-loader').css("visibility", "visible");
            },
            success: function(data) {
                // console.log("price");
                console.log(data);
                // var data = JSON.parse(data);
                // console.log(data.data[0].pincode);

                if(data.status == true){

                    var delivery = data.data[0].delivery;
                    var tat = data.data[0].tat;
                    var cod = data.data[0].cod;
                    var pincode = data.data[0].pincode;
                    var htmlElement = data.data[0].html;
                    var codhtml = data.data[0].cod_html;
                    var returnExchText = data.data[0].returnExchText;
// console.log(data.data[0].tat);
// console.log(data.data[0].html);
// console.log(typeof codhtml);
// console.log(codhtml);
// console.log(codhtml.indexOf("<p"));
// console.log(codhtml.indexOf("</p>"));
// console.log(codhtml.substring(codhtml.indexOf("<p"),codhtml.indexOf("</p>")));

// console.log(codhtml.slice(codhtml.indexOf("<p>")));
    // console.log(typeof codhtml);
// console.log(codhtml.substring);
                    // var deliveryAvailable = "";
                    // var codAvailable = "";
                    // var tatAvailable = "";
                    var resultMessage = "";
                    var cod_available = "";
                    if(data.data[0].delivery == 1){
                        resultMessage = "Delivery to this pincode is available TAT is arround "+ tat + "days.";
                    }else{
                        resultMessage = "Delivery to this pincode is not available.";
                    }


                    if(data.data[0].cod == 1){
                        cod_available = "Cod option is Available";
                    }else{
                        cod_available = "Cod option is not Available";
                    }

                    var finalResultMessage = resultMessage +"\n"+ cod_available;
                    // $('#messageforTat').text(finalResultMessage);
                    $('#tat_html').html(htmlElement);
                    // console.log(htmlElement.indexOf("and-cancel"));
                    // console.log(htmlElement.indexOf("and-check"));
                    if(htmlElement.indexOf("and-cancel") !== -1){
                        $('#tat_html').addClass("red-text");
                        $('#tat_html').removeClass("green-text");
                    }
                    if(htmlElement.indexOf("and-check") !== -1){
                        $('#tat_html').addClass("green-text");
                        $('#tat_html').removeClass("red-text");
                    }
                    $('#cod_html').html(codhtml);
                    // console.log(codhtml);
                    // console.log(codhtml.indexOf("and-check"));
                    if(codhtml.indexOf("and-cancel") !== -1){
                        $('#cod_html').addClass("red-text");
                        $('#cod_html').removeClass("green-text");
                    }
                    if(codhtml.indexOf("and-check") !== -1){
                        console.log("in cod and-check");
                        $('#cod_html').addClass("green-text");
                        $('#cod_html').removeClass("red-text");
                    }
                    $('#returnExchText').html(returnExchText);
                    // $("" + htmlElement).insertAfter( "#messageforTat" );
                    // $("" + codhtml).insertAfter( "#messageforTat" );


                    // a.find('.aircraft_id').val(data.aircraft_id); 
                    // do you want to display id or registration name?
                }else{
                    var htmlElement = data.data.html;
//                   console.log(htmlElement);
                    $('#tat_html').html(htmlElement);
                    // console.log(htmlElement.indexOf("and-cancel"));
                    if(htmlElement.indexOf("and-cancel") !== -1){
                        $('#tat_html').addClass("red-text");
                        $('#tat_html').removeClass("green-text");
                    }
                    if(htmlElement.indexOf("and-check") !== -1){
                        $('#tat_html').addClass("green-text");
                        $('#tat_html').removeClass("red-text");
                    }
                    // console.log(htmlElement.indexOf("and-check"));
                    $('#cod_html').html("");
                    $('#returnExchText').html("");
                }
                },
                complete: function(){
    $('.ajax-loader').css("visibility", "hidden");
  },
                error:function(){
                    $('#tat_html').html('<div>Invalid Pincode</div>');
                    
                    $('#tat_html').addClass("red-text");
                    $('#tat_html').removeClass("green-text");

                    $('#cod_html').html("");
                    $('#returnExchText').html("");
            }
             
        });
    });
});
</script>

@endpush