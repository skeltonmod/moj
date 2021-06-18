<?php include "navbar.php";
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
}

?>

<html lang="eng">
    <head>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MUNICIPAL ORDINANCES</title>

    </head>
    <body>
    
    <div class="container">
        <div class="card" style="margin-top: 2em; margin-bottom: 2em">
            <div class="card-header">
                Ordinance Management
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="subjectTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="addTab" data-toggle="tab" role="tab" href="#add" aria-controls="add" aria-selected="true">
                            Edit Existing Ordinance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="manageTab" data-toggle="tab" href="#manage" role="tab" aria-controls="add" aria-selected="false">
                            Add New
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="scheduleTabContent">
                    <div class="tab-pane fade show active" id="add" role="tabpanel" aria-labelledby="addTab">
                        <div class="modal fade" id="ordinanceModal" tabindex="-1" role="dialog" aria-labelledby="modalScroll" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 id="headerName"> Get Ordinance </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table id="data_table" class="table table-striped table-bordered">
                                            <thead>
                                            <th>ORDINANCE ID</th>
                                            <th>YEAR</th>
                                            <th>ORDINANCE NUMBER</th>
                                            <th>TITLE</th>
                                            <th>DATE OF APPROVAL</th>
                                            <th>ATTACHED DOCUMENT</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="documentsModal" tabindex="-1" role="dialog" aria-labelledby="modalScroll" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 id="headerName"> Get Ordinance </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table id="document_table" class="table table-striped table-bordered">
                                            <thead>
                                            <th>ID</th>
                                            <th>FILENAME</th>
                                            <th>UPLOAD DATE</th>
                                            <th>UPLOADER</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <form id="editOrdinanceFrm">
                            <br>
                            <label>Ordinance Information</label>
                            <div class="form-group">
                                <input type="text" class="form-control" name="ORD_ID" id="ORD_ID" placeholder="ORDINANCE ID" readonly>
                            </div>
                            <div class="form-group">
                                <button type="button" onclick="showModal('#ordinanceModal')" class="btn btn-outline-warning btn-md btn-block"> Get ORDINANCE </button>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control editable" name="YEAR" id="YEAR" placeholder="ORDNINANCE YEAR" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="ORD_NUM" id="ORD_NUM" placeholder="Ordinance Number" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control editable" name="TITLE" id="TITLE" placeholder="Ordinance Title" readonly>
                            </div>
                            <div class="form-group">
                                <input type="date" class="form-control editable" name="DATE_APPROVED" id="DATE_APPROVED" placeholder="Date Approved" readonly>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <input type="text" class="form-control" name="FILE" id="FILE" placeholder="Attached file" readonly>
                                    </div>
                                    <div class="col">
                                        <button type="button" onclick="showModal('#documentsModal')" class="btn btn-outline-primary btn-block"> Get File from Database </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="saveData()" class="btn btn-outline-primary btn-lg btn-block"> Save Changes </button>
                        </form>
                    </div>
                    <!--    UNYA NANI -->
                    <div class="tab-pane fade" id="manage" role="tabpanel" aria-labelledby="manageTab">
                        <br>
                        <table class="table table-bordered" id="gradeTable">
                            <thead class="thead-dark" style="width: 100%">
                            <th scope="col">#</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Grade</th>
                            <th scope="col">Instructor</th>
                            <th scope="col">Subject Name</th>
                            <th scope="col">Subject Code</th>
                            </thead>
                            <tbody id="tableBody">

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>

        $('.modal').on('hide.bs.modal',function () {
            $(".table-bordered").DataTable().destroy();
        })
        function fetchOrdinance(){
            let table = $("#data_table").DataTable({
                lengthChange: true,
                "paging": true,
                "processing": true,
                "serverMethod": "post",
                "ajax": {
                    "url": "../ajax.php",
                    "data": {
                        "key": "getord"
                    }
                },
                'columnDefs':[
                    {
                        "searchable": false,
                        'targets'       : [0,3,4]
                    }
                ],
                "columns":[
                    {data: 'id'},
                    {data: 'year'},
                    {data: 'ord_num'},
                    {data: 'title'},
                    {data: 'doa'},
                    {data: 'file'},

                ],
            });

            $("#data_table tbody").on('click', 'tr', function (){
                let data = table.row(this).data()
                $("#ORD_ID").val(data.id)
                $("#YEAR").val(data.year)
                $("#ORD_NUM").val(data.ord_num)
                $("#TITLE").val(data.title)
                $("#DATE_APPROVED").val(data.doa)
                $('.editable').attr("readonly", false);
            })
        }




        function saveData(){
            let formData = new FormData(document.getElementById("editOrdinanceFrm"))

            formData.append("key", "saveOrdinance")
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

        function fetchDocuments(){
            let table = $("#document_table").DataTable({
                lengthChange: true,
                "paging": true,
                "processing": true,
                "serverMethod": "post",
                "ajax": {
                    "url": "../ajax.php",
                    "data": {
                        "key": "getDocuments"
                    }
                },
                'columnDefs':[
                    {
                        "searchable": false,
                    }
                ],
                "columns":[
                    {data: 'id'},
                    {data: 'filename'},
                    {data: 'date'},
                    {data: 'uploader'}

                ],
            })

            $("#document_table tbody").on('click', 'tr', function (){
                let data = table.row(this).data()
                $("#FILE").val(data.filename)
            })
        }

        function showModal(modal){
            switch (modal) {
                case "#ordinanceModal":
                    fetchOrdinance()
                    break;
                case "#documentsModal":
                    fetchDocuments();
                    break;
            }
            $(modal).modal()
        }
    </script>

    </body>
</html>
