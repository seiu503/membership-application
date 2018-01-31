$( document ).ready(function() {

    // wait until formstack form has rendered and then append agency dropdowns
    $("#ffSection0").waitUntilExists(function() {
      // copy the html from the hidden field since i can't call php inside the formstack code...
      var agencyTypes = $("#hidden-php-vars").html();
      // insert the html wrapper first
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

      // using setTimeout bc for some reason waitUntilExists is not working for this element -- have to wait until this input renders to the DOM
      setTimeout(function(){
        // add input masks to fields that need client-side validation

        var dob = document.getElementById("Contact.Birthdate");
        var zip = document.getElementById("Contact.MailingPostalCode");
        var state = document.getElementById("Contact.MailingState");
        var cell = document.getElementById("Contact.MobilePhone");

        Inputmask("99999").mask(zip);
        Inputmask("aa").mask(state);
        Inputmask("(999) 999-9999").mask(cell);
        // Inputmask("mm/dd/yyyy").mask(dob);
        // inputmask very buggy for mm/dd/yyyy format ??
        // add placeholder instead...
        dob.placeholder = "mm/dd/yyyy";

        // wrap span around preferred language field
        // to match formatting with other selects
        var $langSelect = document.getElementById("Contact.Preferred_Language__c");
        const newSpan = document.createElement('span');
        newSpan.classList.add('ff-select-type', 'ff-singlepicklist');
        $langSelect.parentElement.insertBefore(newSpan, $langSelect);
        newSpan.appendChild($langSelect);

        // hide formstack submit button
        // $(".ff-footer-group").hide();
    }, 1000);

  });

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
