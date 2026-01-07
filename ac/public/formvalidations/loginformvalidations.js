  //*******************EXTRA VALIDATION METHODS STARTS********************//
  //maxsize
  $.validator.addMethod('maxSize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size <= param) 
  });
  //minsize
  $.validator.addMethod('minSize', function(value, element, param) { 
      return this.optional(element) || (element.files[0].size >= param) 
  });
  //alphanumeric
  $.validator.addMethod("alphnumericregex", function(value, element) {
      return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
    });
  //alphaonly
  $.validator.addMethod("onlyalphregex", function(value, element) {
  return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
  });
  //without space
  $.validator.addMethod("noSpace", function(value, element) { 
    return value.indexOf(" ") < 0 && value != ""; 
  }, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//


//*******************OTPSEND FORM VALIDATION STARTS********************//
$("#otpsend").validate({
    rules: {
                mobile: { required: true, noSpace: true, number: true, minlength: 10, maxlength: 10},
                captcha: { required: true },
            },
  messages: { 
             
                mobile: {
                      required: "Enter mobile number",
                      noSpace: "Enter mobile number without space.",
                      number: "Mobile Number should be numbers only.",
                      minlength: "Mobile Number should be 10 digits long.",
                      maxlength: "Mobile Number should be 10 digits long.",
                  },
                captcha: {
                      required: "Captcha required."
                  },
            },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = $(element).data('error');
              if (placement) {
                  $(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//********************OTPSEND FORM VALIDATION ENDS********************//


//*******************LOGIN FORM VALIDATION STARTS********************//
$("#loginval").validate({
    rules: {
                mobile: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
                otp: { required: true, number: true, noSpace: true, minlength: 6, maxlength: 6,},
                password: { required: true, noSpace: true}
            },
  messages: { 
             
                mobile_number: {
                      required: "Enter mobile number",
                      number: "Mobile Number should be numbers only.",
                      noSpace: "Enter mobile number without space.",
                      minlength: "Mobile Number should be 10 digits long.",
                      maxlength: "Mobile Number should be 10 digits long.",
                  },
                otp: {
                      required: "Enter OTP",
                      number: "OTP should be numbers only.",
                      noSpace: "Enter OTP without space.",
                      minlength: "OTP should be 6 digits long.",
                      maxlength: "OTP should be 6 digits long.",
                  },
                  password: {
                    required: "Enter Password",
                    
                    
                },
               
            },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = $(element).data('error');
              if (placement) {
                  $(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//********************LOGIN FORM VALIDATION ENDS********************//

