function isValidDate(txtDate, min, max) {
  var currVal = txtDate;
  if (currVal === '') return false;

  //Declare Regex
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?

  if (dtArray === null) return false;

  //Checks for mm/dd/yyyy format.
  var dtMonth = dtArray[1];
  var dtDay= dtArray[3];
  var dtYear = dtArray[5];

  if (dtYear < min || dtYear > max) return false;
  if (dtMonth < 1 || dtMonth > 12) return false;
  if (dtDay < 1 || dtDay> 31) return false;
  if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
    return false;
  if (dtMonth == 2) {
    var isleap = (dtYear % 4 === 0 && (dtYear % 100 !== 0 || dtYear % 400 === 0));
    if (dtDay> 29 || (dtDay ==29 && !isleap)) return false;
  }
  return true;
}


function FF_OnAfterRender(){
	console.log(`referrer = ${document.referrer}`);

}

function FF_OnBeforeSave() {

  console.log('submitclick');
  var validform = 1;
  var errortext = '';

  //Check valid options
  var lname = $( 'input[name="Contact.LastName"]' ).val();
  var doh = $( 'input[name="Contact.Hire_Date__c"]' ).val();
  var rzip = $( 'input[name="Contact.OtherPostalCode"]' ).val();
  var remail = $( 'input[name="Contact.Home_Email__c"]' ).val();
  var wph = $( 'input[name="Contact.Contact.Work_Phone__c"]' ).val();

  if(lname < 1) {
    validform = 0;
    errortext += "<li>Last name is invalid</li>";
  }

  if(remail.length < 1) {
    validform = 0;
    errortext += "<li>Personal email address is invalid</li>";
  }

  var validdoh = isValidDate(doh, 1900, 2047);
  if(doh.length > 1 && !validdoh) {
    validform = 0;
    errortext += "<li>Hire date is invalid (use mm/dd/yyyy format)</li>";
  }

  if(rzip.length > 1 && rzip.length != 5) {
    validform = 0;
    errortext += "<li>Zip code is invalid (5 digits only)</li>";
  }

  if(wph !== undefined && wph.length > 1 && wph.length != 14) {
    validform = 0;
    errortext += "<li>Work phone is invalid: Use (xxx) xxx-xxxx format</li>";
  }


  $("#messages").html("<h3>Errors with your submission:</h3><ul>" +errortext+"</ul>");

  if (validform === 0) {
  	$('#btnsubmit').prop('disabled', false);
    return false;
  }

  if (validform === 1) {

    $("#messages").html("");

    return true;
  }
}

function FF_OnAfterSave() {

	$("#spinner").show();

}
