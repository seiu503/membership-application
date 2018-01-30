$( document ).ready(function() {

    // wait until formstack form has rendered and then append agency dropdowns
    $("#ffSection0").waitUntilExists(function() {
      // copy the html from the hidden field since i can't call php inside the formstack code...
      var agencyTypes = $("#hidden-php-vars").html();
      // insert the rest of the html first
      var $employmentInfo = $(
        `<div class="ff-item-row">
          <div class="ff-col-1 ff-label-col">
            <label class="ff-label">Employment group</label>
            <span class="requiredSpan ff-required-mark">*</span>
          </div>
          <div class="ff-col-2 ff-field-col">
            <span class="ff-select-type ff-singlepicklist" id="agencyTypesInsert">
            ${agencyTypes}</span>
          </div>
        </div>
        <div class="ff-item-row" id="agencylist">
          <div class="ff-col-1 ff-label-col" id="agency-list-label">
            <label class="ff-label">Employer</label>
            <span class="requiredSpan ff-required-mark">*</span>
          </div>
          <div class="ff-col-2 ff-field-col" id="agency-list-placeholder">
            <span class="ff-select-type ff-singlepicklist" id="agencyplaceholder-wrap">
              <select name="agencyplaceholder" id="agencyplaceholder" class="inner-select" required disabled>
                <option value="">Select an employment group first...</option>
              </select>
            </span>
          </div>
        </div>`);

      $employmentInfo.prependTo( "#ffSection0" );
      // then insert the dynamically-generated select values
      $('#agencyTypesInsert').html(agencyTypes);
      // then hide the placeholder element
      $("#hidden-php-vars").hide();

      // using setTimeout bc for some reason waitUntilExists is not working for this element -- have to wait until this input renders to the DOM
      setTimeout(function(){
        // wrap span around preferred language select
        var $langSelect = document.getElementById("Contact.Preferred_Language__c");
        const newSpan = document.createElement('span');
        newSpan.classList.add('ff-select-type', 'ff-singlepicklist');
        $langSelect.parentElement.insertBefore(newSpan, $langSelect);
        newSpan.appendChild($langSelect);

        // hide formstack submit button
        $(".ff-footer-group").hide();
    }, 1000);

  });



    // input mask
    $('#Contact.Birthdate').inputmask('mm/dd/yyyy', { showMaskOnFocus: true, showMaskOnHover: false });
    $('#Contact.MailingPostalCode').inputmask('99999', { showMaskOnFocus: false, showMaskOnHover: false });
    $('#Contact.MailingState').inputmask('aa', { showMaskOnFocus: false, showMaskOnHover: false });
    $('#Contact.MobilePhone').inputmask('(999) 999-9999', { showMaskOnFocus: false, showMaskOnHover: false, "oncomplete": function(){ $(".phone").show(); document.getElementById("Contact.tdc_tsw__SMS_Opt_out__c").checked = false; } });

    // keyboard accessibility
    $('#termsdiv').keypress(function (e) {
      var key = e.which;
      if (key == 13) { //enter
          $('input[name = termsagree]').click();
          return false;
        }
      });
    $('#smsdiv').keypress(function (e) {
      var key = e.which;
      if (key == 13) { //enter
          $('input[name = Contact.tdc_tsw__SMS_Opt_out__c]').click();
          return false;
        }
      });

    $( "#submit" ).on('click', function( event ) {
      // all the client-side  validation is now being handled by formstack so this can all go away for now...
      var validform = 1;
      var errortext = '';

      // //Check valid options
      // var unit = $('select[name="unit"] option:selected' ).val();
      // var agencynumber=$('select[name="agencynumber"] option:selected').val();
      // var fname = $( 'input[type="text"][name="Contact.FirstName"]' ).val();
      // var lname = $( 'input[type="text"][name="Contact.LastName"]' ).val();
      // var dob = $( 'input[type="text"][name="Contact.Birthdate"]' ).val();
      // var sel = document.getElementById("Contact.Preferred_Language__c");
      // var language = sel.options[sel.selectedIndex].text;
      // // var language = $('#Contact.Preferred_Language__c option:selected').val();
      // console.log(`signup_script.js > 39: language: ${language}`); // language
      // var rstreet = $( 'input[type="text"][name="Contact.MailingStreet"]' ).val();
      // var rcity = $( 'input[type="text"][name="Contact.MailingCity"]' ).val();
      // var rstate = $( 'select[name="Contact.MailingState"]' ).val();
      // var rzip = $( 'input[type="text"][name="Contact.MailingPostalCode"]' ).val();
      // var remail = $( 'input[type="text"][name="Contact.Home_Email__c"]' ).val();
      // var rmobile = $( 'input[type="text"][name="Contact.MobilePhone"]' ).val();
      // var termsagree = $('input[type="checkbox"][name="termsagree"]').prop('checked');
      // var fullname = $( 'input[type="text"][name="fullname"]' ).val();

      // if(!termsagree) {
      //   validform = 0;
      //   errortext += "<li>You must agree to the membership terms</li>";
      //   alert("You must agree to the membership terms in order to submit this form to become a member");
      // }

      // if(fullname <= 0 || (typeof fullname == 'undefined') || fullname.length < 1) {
      //   validform = 0;
      //   errortext += "<li>Full name for signature invalid</li>";
      // }

      // if(unit <= 0 || (typeof unit == 'undefined') || unit.length < 1) {
      //   validform = 0;
      //   errortext += "<li>Employment unit invalid</li>";
      // }

      // if(agencynumber <= 0 || (typeof agencynumber == 'undefined') || agencynumber.length < 1) {
      //   validform = 0;
      //   errortext += "<li>Employment agency invalid. Please select employment unit, then select agency</li>";
      // }

      // if(fname < 1) {
      //   validform = 0;
      //   errortext +=  "<li>First name is invalid.</li>";
      // }

      // if(lname < 1) {
      //   validform = 0;
      //   errortext += "<li>Last name is invalid</li>";
      // }

      // if(remail.length < 1) {
      //   validform = 0;
      //   errortext += "<li>Personal email address is invalid</li>";
      // }

      // var validdob = isValidDate(dob, 1900, 2047);
      // if(!validdob) {
      //   validform = 0;
      //   errortext += "<li>Date of birth is invalid</li>";
      // }

      // if(!language || (typeof language == 'undefined') || language.length < 1) {
      //   validform = 0;
      //   errortext += "<li>Preferred language is invalid</li>";
      // }

      // if(rstreet.length < 1) {
      //   validform = 0;
      //   errortext += "<li>Street address is invalid</li>";
      // }

      // if(rcity.length < 1) {
      //   validform = 0;
      //   errortext += "<li>City is invalid</li>";
      // }

      // if(rstate.length != 2) {
      //   validform = 0;
      //   errortext += "<li>State is invalid</li>";
      // }

      // if(rzip.length != 5) {
      //   validform = 0;
      //   errortext += "<li>Zip code is invalid (5 digits only)</li>";
      // }

      // if(rmobile.length != 14) {
      //   validform = 0;
      //   errortext += "<li>Phone is invalid: Use (xxx) xxx-xxxx format</li>";
      // }

      if (validform == 1) {
        event.preventDefault();

        // copy home address fields over to hidden mailing address fields
        // after validation and before submit

        $('#Contact.OtherStreet').val($("#Contact.MailingStreet").val());
        $('#Contact.OtherCity').val($("#Contact.MailingCity").val());
        $('#Contact.OtherState').val($("#Contact.MailingState").val());
        $('#Contact.OtherPostalCode').val($("#Contact.MailingPostalCode").val());

        // populate agency name from agency number to send to salesforce
        var agencyname = $('select[name="agencynumber"] option:selected').text();
        $('#Contact.Account_name_Pardot_sync__c').val(agencyname);

        // generate full set of hidden fields with matching field names to send to (this happens in the js appended to formstack -- could move it here inside the 'waituntilexists function')

        // this submits both the primary (SF) and hidden (MDB) form
        $("form").submit();
        return;
      }

      if (unit >= 1) {
        showAgencies();
      }

      if (agencynumber > 1) {
        $('select[name="agencynumber"]').val(agencynumber);
      }

      $("#messages").html("<h3>Errors with your submission:</h3><ul>" +errortext+"</ul>");
      event.preventDefault();
        //Need to reload agencies as they are dynamically generated
    });
});

function getElementByClass(classname){
 var inc=0;
 var alltags=document.all? document.all : document.getElementsByTagName("*");
 for (i=0; i<alltags.length; i++){
   if (alltags[i].className==classname)
     customcollection[inc++]=alltags[i];
 }
 return customcollection;
}

function isValidDate(txtDate, min, max) {
  var currVal = txtDate;
  if (currVal == '') return false;

  //Declare Regex
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?

  if (dtArray == null) return false;

  //Checks for mm/dd/yyyy format.
  dtMonth = dtArray[1];
  dtDay= dtArray[3];
  dtYear = dtArray[5];

  if (dtYear < min || dtYear > max) return false;
  if (dtMonth < 1 || dtMonth > 12) return false;
  if (dtDay < 1 || dtDay> 31) return false;
  if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
    return false;
  if (dtMonth == 2) {
    var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
    if (dtDay> 29 || (dtDay ==29 && !isleap)) return false;
  }
  return true;
}

function showThis(theclassname) {
  var allElems = document.getElementsByTagName('*');
  for (var i = 0; i < allElems.length; i++) {
    var thisElem = allElems[i];
    if (thisElem.className && thisElem.className == theclassname) {
      thisElem.style.display = 'block';
    }
  }
}

function hideThis(theclassname) {
  var allElems = document.getElementsByTagName('*');
  for (var i = 0; i < allElems.length; i++) {
    var thisElem = allElems[i];
    if (thisElem.className && thisElem.className == theclassname) {
      thisElem.style.display = 'none';
    }
  }
}

var termsshown = 0;
function terms() {
  if(termsshown != 1) {
    showThis('memberterms');
    termsshown = 1;
  } else {
    hideThis('memberterms');
    termsshown = 0;
  }
}

var textshown = 0;
function phonetext() {
  var textBox = document.getElementById("mobile");
  var textLength = textBox.value.length;
  if(textLength > 0) {
    showThis('phone');
    textshown = 1;
  } else {
    hideThis('phone');
    textshown = 0;
  }
}

function showFields() {
  showThis('optional');
  hideThis('showfields');
}

function hideFields() {
  hideThis('optional');
  showThis('showfields');
}
