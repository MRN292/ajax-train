<!DOCTYPE html>
<html>

<head>
    <title>Insert data in MySQL database using Ajax</title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    {{-- <script src="/js/jquery-3.6.4.min.js"></script> --}}
</head>

<body>
    <div style="margin: auto;width: 60%;">

        <div class="alert alert-success alert-dismissible" id="success" style="display:none;">
        </div>
        <div class="alert  alert-danger alert-dismissible" id="error" style="display:none;">
        </div>


        <section id="fupForm" name="form1">
            <meta name="csrf-token" content="{{ csrf_token() }}" />

            {{-- <input id="csrf_token" type="hidden" name="_token" value="{{ csrf_token() }}" /> --}}

            <div class="form-group">
                <label for="email">Name:</label>
                <input required type="text" class="form-control" id="name" placeholder="Name" name="name">
            </div>
            <div class="form-group">
                <label for="pwd">Email:</label>
                <input required type="text" class="form-control" id="email" placeholder="Email" name="email">
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input required type="Password" class="form-control" id="password" placeholder="Password"
                    name="password">
            </div>



            <div class="form-group mb-2 mt-2">
                <input required id="uploadImage" type="file" accept="image/*" name="image" class="form-control" />

            </div>



            <button id="butsave" type="submit" name="save" class="btn btn-primary">Save to database</button>
            <br>
            {{-- <a  class="btn btn-dark mt-2" href="{{route('table')}}">Show Users</a> --}}
            {{-- <button type="submit" name="save" class="btn btn-primary" value="Save to database" id="butsave"> --}}
        </section>
    </div>


    <div class="container">
        <h2 class="text-center">User List</h2>

        {{-- <a href="{{route('home')}}" class="btn btn-primary mb-1">Add User</a> --}}

        <table id="user-table" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="data-table">
                <meta name="csrf-token" content="{{ csrf_token() }}" />
                <tr id="hidden_row" hidden>
                    <td></td>

                    <td><img width='200px' class='img-thumbnail' src=''></td>

                    <td><span id="" class='spanEdit'></span><input id='name_' class='edit' type='text'
                            value='' hidden></td>

                    <td><span class='spanEdit'></span><input id='email_' class='edit' type='text' value=''
                            hidden></td>
                    <td><button id='' class='delete btn btn-danger btn-sm'>X</button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {

            let tmp = $("#hidden_row").parent().html();
            //show
            function table_show() {
                $.ajax({
                    type: "get",
                    url: "{{ route('table') }}",
                    success: function(response) {
                        var count = 1;

                        // $("#data-table").empty().not();
                        $("#data-table").find('*').not("#hidden_row").remove();
                        // console.log($("#data-table").html());

                        response.forEach(element => {
                            // console.log(element);

                            let tmpElement = $(tmp);

                            tmpElement.removeAttr('id');
                            tmpElement.removeAttr('hidden');

                            // console.log(tmpElement.children().children());

                            //show to the ID (fake)
                            tmpElement.children().eq(0).html(count);


                            // insert src to image element
                            tmpElement.children().children().eq(0).attr('src', '/Users/' +
                                element.image);



                            // insert name
                            tmpElement.children().children().eq(1).html(element.name);
                            tmpElement.children().children().eq(2).attr("id", "name_" + element
                                .id);
                            tmpElement.children().children().eq(2).attr("value", element.name);



                            // insert email
                            tmpElement.children().children().eq(3).html(element.email);
                            tmpElement.children().children().eq(4).attr("id", "email_" + element
                                .id);
                            tmpElement.children().children().eq(4).attr("value", element.email);

                            //button
                            tmpElement.children().children().eq(5).attr("id", element.id);


                            //add it to list
                            $("#data-table").append(tmpElement).hide().fadeIn(500);

                            count++;

                        });
                    }
                });
            }
            table_show();



            //edit
            $(document).on('dblclick', '.spanEdit', function() {
                $(this).next('.edit').removeAttr('hidden').focus();
                $(this).hide();
            });

            $(document).on('focusout', '.edit', function() {
                $(this).prev('.spanEdit').show();
                $(this).val($(this).prev('.spanEdit').html());
                $(this).attr("hidden", "hidden");
            });
            $(document).on('keypress', '.edit', function(e) {
                if (e.which == 27) {
                    $(this).prev('.spanEdit').show();
                    $(this).val($(this).prev('.spanEdit').html());
                    $(this).attr("hidden", "hidden");
                }
            });

            $(document).on('keypress', '.edit', function(e) {
                if (e.which == 13) {
                    var id = this.id;
                    var split_id = id.split("_");
                    var field_name = split_id[0];
                    var edit_id = split_id[1];
                    var value = $(this).val();

                    // Hide Input element


                    // Hide and Change Text of the container with input elmeent

                    var form_data = new FormData();

                    form_data.append("field", field_name);
                    form_data.append("id", edit_id);
                    form_data.append("value", value);

                    $.ajax({
                        url: "{{ route('update') }}",
                        type: 'post',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {

                            if ($.isEmptyObject(response.errors)) {
                                $("#" + id).prev('.spanEdit').show();
                                $("#" + id).prev('.spanEdit').text(value);
                                $("#" + id).attr("hidden", "hidden");
                                $('#error').hide(1000);

                                $("#success").show();
                                $('#success').text("Data edited successfully").hide().fadeIn(
                                    300);

                            } else {
                                // console.log(response.errors);
                                $("#error").empty();
                                for (let x of response.errors) {
                                    $("#butsave").removeAttr("disabled");
                                    $("#uploadImage").removeAttr("disabled");
                                    $('#success').hide(1000);

                                    $("#error").show();
                                    $("#error").append('<li>' +
                                        x + '</li>').hide().fadeIn(300);
                                }
                            }
                        }
                    });
                }

            });


            //delete
            $(document).on(
                'click', '.delete',
                function() {
                    var tmp = this.id;
                    $.ajax({
                        type: "post",
                        url: "{{ route('delete') }}",
                        data: {
                            'id': tmp
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                $("#success").show();
                                $('#success').text("Data deleted successfully");
                                table_show();


                            }
                        }
                    });
                }
            )

            //remove success massage
            $('#success').click(function() {
                $('#success').hide(1000);
            });

            //remove error massage
            $('#error').click(function() {
                $('#error').hide(1000);
            });


            // create user
            $(document).on('click', '#butsave', function() {

                // disable save and file buttons
                $("#butsave").attr("disabled", "disabled");
                $("#uploadImage").attr("disabled", "disabled");




                // store my inputs in variable
                var file_data = $("#uploadImage").prop('files')[0];
                var name = $('#name').val();
                var email = $('#email').val();
                var password = $('#password').val();


                if (name != "" && email != "" && password != "" && file_data != "") {

                    //creating form data 
                    var form_data = new FormData();


                    //file format
                    var ext = $("#uploadImage").val().split('.').pop().toLowerCase();
                    if ($.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
                        $("#error").show();
                        $('#error').text("only jpg and png images allowed");
                        return;
                    }

                    // file size
                    var picsize = (file_data.size);
                    if (picsize > 2097152) /* 2mb*/ {
                        alert("Image allowd less than 2 mb")
                        return;
                    }

                    form_data.append('name', name);
                    form_data.append('email', email);
                    form_data.append('password', password);
                    form_data.append('file', file_data);



                    $.ajax({
                        type: "post",
                        url: "{{ route('store') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: form_data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            // var response = JSON.parse(response);
                            if ($.isEmptyObject(response.errors)) {
                                //enable that buttons

                                $("#butsave").removeAttr("disabled");
                                $("#uploadImage").removeAttr("disabled");

                                $('#fupForm').find('input').val('');
                                $('#error').hide(1000);

                                $("#success").show();
                                $('#success').text("Data added successfully").hide().fadeIn(
                                    300);
                                table_show();

                            } else {
                                $("#error").empty();
                                for (let x of response.errors) {
                                    $("#butsave").removeAttr("disabled");
                                    $("#uploadImage").removeAttr("disabled");
                                    $('#success`').hide(1000);

                                    $("#error").show();
                                    $("#error").append('<li>' +
                                        x + '</li>').hide().fadeIn(300);
                                }
                            }

                        }

                    });







                } else {
                    $("#error").show();
                    $("#error").html("Please fill in all fields");
                    $("#butsave").removeAttr("disabled");
                    $("#uploadImage").removeAttr("disabled");
                }




            });
        });
    </script>
</body>

</html>
