function FF_OnAfterRender(){
 // add logic for displaying agency / employer picklist values
 // for now just pass a fixed value for testing the form

 // hide member terms div and logic to show/hide it
 $( '#GENERALTEXT437' ).hide();
 $( '#showTerms').click(function(){
 		$( '#GENERALTEXT437' ).show();
 });
 $( '#hideTerms').click(function(){
 		$( '#GENERALTEXT437' ).hide();
 })

}

function FF_OnBeforeSave() {
	var fieldMap = {
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
		"Contact.termsagree__c": "termsagree"
	};

	// get list of visible inputs
	var inputsNodelist = $("#dvBannerHTML :input");
	var inputs = Array.from(inputsNodelist);

	// append hidden form to hold new inputs
	var $hiddenForm = $( '<form action="https://seiu503signup.org/process_test.php" method="post" name="hidden_form" id="hidden_form">');
	$('body').append( $hiddenForm );

	// for each visible input, generate a matching input with MDB fieldname and append to hidden form
	inputs.forEach((input, idx) => {
		var name = input.name;
		var value = input.value;
		var mappedName = fieldMap[name];
		if (mappedName) {
			var $newHidden = $( `<input id="${mappedName}" name="${mappedName}" value="${value}" type="hidden" />` );
			console.log($newHidden);
			$( "#hidden_form" ).append( $newHidden );
		}
	});

	// pass a fixed value for the agency number (Eastern Oregon University) until
	// that part is working
	var $fakeAgency = $( '<input name="agencynumber" id="agencynumber" value="58010" type="hidden" />');
	$("#hidden_form").append( $fakeAgency );

	console.log( $hiddenForm );

	$hiddenForm.submit();

  return true;
}