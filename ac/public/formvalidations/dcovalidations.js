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
   //numonly
  $.validator.addMethod("onlynumregex", function(value, element) {
  return this.optional(element) || /^[0-9\.\s]+$/i.test(value);
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

//*******************DCO COMPLAINT SEARCH FORM VALIDATION STARTS********************//
$("#agentcompsearch").validate({
    rules: {
                searchmobile: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
            },
  messages: { 
                searchmobile: {
                      required: "Enter mobile number",
                      number: "Mobile Number should be numbers only.",
                      noSpace: "Enter mobile number without space.",
                      minlength: "Mobile Number should be 10 digits long.",
                      maxlength: "Mobile Number should be 10 digits long.",
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
//********************DCO COMPLAINT SEARCH FORM VALIDATION ENDS********************//


//*******************DCO AGENT FORM VALIDATION STARTS********************//
  $("#dcoagentform").validate({
    rules: {
            //role: { required: true,},
            fname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
            lname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, }, 
            mobile_number: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
            email: { required: true, email: true, maxlength: 100,},
            /*state_id: { required: true,},
            district_id: { required: true,},*/
            address1: { required: true, minlength: 10, maxlength: 255,},
            address2: { maxlength: 255,},
         },
  messages: { 
                //role: { required: "Select agent type."},
                fname: {
                       required: "Enter agent fname.",
                       noSpace: "Enter agent fname without space.",
                       onlyalphregex: "Fname should only contain letters.",
                       minlength: "Minlength length of fname should be 2 characters.",
                       maxlength: "Maximum length of fname should be 50 characters.",
                  },
                lname: {
                       required: "Enter agent lname.",
                       noSpace: "Enter agent lname without space.",
                       onlyalphregex: "Lname should only contain letters.",
                       minlength: "Minlength length of lname should be 2 characters.",
                       maxlength: "Maximum length of lname should be 50 characters.",
                  },
                mobile_number: {
                      required: "Enter mobile number",
                      number: "Mobile Number should be numbers only.",
                      //onlynumregex: "Mobile Number should be numbers only.",
                      noSpace: "Enter mobile number without space.",
                      minlength: "Mobile Number should be 10 digits long.",
                      maxlength: "Mobile Number should be 10 digits long.",
                  },
                email: {
                      required: "Enter email address",
                      email: "Enter valid email address.",
                      maxlength: "Email address should be 100 digits long.",
                  },
                /*state_id: { required: "Select State."},
                district_id: { required: "Select District."}, */
                address1: {
                      required: "Enter Address1",
                      minlength: "Address1 should be 10 characters.",
                      maxlength: "Address1 should be 255 digits.",
                  },
                address2: {
                      maxlength: "Address2 should be 255 characters.",
                  },  
        },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = jQuery(element).data('error');
              if (placement) {
                  jQuery(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//*******************DCO AGENT FORM VALIDATION ENDS********************//

//*******************COMPLAINT FORM VALIDATION STARTS********************//
  $("#compform").validate({
    ignore: [],    // <-- allows validation of all hidden fields
    rules: {
            complaint_subject: { required: true, onlyalphregex: true, minlength: 10, maxlength: 100, },
            complaint_fname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
            complaint_lname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, }, 
            mobile_number: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
            email: { email: true, maxlength: 100,},
            complaint_status_id: { required: true,},
            complaint_type_id: { required: true},
            election_type: { required: true,},
            complaint_category_id: { required: true,},
            //state_id: { required: true,},
            //district_id: { required: true,},
            comp_address: { required: true, minlength: 10, maxlength: 200,},
            complaint_description: { required: true, minlength: 10, maxlength: 500,},
            complaint_level_id: { required: true,},
            complaint_readdressal_time: { required: true,},
            complaint_related_to: { required: true,},
         },
  messages: { 
              complaint_subject: {
                       required: "Enter complaint subject.",
                       onlyalphregex: "complaint subject should only contain letters.",
                       minlength: "Minlength length of complaint subject should be 10 characters.",
                       maxlength: "Maximum length of complaint subject should be 100 characters.",
                  },
              complaint_fname: {
                     required: "Enter complainent fname.",
                     noSpace: "Enter complainent fname without space.",
                     onlyalphregex: "Fname should only contain letters.",
                     minlength: "Minlength length of fname should be 2 characters.",
                     maxlength: "Maximum length of fname should be 50 characters.",
                },
              complaint_lname: {
                     required: "Enter complainent lname.",
                     noSpace: "Enter complainent lname without space.",
                     onlyalphregex: "Lname should only contain letters.",
                     minlength: "Minlength length of lname should be 2 characters.",
                     maxlength: "Maximum length of lname should be 50 characters.",
                },
              mobile_number: {
                    required: "Enter mobile number",
                    number: "Mobile Number should be numbers only.",
                    noSpace: "Enter mobile number without space.",
                    minlength: "Mobile Number should be 10 digits long.",
                    maxlength: "Mobile Number should be 10 digits long.",
                },
              email: {
                    email: "Enter valid email address.",
                    maxlength: "Email address should be 100 digits long.",
                },
              complaint_status_id: { required: "Select Complaint Status."},
              complaint_type_id: { required: "Select Complaint Type."},
              election_type: { required: "Select Election Type."},
              complaint_category_id: { required: "Select Complaint Catogory."},
              comp_address: {
                    required: "Enter Address",
                    minlength: "Address should be 10 characters.",
                    maxlength: "Address should be 200 digits.",
                }, 
              /*state_id: { required: "Select State."},
              district_id: { required: "Enter District."}, */
              complaint_description: {
                    required: "Enter Complaint Description",
                    minlength: "Complaint Description should be 10 characters.",
                    maxlength: "Complaint Description should be 500 characters."                  
                },  
              complaint_level_id: { required: "Select Complaint Level."},
              complaint_readdressal_time: { required: "Select Complaint Re-address Time."},
              complaint_related_to: { required: "Select Complaint Related To."},
        },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = jQuery(element).data('error');
              if (placement) {
                  jQuery(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//*******************COMPLAINT FORM VALIDATION ENDS********************//

//*******************QUERY FORM VALIDATION STARTS********************//
$("#queryform").validate({
    ignore: [],    // <-- allows validation of all hidden fields
    rules: {    
            complaint_subject: { required: true, onlyalphregex: true, minlength: 10, maxlength: 100, },
            complaint_fname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
            complaint_lname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
            mobile_number: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
            email: { email: true, maxlength: 100 }, 
            complaint_description: { required: true, minlength: 10, maxlength: 500,},
            complaint_related_to: { required: true,}
            },
  messages: { 
              complaint_subject: {
                       required: "Enter complaint subject.",
                       onlyalphregex: "complaint subject should only contain letters.",
                       minlength: "Minlength length of complaint subject should be 10 characters.",
                       maxlength: "Maximum length of complaint subject should be 100 characters.",
                  },
              complaint_fname: {
                     required: "Enter complainent fname.",
                     noSpace: "Enter complainent fname without space.",
                     onlyalphregex: "Fname should only contain letters.",
                     minlength: "Minlength length of fname should be 2 characters.",
                     maxlength: "Maximum length of fname should be 50 characters.",
                },
              complaint_lname: {
                     required: "Enter complainent lname.",
                     noSpace: "Enter complainent lname without space.",
                     onlyalphregex: "Lname should only contain letters.",
                     minlength: "Minlength length of lname should be 2 characters.",
                     maxlength: "Maximum length of lname should be 50 characters.",
                },
              mobile_number: {
                    required: "Enter mobile number",
                    number: "Mobile Number should be numbers only.",
                    noSpace: "Enter mobile number without space.",
                    minlength: "Mobile Number should be 10 digits long.",
                    maxlength: "Mobile Number should be 10 digits long.",
                },
               email: {
                    email: "Enter valid email address.",
                    maxlength: "Email address should be 100 digits long.",
                },
              complaint_description: {
                    required: "Enter Complaint Description.",
                    minlength: "Complaint Description should be 10 characters.",
                    maxlength: "Complaint Description should be 500 characters."
                },
              complaint_related_to: { required: "Select Complaint related to."},
               
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
//********************QUERY FORM VALIDATION ENDS********************//

//*******************INFORMATION FORM VALIDATION STARTS********************//
$("#infoform").validate({
    ignore: [],    // <-- allows validation of all hidden fields
    rules: {
            complaint_subject: { required: true, onlyalphregex: true, minlength: 10, maxlength: 100, },
            complaint_fname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
            complaint_lname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
            mobile_number: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
            email: { email: true, maxlength: 100 }, 
            complaint_description: { required: true, minlength: 10, maxlength: 500,},
            complaint_related_to: { required: true,}
            },
  messages: { 
              complaint_subject: {
                       required: "Enter complaint subject.",
                       onlyalphregex: "complaint subject should only contain letters.",
                       minlength: "Minlength length of complaint subject should be 10 characters.",
                       maxlength: "Maximum length of complaint subject should be 100 characters.",
                  },
              complaint_fname: {
                     required: "Enter complainent fname.",
                     noSpace: "Enter complainent fname without space.",
                     onlyalphregex: "Fname should only contain letters.",
                     minlength: "Minlength length of fname should be 2 characters.",
                     maxlength: "Maximum length of fname should be 50 characters.",
                },
              complaint_lname: {
                     required: "Enter complainent lname.",
                     noSpace: "Enter complainent lname without space.",
                     onlyalphregex: "Lname should only contain letters.",
                     minlength: "Minlength length of lname should be 2 characters.",
                     maxlength: "Maximum length of lname should be 50 characters.",
                },
              mobile_number: {
                    required: "Enter mobile number",
                    number: "Mobile Number should be numbers only.",
                    noSpace: "Enter  mobile number without space.",
                    minlength: "Mobile Number should be 10 digits long.",
                    maxlength: "Mobile Number should be 10 digits long.",
                },
               email: {
                    email: "Enter valid email address.",
                    maxlength: "Email address should be 100 digits long.",
                },
              complaint_description: {
                    required: "Enter Complaint Description.",
                    minlength: "Complaint Description should be 10 characters.",
                    maxlength: "Complaint Description should be 500 characters."
                },
              complaint_related_to: { required: "Select Complaint related to."},
               
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
//********************INFORMATION FORM VALIDATION ENDS********************//


//*******************FORM USER REGISTRATION BY DCO VALIDATION STARTS********************//
$("#agentuserform").validate({
    rules: {
                fname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
                lname: { required: true, noSpace: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
                mobile_number: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
            },
  messages: { 
              fname: {
                     required: "Enter complainent fname.",
                     noSpace: "Enter complainent fname without space.",
                     onlyalphregex: "Fname should only contain letters.",
                     minlength: "Minlength length of fname should be 2 characters.",
                     maxlength: "Maximum length of fname should be 50 characters.",
                },
              lname: {
                     required: "Enter complainent lname.",
                     noSpace: "Enter complainent lname without space.",
                     onlyalphregex: "Lname should only contain letters.",
                     minlength: "Minlength length of lname should be 2 characters.",
                     maxlength: "Maximum length of lname should be 50 characters.",
                },
              mobile_number: {
                    required: "Enter mobile number",
                    number: "Mobile Number should be numbers only.",
                    noSpace: "Enter mobile number without space.",
                    minlength: "Mobile Number should be 10 digits long.",
                    maxlength: "Mobile Number should be 10 digits long.",
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
//********************FORM USER REGISTRATION BY DCO VALIDATION ENDS********************//

//*******************FORM USER REGISTRATION BY DCO VALIDATION STARTS********************//
$("#agentuserverify").validate({
    rules: {
                mobile_number: { required: true, number: true, noSpace: true, minlength: 10, maxlength: 10},
                otpreg: { required: true, number: true, noSpace: true, minlength: 4, maxlength: 4,}
            },
  messages: { 
              mobile_number: {
                    required: "Enter mobile number",
                    number: "Mobile Number should be numbers only.",
                    noSpace: "Enter mobile number without space.",
                    minlength: "Mobile Number should be 10 digits long.",
                    maxlength: "Mobile Number should be 10 digits long.",
                },
              otpreg: {
                    required: "Enter OTP",
                    number: "OTP should be numbers only.",
                    noSpace: "Enter OTP without space.",
                    minlength: "OTP should be 4 digits long.",
                    maxlength: "OTP should be 4 digits long.",
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
//********************FORM USER REGISTRATION BY DCO VALIDATION ENDS********************//

//*******************COMPLAINT LOG REPLY BY DCO VALIDATION STARTS********************//
$("#dcologreplyformsave").validate({
    ignore: [],    // <-- allows validation of all hidden fields

    rules: {    
                complaint_reply_description: { required: true, minlength: 30, maxlength: 500,},
            },
  messages: { 
              complaint_reply_description: {
                    required: "Enter Reply Log Description.",
                    minlength: "Reply Log Description should be 30 characters.",
                    maxlength: "Reply Log Description should be 500 characters."
                }
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
//********************COMPLAINT LOG REPLY BY DCO VALIDATION ENDS********************//


//*******************DCO CUSTOM DATE AND STATUS FILTER VALIDATION STARTS********************//
$("#dcocustomdatefilter").validate({
    rules: {
                startDate: { required: true, date: true, dateFormat: true},
                endDate: { required: true, date: true}
            },
  messages: { 
              startDate: {
                    required: "Enter Start Date.",
                    date: "Start Date only can be date.",
                },
              endDate: {
                    required: "Enter Start Date.",
                    date: "Start Date only can be date.",
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
//********************DCO CUSTOM DATE AND STATUS FILTER VALIDATION ENDS********************//

//*******************DCO ELECTROL SEARCH BY EPIC VALIDATION STARTS********************//
$("#epicsearchform").validate({
    rules: {
                epic_no: { required: true, noSpace: true, minlength: 10, maxlength: 10, },
                search_type: { required: true, noSpace: true, onlyalphregex: true, minlength: 4, maxlength: 4, },
              },
  messages: { 
              epic_no: {
                     required: "Enter EPIC number.",
                     noSpace: "Enter EPIC without space.",
                     minlength: "Minlength length of EPIC should be 10 characters.",
                     maxlength: "Maximum length of EPIC should be 10 characters.",
                },
              search_type: {
                     required: "Enter search type.",
                     noSpace: "Enter search type without space.",
                     minlength: "Minlength length of search type should be 4 characters.",
                     maxlength: "Maximum length of search type should be 4 characters.",
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
//********************DCO ELECTROL SEARCH BY EPIC VALIDATION ENDS********************//

//*******************DCO ELECTROL SEARCH BY NAME VALIDATION STARTS********************//
$("#epicnameform").validate({
    rules: {
                name: { required: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
                rln_name: { onlyalphregex: true, minlength: 2, maxlength: 50, },
                search_type: { required: true, noSpace: true, onlyalphregex: true, minlength: 7, maxlength: 7, },
                state_name: { required: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
                district_name: { required: true, onlyalphregex: true, minlength: 2, maxlength: 50, },
              },
  messages: { 
              name: {
                     required: "Enter name.",
                     onlyalphregex: "Name should only contain letters.",
                     minlength: "Minlength length of name should be 2 characters.",
                     maxlength: "Maximum length of name should be 50 characters.",
                },
              rln_name: {
                     onlyalphregex: "Relation name should only contain letters.",
                     minlength: "Minlength length of Relation name should be 2 characters.",
                     maxlength: "Maximum length of Relation name should be 50 characters.",
                },
              search_type: {
                     required: "Enter search type.",
                     noSpace: "Enter search type without space.",
                     minlength: "Minlength length of search type should be 7 characters.",
                     maxlength: "Maximum length of search type should be 7 characters.",
                },
              state_name: {
                     required: "Enter state name.",
                     onlyalphregex: "Name should only contain letters.",
                     minlength: "Minlength length of state name should be 3 characters.",
                     maxlength: "Maximum length of state name should be 50 characters.",
                },
              district_name: {
                     required: "Enter district name.",
                     onlyalphregex: "District name should only contain letters.",
                     minlength: "Minlength length of districtname should be 3 characters.",
                     maxlength: "Maximum length of district name should be 50 characters.",
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
//********************DCO ELECTROL SEARCH BY NAME VALIDATION ENDS********************//



