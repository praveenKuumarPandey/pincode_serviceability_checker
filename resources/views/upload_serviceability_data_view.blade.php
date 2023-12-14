@extends('templates.main')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12 align-self-center">

        <h1>
            Upload Serviceability Details
        </h1>

    </div>
</div>
<div class="row" id="content">
    <div class="col-md-5">
        <h2>Instruction</h2>
        <div class="m-2">
            please use .xlxs, csv,files only
        </div>
        <div class="m-2">
            <a href="{{route('pincode.serviceabilityUploadSampleDownload')}}">Click Here to Download Sample</a>
        </div>
    </div>
    <div class="col-md-5">
        <h3>Select Serviceability Details Sheet </h3>
        <form id="product-attribute-upload-form" method="POST" action="javascript:void(0)" accept-charset="utf-8"
            enctype="multipart/form-data">
            <div class="input-group">
                <input type="file" class="form-control" name="productUploadfile" id="productUploadfile"
                    placeholder="Choose Product Upload Shee" aria-describedby="submit" aria-label="Upload" required>
                <button class="btn btn-outline-secondary" type="submit" id="submit">Upload</button>
            </div>

        </form>
    </div>

</div>

<div class="row justify-content-center">
    <div id="loader_image_upload" style="display: none" class="spinner-border text-primary m-5 p-2" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>


<div id="errors_listing" class="m-2 p-2">

</div>
{{-- <div id="list_errors" class="m-2 p-2 alert alert-danger" role="alert">
</div> --}}
@endsection

@push('scripts')

<script>
    $(document).ready(function (){

$.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

$('#product-attribute-upload-form').submit(function(e) {
e.preventDefault();
console.log('product attribute upload');
$('#loader_image_upload').show();
let form_data1 = new FormData(this);
let productAttributeUploadFile = $('#productUploadfile')[0];
console.dir(productAttributeUploadFile.files[0]);

form_data1.append("uploadfile", true);
form_data1.append("productAttributeUploadfile", productAttributeUploadFile.files[0]);

// let file = new formdata();
$.ajax({
type:'POST',
url: "{{ url('uploadPincodeServiceabilityDetailsSheet')}}",
data: form_data1,
catch:false,
processData:false,
contentType:false,
dataType: 'json',
success: (data) => {
    // console.log(data);
    $('#loader_image_upload').hide();


    console.log(data);
    console.log(data[0].error.is_any_error);
    if(data[0].error.is_any_error === true){
        $('#errors_listing').html("<div class='alert alert-danger' role='alert'>"+ "You have Issue in uploded Files : "+"</div>");
        let errorTracker = data[0].error.error_tracker;
        errorTracker.forEach(error_entry => {
            console.log('hi inside error_entry', error_entry);
                if(error_entry.error === true){
                    console.log('hi inside if error_entry.error row is true ', error_entry.row);
                    const newDiv = document.createElement("p");
                    newDiv.classList.add("alert", "alert-danger");
                    const newContent = document.createTextNode("You have Issue in uploded Files near row no: "+ (error_entry.row + 2));
                    newDiv.appendChild(newContent);                    
                    let errorlistings = document.querySelector("#errors_listing");
                    errorlistings.appendChild(newDiv);
                    console.dir(newDiv);
                }                
        });
        
    }else{
        $('#errors_listing').html("<div class='alert alert-success' role='success'>"+ "Successfully Uploaded given File "+"</div>")
    }
   
},error:function(data){
    $('#loader_image_upload').hide();
    $('#errors_listing').html("<div class='alert alert-warning' role='alert'>Unable to Uploaded Given File<div>");

}
});


});


});

</script>


@endpush