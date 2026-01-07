//****** Start GET All AC In  a State *****

 function getAllACList(sid){
  var e_type = $("input[name='election_type']:checked").val();
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/getaclistbysid/"+sid+"/"+e_type,
                  data: {  _token: '{{csrf_token()}}' },
       success: function (data) {
         //do something
          //console.log(data);
          jQuery('#assem_name').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
}
function chengestatus(n) { 
      if(confirm('Are you change status this Candidate?')) {
      var varr = n.split('-');
      var val = varr[0]; 
      var val1 = varr[1];  
      //alert(n);alert(val); alert(val1);
        window.location.href =APP_URL+"/ro/change-status/"+val1+"/"+val;
       }
      }
    function checkedvip(n) { 
      confirm('Are you sure remove as vip?'); 
      var val = n.slice(0, -1); 
      var val1 = n.slice(-1); 
      var val2 = val.slice(-1); 
     // alert(val1); alert(val2);
      nam="cand_id"+val2;  
      var hiddenFieldID = "input[id$=" + nam + "]";
      var requiredVal= $(hiddenFieldID).val();   
        jQuery.ajax({   
                  type:'GET',
                  url: APP_URL+"/ro/marks-vip/"+requiredVal+"/"+val1,
                  data: {  _token: '{{csrf_token()}}' },
       success: function (data) {
         //do something
        // console.log(data);
        jQuery('#m_vip').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
      }
  //****** Start GET All  election List In  a State *****

 function getAllelectionList(acno){
  var e_type = $("input[name='election_type']:checked").val();
  var st_code = $('#states').val(); //$("input[name='states']:selected").val();
  // alert(e_type);alert(st_code);alert(acno); 
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/getelection/"+st_code+"/"+acno+"/"+e_type,
                  data: { _token: '{{csrf_token1()}}' },
       success: function (data) {
         //do something
         //alert("hello");
         //console.log(data);
         jQuery('#election').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something
         
       }
           });

}
//****** END GET All  election List In  a State *****
//****** Start GET All  election List In  a State *****

 function allelectionlist(st_code){
  var  a = $("input[name='const_type']:checked").val();
    
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/getstelection/"+st_code+"/"+a,
                  data: { _token: '{{csrf_token1()}}' },
       success: function (data) {
         //do something
         //alert("hello");
         //console.log(data);
         jQuery('#election').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something
         
       }
           });

}
//****** END GET All  election List In  a State *****
//****** Referesh Caopcha*****
 
 function refereshcaptcha(sid){
  
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/refresh_captcha",
                  data: {  _token: '{{csrf_token()}}' },
       success: function (data) {
          
         jQuery("#captcha").html(data.captcha);
       },
       error: function (data, textStatus, errorThrown) {
             //do something

       }
           });
}
//****** END GET All AC In  a State *****
function showrecords(val)
    {
     var const_type = $("input[name='const_type']:checked").val();
    var st_code = $('#states').val();  
    
    jQuery.ajax({
                  type:'GET',
                  url: APP_URL+"/displayectiondetails/"+st_code+"/"+const_type+"/"+val,
                  data: { _token: '{{csrf_token1()}}' },
       success: function (data) {
         //do something
         //alert("hello");
         //console.log(data);
         jQuery('#showrecords').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something
         
       }
           });
    }

function filterdata(val)
    {   
      jQuery.noConflict();
      var cand_status = $('#cand_status').val();  
      var constituency = $('#constituency').val();  
      var search = $('#search').val();
      if(cand_status=='') cand_status='null'; if(constituency=='') constituency='null'; if(search=='') search='null'; 
      jQuery.ajax({  
                  type:'GET',
                  url: APP_URL+"/ceo/showdashboard/"+cand_status+"/"+constituency+"/"+search,
                  data: { _token: '{{csrf_token()}}' },
       success: function (data) {
         //do something
        // alert("hello");
         console.log(data);
         jQuery('#oneTimetab').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something
         
       }
           });
    }

// ######################### Mayank Added JS For Deo Search ##############################

function filterdataatdistrict(val)
    {   
      jQuery.noConflict();
      var cand_status = $('#cand_status').val();  
      var constituency = $('#constituency').val();  
      var search = $('#search').val();
      if(cand_status=='') cand_status='null'; if(constituency=='') constituency='null'; if(search=='') search='null'; 
      jQuery.ajax({  
                  type:'GET',
                  url: APP_URL+"/deo/showdashboard/"+cand_status+"/"+constituency+"/"+search,
                  data: { _token: '{{csrf_token()}}' },
       success: function (data) {
         //do something
        // alert("hello");
         console.log(data);
         jQuery('#oneTimetab').html(data);
       },
       error: function (data, textStatus, errorThrown) {
             //do something
         
       }
           });
    }