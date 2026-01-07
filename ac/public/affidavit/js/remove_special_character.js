function blockSpecialChar_name(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || k  == 46);
}

function blockSpecialChar(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}

function blockSpecialChar_address(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || k == 44 || k == 45 || k == 46 || k == 47 || (k >= 48 && k <= 57));
}

jQuery('.removespecialcharacter').keyup(function () { 
  this.value = this.value.replace(/[^0-9\.]/g,'');
});

jQuery('.alphanumeric').keyup(function () { 
  this.value = this.value.replace(/[^a-zA-Z0-9\.]/g,'');
});

$('input#file').bind('change', function() {
  var maxSizeKB = 50; //Size in KB
  var maxSize = maxSizeKB * 1024; //File size is returned in Bytes
  if (this.files[0].size > maxSize) {
    $(this).val("");
    swal({
          title: 'Max size exceeded',
          text: "you can't upload more than 50 kb attach file",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, i got it!'
        });
    return false;
  }
});