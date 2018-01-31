$( document ).ready(function() {

    // wait until formstack form has rendered and then append agency dropdowns
    $("#ffSection0").waitUntilExists(function() {

      // using setTimeout bc for some reason waitUntilExists is not working for this element -- have to wait until this input renders to the DOM
      setTimeout(function(){
        // add input masks to fields that need client-side validation

        var doh = document.getElementById("Contact.Hire_Date__c");
        var zip = document.getElementById("Contact.OtherPostalCode");
        var state = document.getElementById("Contact.OtherState");
        var wph = document.getElementById("Contact.Work_Phone__c");

        Inputmask("99999").mask(zip);
        Inputmask("aa").mask(state);
        Inputmask("(999) 999-9999").mask(wph);
        // Inputmask("mm/dd/yyyy").mask(doh);
        // inputmask very buggy for mm/dd/yyyy format ??
        // add placeholder instead...
        doh.placeholder = "mm/dd/yyyy";

        // wrap span around gender and pronouns selects to fix formatting
        var $genderSelect = document.getElementById("Contact.Gender__c");
        var $pronounSelect = document.getElementById("Contact.Prounoun__c");
        var span1 = document.createElement('span');
        span1.classList.add('ff-select-type', 'ff-singlepicklist');
        $genderSelect.parentElement.insertBefore(span1, $genderSelect);
        span1.appendChild($genderSelect);
        var span2 = document.createElement('span');
        span2.classList.add('ff-select-type', 'ff-singlepicklist');
        $pronounSelect.parentElement.insertBefore(span2, $pronounSelect);
        span2.appendChild($pronounSelect);

    }, 1000);

  });

    // keyboard accessibility

});
