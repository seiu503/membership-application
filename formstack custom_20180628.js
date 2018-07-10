var queryString = '';
/* global console */
/* global $ */
/* global document */
/* global alert */

function isValidDate(txtDate, min, max) {
  "use strict";
  var currVal = txtDate;
  if (currVal === '') {
    return false;
  }

  //Declare Regex
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?

  if (dtArray === null) {
    return false;
  }

  //Checks for mm/dd/yyyy format.
  var dtMonth = dtArray[1];
  var dtDay = dtArray[3];
  var dtYear = dtArray[5];

  if (dtYear < min || dtYear > max) {
    return false;
  }
  if (dtMonth < 1 || dtMonth > 12) {
    return false;
  }
  if (dtDay < 1 || dtDay> 31) {
    return false;
  }
  if ((dtMonth === 4 || dtMonth === 6 || dtMonth === 9 || dtMonth === 11) && dtDay === 31) {
    return false;
  }
  if (dtMonth === 2) {
    var isleap = (dtYear % 4 === 0 && (dtYear % 100 !== 0 || dtYear % 400 === 0));
    if (dtDay > 29 || (dtDay === 29 && !isleap)) {
      return false;
    }
  }
  return true;
}


function FF_OnAfterRender(){
  'use strict';
   // hide member terms div and logic to show/hide it
   $( '#GENERALTEXT437' ).hide();
   $( '#showTerms').click(function(){
      $( '#GENERALTEXT437' ).show();
   });
   $( '#hideterms').click(function(){
      $( '#GENERALTEXT437' ).hide();
   });
}

function FF_OnBeforeSave() {
  'use strict';
  var validform = 1;
  var errortext = '';

  //Check valid options
  var unit = $('select[name="unit"] option:selected' ).val();
  var agencynumber=$('select[name="agencynumber"] option:selected').val();
  console.log(`agencynumber: ${agencynumber}`);
  var fname = $( 'input[name="Contact.FirstName"]' ).val();
  var lname = $( 'input[name="Contact.LastName"]' ).val();
  var dob = $( 'input[name="Contact.Birthdate"]' ).val();
  var language = $( 'select[name="Contact.Preferred_Language__c"]' ).val();
  var rstreet = document.getElementById("Contact.MailingStreet").value;
  var rcity = $( 'input[name="Contact.MailingCity"]' ).val();
  var rstate = $( 'input[name="Contact.MailingState"]' ).val();
  var rzip = $( 'input[name="Contact.MailingPostalCode"]' ).val();
  var remail = $( 'input[name="Contact.Home_Email__c"]' ).val();
  var rmobile = $( 'input[name="Contact.MobilePhone"]' ).val();
  var termsagree = $('input[id="Contact.termsagree__c"]').val('on');
  var fullname = $( 'input[id="Contact.Signature__c"]' ).val();

    // copy home address fields over to hidden mailing address fields
    $("#messages").html("");
    $('textarea[id="Contact.OtherStreet"]').val(rstreet);
    $('input[id="Contact.OtherCity"]').val(rcity);
    $('input[id="Contact.OtherState"]').val(rstate);
    $('input[id="Contact.OtherPostalCode"]').val(rzip);

    // populate agency name from agency number to send to salesforce
    agencyname = $('select[name="agencynumber"] option:selected').text();
    $('input[id="Contact.Account_name_Pardot_sync__c"]').val(agencyname);

    // populate agencynumber for xml
    $('input[id="Contact.Formstack_Agency_Number__c"]').val(agencynumber);
    console.log('value of Contact.Formstack_Agency_Number__c input is');
    console.log($('input[id="Contact.Formstack_Agency_Number__c"]').val());

  if(!termsagree) {
    validform = 0;
    errortext += "<li>You must agree to the membership terms</li>";
    alert("You must agree to the membership terms in order to submit this form to become a member");
  }

  if(fullname <= 0 || fullname === undefined || fullname.length < 1) {
    validform = 0;
    errortext += "<li>Full name for signature invalid</li>";
  }

  if(unit <= 0 || unit === undefined || unit.length < 1) {
    validform = 0;
    errortext += "<li>Employment unit invalid</li>";
  }

  if(agencynumber <= 0 || agencynumber === undefined || agencynumber.length < 1) {
    validform = 0;
    errortext += "<li>Employment agency invalid. Please select employment unit, then select agency</li>";
  }

  if(fname < 1) {
    validform = 0;
    errortext +=  "<li>First name is invalid.</li>";
  }

  if(lname < 1) {
    validform = 0;
    errortext += "<li>Last name is invalid</li>";
  }

  if(remail.length < 1) {
    validform = 0;
    errortext += "<li>Personal email address is invalid</li>";
  }

  var validdob = isValidDate(dob, 1900, 2047);
  if(!validdob) {
    validform = 0;
    errortext += "<li>Date of birth is invalid (use mm/dd/yyyy format)</li>";
  }

  if(!language || language === undefined || language.length < 1) {
    validform = 0;
    errortext += "<li>Preferred language is invalid</li>";
  }

  if(rstreet.length < 1) {
    validform = 0;
    errortext += "<li>Street address is invalid</li>";
  }

  if(rcity.length < 1) {
    validform = 0;
    errortext += "<li>City is invalid</li>";
  }

  if(rstate.length !== 2) {
    validform = 0;
    errortext += "<li>State is invalid</li>";
  }

  if(rzip.length !== 5) {
    validform = 0;
    errortext += "<li>Zip code is invalid (5 digits only)</li>";
  }

  if(rmobile.length !== 14) {
    validform = 0;
    errortext += "<li>Phone is invalid: Use (xxx) xxx-xxxx format</li>";
  }

  if (agencynumber > 1) {
    $('select[name="agencynumber"]').val(agencynumber);
  }

  $("#messages").html("<h3>Errors with your submission:</h3><ul>" +errortext+"</ul>");

  if (validform === 0) {
    $('#btnsubmit').prop('disabled', false);
    return false;
  }

  if (validform === 1) {
    // copy home address fields over to hidden mailing address fields
    // after validation and before submit
    $("#messages").html("");
    $('textarea[id="Contact.OtherStreet"]').val(rstreet);
    $('input[id="Contact.OtherCity"]').val(rcity);
    $('input[id="Contact.OtherState"]').val(rstate);
    $('input[id="Contact.OtherPostalCode"]').val(rzip);

    // populate agency name from agency number to send to salesforce
    var agencyname = $('select[name="agencynumber"] option:selected').text();
    $('input[id="Contact.Account_name_Pardot_sync__c"]').val(agencyname);

    // populate agencynumber for xml
    $('input[id="Contact.Formstack_Agency_Number__c"]').val(agencynumber);
    console.log('value of Contact.Formstack_Agency_Number__c input is');
    console.log($('input[id="Contact.Formstack_Agency_Number__c"]').val());

    var fieldMap = {
      "Contact.Formstack_Agency_Number__c": "agencynumber",
      "Contact.FirstName": "fname",
      "Contact.LastName": "lname",
      "Contact.Birthdate": "dob",
      "Contact.Preferred_Language__c": "language",
      "Contact.MailingStreet": "rstreet",
      "Contact.MailingCity": "rcity",
      "Contact.MailingState": "rstate",
      "Contact.MailingPostalCode": "rzip",
      "Contact.Home_Email__c": "remail",
      "Contact.MobilePhone": "rmobile",
      "Contact.tdc_tsw__SMS_Opt_out__c": "sms",
      "Contact.Account_name_Pardot_sync__c": "agency_pardot",
      "Contact.OtherStreet": "mstreet",
      "Contact.OtherCity": "mcity",
      "Contact.OtherState": "mstate",
      "Contact.OtherPostalCode": "mzip",
      "Contact.Signature__c": "fullname",
      "Contact.termsagree__c": "termsagree"
    };

    // get list of visible inputs
    var inputsNodelist = $("#dvBannerHTML :input");
    var inputs = Array.from(inputsNodelist);

    // append hidden form to hold new inputs
    var $hiddenForm = $( '<form action="https://seiu503signup.org/process_test.php" method="post" name="hidden_form" id="hidden_form">');
    $('body').append( $hiddenForm );

    // for each visible input, generate a matching input with MDB fieldname and append to hidden form
    inputs.forEach(function(input) {
      var name = input.name;
      var value = input.value;
      var mappedName = fieldMap[name];
      if (mappedName) {
        var $newHidden = $( '<input id="' + mappedName + '" name="' + mappedName + '" value="' + value + '" type="hidden" />' );
        $( "#hidden_form" ).append( $newHidden );
      }
    });

    console.log($("#hidden_form"));
    console.log($("#hidden_form").html());

    // generate querystring to prepopulate fields on second page
    queryString = `https://seiu503signup.org/p2_test.html?Contact.FirstName=${fname}&Contact.LastName=${lname}&Contact.Home_Email__c=${remail}`;

    return true;
  }
}

function FF_OnAfterSave() {
  'use strict';
  console.log('onAfterSave');
  // submit MDB form only if FF passes all client-side validation
  console.log('submitting to MDB');
  console.log($("#hidden_form").html());
  $("#hidden_form").submit();

}