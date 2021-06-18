
<!doctype html>
<?php include "navbar.php";
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
}

?>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>Office Of Sangguniang Bayan | Municipality of Jasaan</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Place favicon.ico in the root directory -->

</head>

<body>

    <div class="container">
        <div class="card" style="margin-top: 2em;">
            <div class="card-header">
                Feedbacks
                <button class="btn btn-success float-right"  onclick="editFeedback(null, 'all', 'accept')" style="margin-left: 1em">Accept All</button>

                <button class="btn btn-danger float-right" onclick="editFeedback(null, 'all', 'reject')" >Reject All</button>
                <button class="btn btn-success float-right checked"  onclick="editFeedback(null, 'checked', 'accept')" style="margin-left: 1em; margin-right: 1em; display: none">Accept Checked</button>
                <button class="btn btn-danger float-right checked" onclick="editFeedback(null, 'checked', 'reject')" style="display: none" >Reject Checked</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="feedbacks">
                    <thead class="thead-dark" style="width: 100%">
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Comment</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                    <th scope="col">Check</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>

<script>

    // Get All Feedback
    let checked_items = []

    $(document).ready(function (){
        $("#feedbacks").dataTable({
            "paging": true,
            "processing": true,
            "serverMethod": 'post',
            "ajax":{
                "url": "../ajax.php",
                "data": {
                    "key": "fetchFeedback"
                }
            },
            columns: [
                {data: "id"},
                {data: "name"},
                {data: "comment"},
                {data: "status"},
                {data: "id",
                    render: function (data){
                        return `<button type="button" class="btn btn-success"  onclick="editFeedback(${data}, 'current', 'accept')" style="margin-left: 1em">Accept</button>
                <button type="button" class="btn btn-danger" onclick="editFeedback(${data}, 'current', 'reject')" style="margin-left: 1em">Reject</button>
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

    })



    function editFeedback(id, key, operation){
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
                   key: "editFeedback"
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
                   key: "editFeedback"
               },
               success: function (response) {
                   console.log(response)
               }

           })
       }


    }

</script>



</html>
