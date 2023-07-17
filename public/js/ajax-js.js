$(document).ready(function () {
    //show
    function show() {
        $.ajax({
            type: "get",
            url: "{{ route('table') }}",
            success: function (response) {
                var count = 1;
                let tmp = $("#hidden_row").parent().html();
                $("#data-table").empty();

                response.forEach((element) => {
                    // console.log(element);

                    let tmpElement = $(tmp);

                    tmpElement.removeAttr("id");
                    tmpElement.removeAttr("hidden");

                    // console.log(tmpElement.children().children());

                    //show to the ID (fake)
                    tmpElement.children().eq(0).html(count);

                    // insert src to image element
                    tmpElement
                        .children()
                        .children()
                        .eq(0)
                        .attr("src", "/Users/" + element.image);

                    // insert name
                    tmpElement.children().children().eq(1).html(element.name);
                    tmpElement
                        .children()
                        .children()
                        .eq(2)
                        .attr("id", "name_" + element.id);
                    tmpElement
                        .children()
                        .children()
                        .eq(2)
                        .attr("value", element.name);

                    // insert email
                    tmpElement.children().children().eq(3).html(element.email);
                    tmpElement
                        .children()
                        .children()
                        .eq(4)
                        .attr("id", "email_" + element.id);
                    tmpElement
                        .children()
                        .children()
                        .eq(4)
                        .attr("value", element.email);

                    //button
                    tmpElement
                        .children()
                        .children()
                        .eq(5)
                        .attr("id", element.id);

                    //add it to list
                    $("#data-table").append(tmpElement).hide().fadeIn(500);

                    count++;
                });
            },
        });
    }
    show();

    //edit
    $(document).on("dblclick", ".spanEdit", function () {
        $("this").hide();
        $(this).next(".edit").removeAttr("hidden").focus();
        $(this).hide();
    });

    $(document).on("focusout", ".edit", function () {
        var id = this.id;
        var split_id = id.split("_");
        var field_name = split_id[0];
        var edit_id = split_id[1];
        var value = $(this).val();

        // Hide Input element
        $(this).attr("hidden", "hidden");

        // Hide and Change Text of the container with input elmeent
        $(this).prev(".spanEdit").show();
        $(this).prev(".spanEdit").text(value);
        var form_data = new FormData();

        form_data.append("field", field_name);
        form_data.append("id", edit_id);
        form_data.append("value", value);

        $.ajax({
            url: "{{ route('update') }}",
            type: "post",
            dataType: "text",
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                var response = JSON.parse(response);
                if (response.massage) {
                    $("#success").show();

                    $("#success").text(response.massage);
                } else {
                    console.log("Not saved.");
                }
            },
        });
    });

    //delete
    $(document).on("click", ".delete", function () {
        var tmp = this.id;
        $.ajax({
            type: "post",
            url: "{{ route('delete') }}",
            data: {
                id: tmp,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                var response = JSON.parse(response);
                if (response.massage) {
                    show();
                    $("#success").show();
                    $("#success").text(response.massage);
                }
            },
        });
    });

    //remove success massage
    $("#success").click(function () {
        $("#success").hide(1000);
    });

    //remove error massage
    $("#error").click(function () {
        $("#error").hide(1000);
    });

    $(document).on("click", "#butsave", function () {
        // disable save and file buttons
        $("#butsave").attr("disabled", "disabled");
        $("#uploadImage").attr("disabled", "disabled");

        // store my inputs in variable
        var file_data = $("#uploadImage").prop("files")[0];
        var name = $("#name").val();
        var email = $("#email").val();
        var password = $("#password").val();

        if (name != "" && email != "" && password != "" && file_data != "") {
            //creating form data
            var form_data = new FormData();

            //file format
            var ext = $("#uploadImage").val().split(".").pop().toLowerCase();
            if ($.inArray(ext, ["png", "jpg", "jpeg"]) == -1) {
                $("#error").show();
                $("#error").text("only jpg and png images allowed");
                return;
            }

            // file size
            var picsize = file_data.size;
            if (picsize > 2097152) {
                /* 2mb*/ alert("Image allowd less than 2 mb");
                return;
            }

            form_data.append("name", name);
            form_data.append("email", email);
            form_data.append("password", password);
            form_data.append("file", file_data);

            $.ajax({
                type: "post",
                url: "{{ route('store') }}",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    // var response = JSON.parse(response);
                    if ($.isEmptyObject(response.errors)) {
                        //enable that buttons

                        $("#butsave").removeAttr("disabled");
                        $("#uploadImage").removeAttr("disabled");

                        $("#fupForm").find("input").val("");
                        $("#success").show();
                        $("#success").text("Data added successfully");
                        $(document).show();
                    } else {
                        $("#error").empty();
                        $.each(response.errors, function (key, value) {
                            // enable that buttons
                            $("#butsave").removeAttr("disabled");
                            $("#uploadImage").removeAttr("disabled");

                            $("#error").show();
                            $("#error").append("<li>" + value + "</li>");
                        });
                    }
                },
            });
        } else {
            $("#error").show();
            $("#error").html("Please fill in all fields");
            $("#butsave").removeAttr("disabled");
            $("#uploadImage").removeAttr("disabled");
        }
    });
});
