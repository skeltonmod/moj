<?php include "navbar.php";
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
}

?>

<html lang="eng">
<head>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MUNICIPAL MEDIAS</title>

</head>
<body>

<div class="container">
    <div class="modal fade" id="editAlbumModal" tabindex="-1" role="dialog" aria-labelledby="modalScroll" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="headerName"> Change Album Name </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAlbumForm">
                        <br>
                        <label>Album Information</label>
                        <div class="form-group">
                            <input type="text" class="form-control editable" name="id" id="id" placeholder="ID" readonly>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control editable" name="album" id="album" placeholder="Album Name">
                        </div>
                        <button type="button" id="savebtn" class="btn btn-outline-primary btn-lg btn-block"> Save Changes </button>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 2em; margin-bottom: 2em">
        <div class="card-header">
            Media
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="subjectTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="addTab" data-toggle="tab" role="tab" href="#add" aria-controls="add" aria-selected="true">
                        Add Media
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="manageTab" data-toggle="tab" href="#manage" role="tab" aria-controls="add" aria-selected="false">
                        Manage Media
                    </a>
                </li>
            </ul>
            <div class="tab-content" id="scheduleTabContent">
                <div class="tab-pane fade show active" id="add" role="tabpanel" aria-labelledby="addTab">
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

                        <button type="submit" id="uploadBtn" class="btn btn-outline-primary btn-lg btn-block"> Upload Medias </button>
                    </form>

                </div>
                <div class="tab-pane fade" id="manage" role="tabpanel" aria-labelledby="manageTab">
                    <div class="row">
                        <div class="col" style="margin-top: 1em; margin-bottom: 1em;">
                            Medias
                            <button class="btn btn-success float-right"  onclick="editMedia(null, 'all', 'show')" style="margin-left: 1em">Show All</button>

                            <button class="btn btn-danger float-right" onclick="editMedia(null, 'all', 'hide')" >Hide All</button>
                            <button class="btn btn-success float-right checked"  onclick="editMedia(null, 'checked', 'show')" style="margin-left: 1em; margin-right: 1em; display: none">Show Checked</button>
                            <button class="btn btn-danger float-right checked" onclick="editMedia(null, 'checked', 'hide')" style="display: none" >Hide Checked</button>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table class="table table-bordered" id="medias">
                                <thead class="thead-dark" style="width: 100%">
                                <th scope="col">#</th>
                                <th scope="col">File Name</th>
                                <th scope="col">Uploade Date</th>
                                <th scope="col">Uploader</th>
                                <th scope="col">Album</th>
                                <th scope="col">Action</th>
                                <th scope="col">Check</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<script>
    let checked_items = [];
    $(document).ready(function (){
        $("#medias").dataTable({
            "paging": true,
            "processing": true,
            "serverMethod": 'post',
            "ajax":{
                "url": "../ajax.php",
                "data": {
                    "key": "getMedias"
                }
            },
            columns: [
                {data: "id"},
                {data: "filename"},
                {data: "date"},
                {data: "uploader"},
                {data: "album"},
                {data: "id",
                    render: function (data){
                        return `<button type="button" class="btn btn-success"  onclick="editMedia(${data}, 'current', 'accept')" style="margin-left: 1em">Accept</button>
                <button type="button" class="btn btn-danger" onclick="editMedia(${data}, 'current', 'reject')" style="margin-left: 1em">Reject</button>
                <button type="button" class="btn btn-warning" onclick="editAlbum('#editAlbumModal',${data})"  style="margin-left: 1em">Change Album Name</button>
`
                    }

                },
                {data: "id",
                    render: function (data){
                        return `<div class="form-check"><input class="form-check-input" name="checkbox" type="checkbox" value="${data}" id="${data}"></div>`
                    }

                },
            ]


        })
    })



    $("#savebtn").on('click', function (e){
        let formData = new FormData(document.getElementById("editAlbumForm"))

        formData.append("key", "editAlbum")

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
    })


    function editMedia(id, key, operation){
        let mode = key+operation;

        if (key === "checked"){
            $.ajax({
                url: '../ajax.php',
                method: 'post',
                dataType: 'json',
                data:{
                    id: id,
                    mode: mode,
                    items: checked_items,
                    key: "editMedia"
                },
                success: function (response) {
                    console.log(response)
                }

            })
        }else{
            $.ajax({
                url: '../ajax.php',
                method: 'post',
                dataType: 'json',
                data:{
                    id: id,
                    mode: mode,
                    key: "editMedia"
                },
                success: function (response) {
                    console.log(response)
                }

            })
        }


    }


    $("#addDocument").on("submit", function (e){
        e.preventDefault();
        let length = document.getElementById("files").files.length;
        let formData = new FormData(document.getElementById("addDocument"));
        formData.append("key", "addMedias")
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


    $(document).on('click','.form-check-input', function (e){
        if (this.checked){
            checked_items.push(this.value)
            console.log(checked_items)
        }else{
            checked_items.splice(checked_items.indexOf(this.value), 1)
            console.log(checked_items)
        }

        if(document.querySelectorAll('input[name=checkbox]:checked').length > 0){
            $(".checked").show()
        }else{
            $(".checked").hide()
        }
    })


    $("#files").on("change", function (e){
        let uploaded_names = ""
        $.each(this.files, function (key, value){
            uploaded_names += `${value.name} </br>`
        })
        $("#uploaded_files").html(uploaded_names)
    })

    function editAlbum(modal, id){
        $.ajax({
            url: '../ajax.php',
            method: 'post',
            dataType: 'json',
            data:{
                key: "getCurrentMedia",
                id: id
            },
            success: function (response) {
                $("#id").val(response.data[0].id)
                $("#album").val(response.data[0].album)
            }

        })

        $(modal).modal()
    }
</script>

</body>
</html>
