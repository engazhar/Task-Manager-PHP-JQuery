// Login AjaX Script
$(document).ready(function () {
    $("#login").submit(function (event) {
        event.preventDefault();
        var formData = {
            user_email : $("#user_email").val(),
            user_password : $("#user_password").val(),
            action : $('#login').attr('action')
        };
        $.ajax({
            type: "POST",
            url: "task.php",
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function (data) {
            if(data['error'] == false){
                location.reload();
            }else{
                for (let key in data) {
                    console.log("key " + key + " has value " + data[key]);
                    if(key == 'notify'){
                        console.log(data['notfiy'])
                        $('#res-head').html("  <span style='color:red;'>Error</span>");
                        $('#res-bdy').html("  <span style='color:red;'>"+data['notify']+"</span>");
                        $("#notifyToast").toast("show");
                    }else{
                        var error_ar = data['data'];
                        for (let input in error_ar){
                            console.log(error_ar)
                            $('#'+input).addClass('error_input');
                            $('#'+input).attr('placeholder', error_ar[input]); 
                        }
                    }
                    
                }  
            }
            
        });
    });
});