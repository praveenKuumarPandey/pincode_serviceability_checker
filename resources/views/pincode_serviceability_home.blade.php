@extends('templates.main')

@section('content')

<div class="heading">
    <h1> Pincode Checker</h1>
</div>
<div class="col">

</div>
<div>

    <h2>Enter Pincode</h2>

    <input type="text" name="pincode_text" id="pincode_text">
    <input type="button" name="pincode_checkBtn" id="pincode_checkBtn" value="Check">

</div>

{{-- <div class="ajax-loader">
    <img src="{{url('/images/pinloader.gif')}}" alt="Image" />
</div> --}}

<div class="row justify-content-center">
    <div id="loader_image_upload" style="display: none" class="spinner-border text-primary m-2 p-2" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div id="resultMessage">
    <div id="error"></div>



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
        $('#loader_image_upload').show();
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
    // $('.ajax-loader').css("visibility", "hidden");
    $('#loader_image_upload').hide();
  },
                error:function(){
                    $('#tat_html').html('<div>Invalid Pincode</div>');
                    
                    $('#tat_html').addClass("red-text");
                    $('#tat_html').removeClass("green-text");

                    $('#cod_html').html("");
                    $('#returnExchText').html("");
                    $('#loader_image_upload').hide();

            }
             
        });
    });
});
</script>

@endpush