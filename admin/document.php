<?php include "navbar.php";
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
}

?>

<html lang="eng">
<head>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MUNICIPAL DOCUMENTS</title>

</head>
<body>

<div class="container">
    <div class="card" style="margin-top: 2em; margin-bottom: 2em">
        <div class="card-header">
            ADD DOCUMENTS
        </div>
        <div class="card-body">

            <form id="addDocument">
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <input type="file" name="FILE[]" id="files" multiple>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <span id="uploaded_files"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" id="uploadBtn" class="btn btn-outline-primary btn-lg btn-block"> Upload Files </button>
            </form>
        </div>
    </div>

</div>

<script>


    $("#addDocument").on("submit", function (e){
        e.preventDefault();
        let length = document.getElementById("files").files.length;
        let formData = new FormData(document.getElementById("addDocument"));
        formData.append("key", "addDocuments")
        if(length > 0){
            $.ajax({
                url: '../ajax.php',
                method: 'post',
                dataType: 'json',
                contentType: false,
                processData: false,
                data:formData,
                success: function (response) {
                    console.log(response)
                }
            })
        }
    })

    $("#files").on("change", function (e){
        let uploaded_names = ""
        $.each(this.files, function (key, value){
            uploaded_names += `${value.name} </br>`
        })
        $("#uploaded_files").html(uploaded_names)
    })

    function showModal(modal){
        switch (modal) {
            case "#ordinanceModal":
                fetchOrdinance()
                break;
        }
        $(modal).modal()
    }
</script>

</body>
</html>
